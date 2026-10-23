<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    // HU-04: muestra el catálogo con los productos registrados.
    public function index(Request $request)
    {
        $productos = Producto::query()
            ->when($request->get('categoria'), fn ($q, $c) => $q->where('categoria', $c))
            ->when($request->get('busqueda'), fn ($q, $b) => $q->where('nombre', 'like', "%{$b}%"))
            ->orderBy('nombre')
            ->paginate(12);

        return view('productos.index', [
            'productos' => $productos,
            'categorias' => Producto::distinct()->pluck('categoria'),
        ]);
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        // HU-04: se piden los datos obligatorios del producto antes de guardarlo.
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:productos,codigo'],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'categoria' => ['required', 'string', 'max:50'],
            'marca' => ['nullable', 'string', 'max:50'],
            'proveedor' => ['nullable', 'string', 'max:100'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'costo' => ['required', 'numeric', 'min:0'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($datos);

        return redirect()->route('productos.index')
            ->with('status', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'categoria' => ['required', 'string', 'max:50'],
            'marca' => ['nullable', 'string', 'max:50'],
            'proveedor' => ['nullable', 'string', 'max:100'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'costo' => ['required', 'numeric', 'min:0'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($datos);

        return redirect()->route('productos.index')
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function actualizarStock(Request $request, Producto $producto)
    {
        // HU-05: la cantidad debe ser un número válido mayor o igual a cero.
        $datos = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        // HU-05: al guardar, el nuevo valor ya se ve en el inventario.
        $producto->actualizarStock($datos['stock']);

        return redirect()->route('productos.index')
            ->with('status', 'Stock actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->update(['activo' => false]);

        return redirect()->route('productos.index')
            ->with('status', 'Producto desactivado.');
    }
}