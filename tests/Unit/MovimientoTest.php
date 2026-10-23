<?php

namespace Tests\Unit;

use App\Models\Movimiento;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MovimientoTest extends TestCase
{
    // Dado: los tipos de movimiento del sistema
    // Cuando: se consultan las constantes
    // Entonces: deben existir entrada y salida
    #[Test]
    public function movimiento_tiposEntradaYSalida_debenExistir(): void
    {
        $this->assertEquals('entrada', Movimiento::TIPO_ENTRADA);
        $this->assertEquals('salida', Movimiento::TIPO_SALIDA);
    }

    // Dado: un movimiento con producto asociado
    // Cuando: se consulta la relación producto
    // Entonces: debe retornar una relación con el producto
    #[Test]
    public function movimiento_relacionProducto_debeRetornarProducto(): void
    {
        $movimiento = new Movimiento(['producto_id' => 1]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $movimiento->producto()
        );
    }
}
