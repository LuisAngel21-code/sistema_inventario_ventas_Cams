<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function create(Request $request)
    {
        $usuario = $request->user();
        $sucursalId = $usuario->sucursal_id;

        $productos = Producto::where('activo', true)
            ->with(['stocks' => fn ($consulta) => $consulta->where('sucursal_id', $sucursalId)])
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) use ($sucursalId) {
                $producto->stock_sucursal = $sucursalId
                    ? (int) ($producto->stocks->first()->cantidad ?? 0)
                    : (int) $producto->stock;

                return $producto;
            })
            ->when($sucursalId, fn ($lista) => $lista->filter(
                fn ($producto) => $producto->stock_sucursal > 0
            ))
            ->values();

        return view('ventas.create', [
            'productos' => $productos,
            'sucursalId' => $sucursalId,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'tipo_pago' => ['nullable', 'string', 'max:20'],
            'comprobante_tipo' => ['nullable', 'string', 'max:20'],
            'cliente_nombre' => ['nullable', 'string', 'max:100'],
            'vendedor_nombre' => ['nullable', 'string', 'max:100'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($datos, $request) {
            $totalVenta = 0;

            foreach ($datos['items'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $stock = Stock::where('producto_id', $producto->id)
                    ->where('sucursal_id', $datos['sucursal_id'])
                    ->first();

                $stockDisponible = $stock ? $stock->cantidad : 0;

                if ($item['cantidad'] > $stockDisponible) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$producto->nombre}. Disponible: {$stockDisponible}",
                    ]);
                }

                $totalVenta += $producto->precio_base * $item['cantidad'];
            }

            $venta = Venta::create([
                'sucursal_id' => $datos['sucursal_id'],
                'usuario_id' => $request->user()->id,
                'subtotal' => $totalVenta,
                'descuento' => $datos['descuento'] ?? 0,
                'total' => $totalVenta - ($datos['descuento'] ?? 0),
                'tipo_pago' => $datos['tipo_pago'] ?? 'efectivo',
                'comprobante_tipo' => $datos['comprobante_tipo'] ?? 'boleta',
                'cliente_nombre' => $datos['cliente_nombre'] ?? null,
                'vendedor_nombre' => $datos['vendedor_nombre'] ?? null,
                'estado' => 'completada',
            ]);

            foreach ($datos['items'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio_base,
                ]);

                Stock::where('producto_id', $producto->id)
                    ->where('sucursal_id', $datos['sucursal_id'])
                    ->decrement('cantidad', $item['cantidad']);

                $producto->decrement('stock', $item['cantidad']);

                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $datos['sucursal_id'],
                    'tipo' => Movimiento::TIPO_SALIDA,
                    'cantidad' => $item['cantidad'],
                    'motivo' => 'Venta #' . $venta->id,
                    'usuario_id' => $request->user()->id,
                ]);
            }
        });

        return redirect()->route('ventas.historial')
            ->with('status', 'Venta registrada y stock actualizado.');
    }

    public function historial(Request $request)
    {
        $usuario = $request->user();

        $ventas = Venta::with(['sucursal', 'usuario', 'detalleVentas.producto'])
            ->when(!$usuario->esAdmin(), fn ($q) => $q->where('sucursal_id', $usuario->sucursal_id))
            ->when($request->get('fecha_inicio') && $request->get('fecha_fin'),
                fn ($q) => $q->filtradasPorRango($request->get('fecha_inicio'), $request->get('fecha_fin')))
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('ventas.historial', compact('ventas'));
    }

    public function detalle(Venta $venta)
    {
        $venta->load(['detalleVentas.producto', 'sucursal', 'usuario']);

        return view('ventas.detalle', compact('venta'));
    }
}