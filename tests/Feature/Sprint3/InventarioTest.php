<?php

namespace Tests\Feature\Sprint3;

use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    private Usuario $almacenero;
    private \App\Models\Sucursal $sucursal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sucursal = $this->crearSucursal();
        $this->almacenero = $this->crearUsuario('encargado_almacen', $this->sucursal->id);
    }

    // Dado: productos registrados con stock en la sucursal
    // Cuando: el encargado consulta el inventario
    // Entonces: debe ver los productos y su cantidad disponible
    #[Test]
    public function inventario_muestraProductosYCantidad(): void
    {
        $cama = $this->crearProducto(['codigo' => 'INV-001', 'nombre' => 'Cama Test Inventario']);
        $this->crearStock($cama, $this->sucursal, 10);

        $response = $this->actingAs($this->almacenero)->get('/inventario');

        $response->assertStatus(200);
        $response->assertSee('Cama Test Inventario');
        $response->assertSee('10 unid.');
    }

    // Dado: productos de tipo cama y colchón
    // Cuando: se filtra el inventario por la categoría Camas
    // Entonces: solo deben mostrarse los productos de esa categoría
    #[Test]
    public function inventario_filtraCamasYColchones_debeMostrarSoloEsos(): void
    {
        $cama = $this->crearProducto(['codigo' => 'INV-001', 'nombre' => 'Cama Test Inventario', 'categoria' => 'Camas']);
        $colchon = $this->crearProducto(['codigo' => 'INV-003', 'nombre' => 'Colchon Test Inventario', 'categoria' => 'Colchones']);
        $this->crearStock($cama, $this->sucursal, 10);
        $this->crearStock($colchon, $this->sucursal, 20);

        $response = $this->actingAs($this->almacenero)->get('/inventario?categoria=Camas');

        $response->assertStatus(200);
        $response->assertSee('Cama Test Inventario');
        $response->assertDontSee('Colchon Test Inventario');
    }

    // Dado: un producto con 15 unidades registradas en el sistema
    // Cuando: se consulta el inventario
    // Entonces: la cantidad mostrada debe corresponder al stock registrado
    #[Test]
    public function inventario_cantidadMostrada_debeCoincidirConBd(): void
    {
        $cama = $this->crearProducto(['codigo' => 'INV-001', 'nombre' => 'Cama Test Inventario']);
        $this->crearStock($cama, $this->sucursal, 15);

        $response = $this->actingAs($this->almacenero)->get('/inventario');

        $response->assertStatus(200);
        $response->assertSee('15 unid.');
    }
}
