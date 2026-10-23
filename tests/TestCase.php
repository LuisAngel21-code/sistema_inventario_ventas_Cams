<?php

namespace Tests;

use App\Models\Producto;
use App\Models\Stock;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function crearSucursal(string $nombre = 'Tienda Central'): Sucursal
    {
        return Sucursal::create([
            'nombre' => $nombre,
            'direccion' => 'Av. Principal 123',
            'telefono' => '987654321',
            'activo' => true,
        ]);
    }

    protected function crearUsuario(string $rol, int $sucursalId): Usuario
    {
        return Usuario::create([
            'username' => "{$rol}@cams.com",
            'password_hash' => bcrypt('cams2026'),
            'rol' => $rol,
            'sucursal_id' => $sucursalId,
            'activo' => true,
        ]);
    }

    protected function crearProducto(array $datos = []): Producto
    {
        return Producto::create(array_merge([
            'codigo' => 'TEST-0001',
            'nombre' => 'Cama Queen Clasica',
            'descripcion' => 'Madera solida',
            'categoria' => 'Camas',
            'marca' => 'Cams',
            'costo' => 2500,
            'precio_base' => 3500,
            'stock' => 10,
            'stock_minimo' => 2,
            'activo' => true,
        ], $datos));
    }

    protected function crearStock(Producto $producto, Sucursal $sucursal, int $cantidad): Stock
    {
        return Stock::create([
            'producto_id' => $producto->id,
            'sucursal_id' => $sucursal->id,
            'cantidad' => $cantidad,
        ]);
    }
}
