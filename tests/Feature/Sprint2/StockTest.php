<?php

namespace Tests\Feature\Sprint2;

use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockTest extends TestCase
{
    private Usuario $almacenero;

    protected function setUp(): void
    {
        parent::setUp();

        $sucursal = $this->crearSucursal();
        $this->almacenero = $this->crearUsuario('encargado_almacen', $sucursal->id);
    }

    // Dado: un producto registrado con stock 10
    // Cuando: se modifica la cantidad disponible a 15
    // Entonces: el stock debe quedar actualizado en el sistema
    #[Test]
    public function actualizar_cantidadValida_debeModificarStock(): void
    {
        $producto = $this->crearProducto(['stock' => 10]);

        $response = $this->actingAs($this->almacenero)
            ->put('/productos/' . $producto->id . '/stock', ['stock' => 15]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock' => 15]);
    }

    // Dado: un producto registrado
    // Cuando: se intenta actualizar con una cantidad negativa
    // Entonces: el sistema debe rechazar la actualización
    #[Test]
    public function actualizar_cantidadInvalida_debeRechazar(): void
    {
        $producto = $this->crearProducto(['stock' => 10]);

        $response = $this->actingAs($this->almacenero)
            ->put('/productos/' . $producto->id . '/stock', ['stock' => -5]);

        $response->assertSessionHasErrors('stock');
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock' => 10]);
    }

    // Dado: un stock actualizado correctamente
    // Cuando: se consulta el inventario
    // Entonces: el nuevo valor debe ser visible en la lista
    #[Test]
    public function actualizar_stockGuardado_debeMostrarNuevoValor(): void
    {
        $producto = $this->crearProducto(['stock' => 10]);

        $this->actingAs($this->almacenero)
            ->put('/productos/' . $producto->id . '/stock', ['stock' => 20]);

        $response = $this->actingAs($this->almacenero)->get('/productos');

        $response->assertStatus(200);
        $response->assertSee('20 unid.');
    }
}
