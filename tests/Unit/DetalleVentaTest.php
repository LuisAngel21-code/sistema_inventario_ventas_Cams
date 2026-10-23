<?php

namespace Tests\Unit;

use App\Models\DetalleVenta;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DetalleVentaTest extends TestCase
{
    // Dado: un detalle con cantidad 2 y precio 3500
    // Cuando: se calcula el subtotal
    // Entonces: debe retornar 7000
    #[Test]
    public function detalleVenta_calcularSubtotal_debeRetornarMonto(): void
    {
        $detalle = new DetalleVenta([
            'cantidad' => 2,
            'precio_unitario' => 3500,
        ]);

        $this->assertEquals(7000, $detalle->calcularSubtotal());
    }

    // Dado: un detalle de venta con producto asociado
    // Cuando: se consulta la relación producto
    // Entonces: debe retornar una relación con el producto
    #[Test]
    public function detalleVenta_relacionProducto_debeRetornarProducto(): void
    {
        $detalle = new DetalleVenta(['producto_id' => 1]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $detalle->producto()
        );
    }
}
