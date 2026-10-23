<?php

namespace Tests\Unit;

use App\Models\Compra;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompraTest extends TestCase
{
    // Dado: una compra registrada
    // Cuando: se consultan sus líneas de detalle
    // Entonces: debe retornar la relación con los detalles
    #[Test]
    public function compra_relacionDetalleCompras_debeRetornarDetalles(): void
    {
        $compra = new Compra();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $compra->detalleCompras()
        );
    }
}
