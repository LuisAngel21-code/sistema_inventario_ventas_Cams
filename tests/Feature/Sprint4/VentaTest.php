<?php

namespace Tests\Feature\Sprint4;

use App\Models\Sucursal;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VentaTest extends TestCase
{
    private Usuario $vendedor;
    private Sucursal $sucursal;
    private \App\Models\Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sucursal = $this->crearSucursal();
        $this->vendedor = $this->crearUsuario('encargado_tienda', $this->sucursal->id);
        $this->producto = $this->crearProducto([
            'codigo' => 'VEN-001',
            'nombre' => 'Cama Queen',
            'precio_base' => 3500,
            'stock' => 10,
        ]);
        $this->crearStock($this->producto, $this->sucursal, 10);
    }

    private function registrarVenta(int $cantidad)
    {
        return $this->actingAs($this->vendedor)->post('/ventas', [
            'sucursal_id' => $this->sucursal->id,
            'tipo_pago' => 'efectivo',
            'comprobante_tipo' => 'boleta',
            'cliente_nombre' => 'Juan Perez',
            'vendedor_nombre' => 'Maria Garcia',
            'items' => [
                ['producto_id' => $this->producto->id, 'cantidad' => $cantidad],
            ],
        ]);
    }

    // Dado: un encargado de tienda autenticado
    // Cuando: selecciona el producto vendido y registra la cantidad
    // Entonces: el sistema debe guardar la venta con su total
    #[Test]
    public function venta_registrarProductoCantidad_debeCrearVenta(): void
    {
        $response = $this->registrarVenta(2);

        $response->assertSessionHas('status');
        $response->assertRedirect(route('ventas.historial'));
        $this->assertDatabaseHas('ventas', ['total' => 7000]);
        $this->assertDatabaseHas('detalle_ventas', [
            'producto_id' => $this->producto->id,
            'cantidad' => 2,
            'precio_unitario' => 3500,
        ]);
    }

    // Dado: un producto con 10 unidades disponibles
    // Cuando: se intenta vender una cantidad mayor al stock
    // Entonces: el sistema no debe permitir la venta
    #[Test]
    public function venta_cantidadSuperaStock_debeRechazar(): void
    {
        $response = $this->registrarVenta(999);

        $response->assertSessionHasErrors('items');
        $response->assertRedirect();
        $this->assertDatabaseCount('ventas', 0);
    }

    // Dado: una venta confirmada de 2 unidades
    // Cuando: se consulta el inventario
    // Entonces: el stock debe disminuir según la cantidad vendida
    #[Test]
    public function venta_confirmar_debeDisminuirStock(): void
    {
        $this->registrarVenta(2);

        $this->assertDatabaseHas('stock', [
            'producto_id' => $this->producto->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 8,
        ]);
        $this->assertDatabaseHas('productos', ['id' => $this->producto->id, 'stock' => 8]);
        $this->assertDatabaseHas('movimientos', [
            'producto_id' => $this->producto->id,
            'tipo' => 'salida',
            'cantidad' => 2,
        ]);
    }
}
