<?php

namespace Tests\Feature\Sprint3;

use App\Models\Sucursal;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IngresoTest extends TestCase
{
    private Usuario $almacenero;
    private Sucursal $sucursal;
    private \App\Models\Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sucursal = $this->crearSucursal();
        $this->almacenero = $this->crearUsuario('encargado_almacen', $this->sucursal->id);
        $this->producto = $this->crearProducto(['stock' => 10]);
        $this->crearStock($this->producto, $this->sucursal, 10);
    }

    // Dado: un encargado de almacén autenticado
    // Cuando: selecciona el producto recibido y registra la cantidad ingresada
    // Entonces: el sistema debe aceptar y guardar el ingreso
    #[Test]
    public function ingreso_seleccionarProductoYCantidad_debeGuardar(): void
    {
        $response = $this->actingAs($this->almacenero)->post('/inventario/ingreso', [
            'producto_id' => $this->producto->id,
            'cantidad' => 5,
            'sucursal_id' => $this->sucursal->id,
            'motivo' => 'Reposicion',
        ]);

        $response->assertSessionHas('status');
        $response->assertRedirect(route('inventario.movimientos'));
    }

    // Dado: un ingreso de 5 unidades
    // Cuando: se confirma el registro
    // Entonces: el stock del producto debe incrementarse
    #[Test]
    public function ingreso_confirmar_debeIncrementarStock(): void
    {
        $this->actingAs($this->almacenero)->post('/inventario/ingreso', [
            'producto_id' => $this->producto->id,
            'cantidad' => 5,
            'sucursal_id' => $this->sucursal->id,
        ]);

        $this->assertDatabaseHas('stock', [
            'producto_id' => $this->producto->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 15,
        ]);
        $this->assertDatabaseHas('productos', ['id' => $this->producto->id, 'stock' => 15]);
    }

    // Dado: un ingreso confirmado
    // Cuando: se consulta el inventario
    // Entonces: el ingreso debe quedar registrado como movimiento de entrada
    #[Test]
    public function ingreso_registrado_debeCrearMovimientoEntrada(): void
    {
        $this->actingAs($this->almacenero)->post('/inventario/ingreso', [
            'producto_id' => $this->producto->id,
            'cantidad' => 5,
            'sucursal_id' => $this->sucursal->id,
            'motivo' => 'Reposicion',
        ]);

        $this->assertDatabaseHas('movimientos', [
            'producto_id' => $this->producto->id,
            'sucursal_id' => $this->sucursal->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
        ]);
    }
}
