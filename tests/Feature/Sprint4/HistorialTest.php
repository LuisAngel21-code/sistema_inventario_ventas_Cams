<?php

namespace Tests\Feature\Sprint4;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\Venta;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HistorialTest extends TestCase
{
    private Usuario $vendedor;
    private Sucursal $sucursal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sucursal = $this->crearSucursal();
        $this->vendedor = $this->crearUsuario('encargado_tienda', $this->sucursal->id);
    }

    private function registrarVenta(Producto $producto, int $cantidad, string $fecha): Venta
    {
        $venta = Venta::create([
            'fecha' => $fecha,
            'subtotal' => $producto->precio_base * $cantidad,
            'descuento' => 0,
            'total' => $producto->precio_base * $cantidad,
            'tipo_pago' => 'efectivo',
            'comprobante_tipo' => 'boleta',
            'cliente_nombre' => 'Juan Perez',
            'vendedor_nombre' => 'Maria Garcia',
            'estado' => 'completada',
            'sucursal_id' => $this->sucursal->id,
            'usuario_id' => $this->vendedor->id,
        ]);

        DetalleVenta::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $producto->precio_base,
        ]);

        return $venta;
    }

    // Dado: ventas registradas en el sistema
    // Cuando: se consulta el historial
    // Entonces: debe mostrarse la fecha, los productos y el monto total
    #[Test]
    public function historial_muestraFechaProductosYMonto(): void
    {
        $producto = $this->crearProducto(['nombre' => 'Cama Queen', 'precio_base' => 3500]);
        $this->registrarVenta($producto, 2, '2026-01-15 10:00:00');

        $response = $this->actingAs($this->vendedor)->get('/ventas');

        $response->assertStatus(200);
        $response->assertSee('Cama Queen');
        $response->assertSee('15/01/2026');
        $response->assertSee('7,000.00');
    }

    // Dado: una venta registrada
    // Cuando: se consulta el detalle de esa venta
    // Entonces: debe mostrarse cada producto con su cantidad y subtotal
    #[Test]
    public function historial_detalleVenta_debeMostrarLineas(): void
    {
        $producto = $this->crearProducto(['nombre' => 'Cama Queen', 'precio_base' => 3500]);
        $venta = $this->registrarVenta($producto, 2, '2026-01-15 10:00:00');

        $response = $this->actingAs($this->vendedor)->get('/ventas/' . $venta->id);

        $response->assertStatus(200);
        $response->assertSee('Cama Queen');
        $response->assertSee('7,000.00');
    }

    // Dado: ventas registradas en fechas distintas
    // Cuando: se filtra el historial por un rango de fechas
    // Entonces: solo deben mostrarse las ventas dentro de ese rango
    #[Test]
    public function historial_filtrarPorFecha_debeMostrarSoloRango(): void
    {
        $productoEnero = $this->crearProducto(['codigo' => 'CAMA-ENE', 'nombre' => 'Cama Enero']);
        $productoFebrero = $this->crearProducto(['codigo' => 'CAMA-FEB', 'nombre' => 'Cama Febrero']);
        $this->registrarVenta($productoEnero, 1, '2026-01-10 10:00:00');
        $this->registrarVenta($productoFebrero, 1, '2026-02-10 10:00:00');

        $response = $this->actingAs($this->vendedor)
            ->get('/ventas?fecha_inicio=2026-01-01&fecha_fin=2026-01-31');

        $response->assertStatus(200);
        $response->assertSee('Cama Enero');
        $response->assertDontSee('Cama Febrero');
    }
}
