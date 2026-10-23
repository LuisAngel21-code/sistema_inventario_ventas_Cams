<?php

namespace Tests\Unit;

use App\Models\Producto;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    private function productoConStock(int $stock): Producto
    {
        return new Producto([
            'codigo' => 'UNIT-001',
            'nombre' => 'Producto Unitario',
            'categoria' => 'Camas',
            'precio_base' => 100,
            'stock' => $stock,
        ]);
    }

    // Dado: un producto con stock 10
    // Cuando: se actualiza el stock a 15
    // Entonces: el stock queda en 15
    #[Test]
    public function actualizarStock_conCantidadValida_debeGuardarNuevoValor(): void
    {
        $producto = $this->productoConStock(10);

        $producto->actualizarStock(15);

        $this->assertEquals(15, $producto->stock);
    }

    // Dado: un producto con stock 10
    // Cuando: se actualiza el stock a cero
    // Entonces: el stock queda en cero (la validación de negativos la hace el formulario)
    #[Test]
    public function actualizarStock_conCantidadCero_debeGuardarCero(): void
    {
        $producto = $this->productoConStock(10);

        $producto->actualizarStock(0);

        $this->assertEquals(0, $producto->stock);
    }

    // Dado: un producto con stock igual al mínimo
    // Cuando: se pregunta si tiene stock bajo
    // Entonces: retorna true
    #[Test]
    public function tieneStockBajo_conStockIgualAlMinimo_debeRetornarTrue(): void
    {
        $producto = new Producto(['stock' => 2, 'stock_minimo' => 2]);

        $this->assertTrue($producto->tieneStockBajo());
    }

    // Dado: un producto con stock mayor al mínimo
    // Cuando: se pregunta si tiene stock bajo
    // Entonces: retorna false
    #[Test]
    public function tieneStockBajo_conStockMayorAlMinimo_debeRetornarFalse(): void
    {
        $producto = new Producto(['stock' => 10, 'stock_minimo' => 2]);

        $this->assertFalse($producto->tieneStockBajo());
    }
}
