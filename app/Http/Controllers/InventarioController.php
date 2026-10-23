<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    // HU-06: muestra el stock disponible de los productos, con filtro por categoría y sucursal.
    public function index(Request $request)
    {
        $usuario = $request->user();

        $stocks = Stock::with(['producto', 'sucursal'])
            ->when(!$usuario->esAdmin(), fn ($q) => $q->where('sucursal_id', $usuario->sucursal_id))
            ->when($request->get('sucursal_id'), fn ($q, $sucursalId) => $q->where('sucursal_id', $sucursalId))
            ->when($request->get('categoria'), function ($q, $categoria) {
                $q->whereHas('producto', fn ($producto) => $producto->where('categoria', $categoria));
            })
            ->get();

        return view('inventario.index', [
            'stocks' => $stocks,
            'categorias' => Producto::distinct()->orderBy('categoria')->pluck('categoria'),
            'sucursales' => Sucursal::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }
    public function movimientos(Request $request)
    {
        $usuario = $request->user();

        $movimientos = Movimiento::with(['producto', 'sucursal'])
            ->when(!$usuario->esAdmin(), fn ($q) => $q->where('sucursal_id', $usuario->sucursal_id))
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('inventario.movimientos', compact('movimientos'));
    }

    public function mostrarRegistroIngreso()
    {
        return view('inventario.ingreso', [
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function registrarIngreso(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => ['required', 'integer', 'exists:productos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'motivo' => ['nullable', 'string', 'max:200'],
        ]);

        DB::transaction(function () use ($datos, $request) {
            $stock = Stock::firstOrCreate([
                'producto_id' => $datos['producto_id'],
                'sucursal_id' => $datos['sucursal_id'],
            ], ['cantidad' => 0]);

            $stock->incrementar($datos['cantidad']);

            $producto = Producto::find($datos['producto_id']);
            $producto->increment('stock', $datos['cantidad']);

            $compra = Compra::create([
                'sucursal_id' => $datos['sucursal_id'],
                'usuario_id' => $request->user()->id,
                'total' => $producto->costo * $datos['cantidad'],
            ]);

            DetalleCompra::create([
                'compra_id' => $compra->id,
                'producto_id' => $producto->id,
                'cantidad' => $datos['cantidad'],
                'costo' => $producto->costo,
            ]);

            Movimiento::create([
                'producto_id' => $producto->id,
                'sucursal_id' => $datos['sucursal_id'],
                'tipo' => Movimiento::TIPO_ENTRADA,
                'cantidad' => $datos['cantidad'],
                'motivo' => $datos['motivo'] ?? null,
                'usuario_id' => $request->user()->id,
            ]);
        });

        return redirect()->route('inventario.movimientos')
            ->with('status', 'Ingreso de mercaderia registrado y stock incrementado.');
    }
}