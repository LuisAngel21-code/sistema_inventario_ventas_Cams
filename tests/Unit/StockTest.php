<?php

namespace Tests\Unit;

use App\Models\Stock;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockTest extends TestCase
{
    private function stockConCantidad(int $cantidad): Stock
    {
        $sucursal = $this->crearSucursal();
        $producto = $this->crearProducto();

        return $this->crearStock($producto, $sucursal, $cantidad);
    }

    // Dado: un stock con cantidad 10
    // Cuando: se incrementa en 5
    // Entonces: la cantidad queda en 15
    #[Test]
    public function incrementar_conCantidadPositiva_debeSumarCorrectamente(): void
    {
        $stock = $this->stockConCantidad(10);

        $stock->incrementar(5);

        $this->assertEquals(15, $stock->cantidad);
    }

    // Dado: un stock con cantidad 10
    // Cuando: se decrementa en 3
    // Entonces: la cantidad queda en 7
    #[Test]
    public function decrementar_conCantidadPositiva_debeRestarCorrectamente(): void
    {
        $stock = $this->stockConCantidad(10);

        $stock->decrementar(3);

        $this->assertEquals(7, $stock->cantidad);
    }
}
