<?php

namespace Tests\Feature\Sprint2;

use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    private Usuario $almacenero;

    protected function setUp(): void
    {
        parent::setUp();

        $sucursal = $this->crearSucursal();
        $this->almacenero = $this->crearUsuario('encargado_almacen', $sucursal->id);
    }

    // Dado: un encargado de almacén autenticado
    // Cuando: registra un producto de tipo cama o colchón con todos sus datos
    // Entonces: el producto debe guardarse correctamente
    #[Test]
    public function registrar_categoriaCamaColchon_debeGuardarProducto(): void
    {
        $response = $this->actingAs($this->almacenero)->post('/productos', [
            'codigo' => 'CAMA-TEST',
            'nombre' => 'Cama Test',
            'categoria' => 'Camas',
            'costo' => 1000,
            'precio_base' => 1500,
            'stock' => 5,
            'stock_minimo' => 1,
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('productos', ['codigo' => 'CAMA-TEST']);
    }

    // Dado: un encargado de almacén autenticado
    // Cuando: intenta guardar un producto sin los datos obligatorios
    // Entonces: el sistema debe rechazar el registro y mostrar el error
    #[Test]
    public function registrar_datosObligatoriosFaltantes_debeMostrarError(): void
    {
        $response = $this->actingAs($this->almacenero)->post('/productos', [
            'codigo' => '',
            'nombre' => '',
            'categoria' => '',
        ]);

        $response->assertSessionHasErrors(['codigo', 'nombre', 'categoria']);
    }

    // Dado: un producto registrado exitosamente
    // Cuando: se consulta el catálogo
    // Entonces: el producto debe aparecer en la lista visible
    #[Test]
    public function registrar_productoExitoso_debeAparecerEnCatalogo(): void
    {
        $this->actingAs($this->almacenero)->post('/productos', [
            'codigo' => 'CAMA-TEST',
            'nombre' => 'Cama Test',
            'categoria' => 'Camas',
            'costo' => 1000,
            'precio_base' => 1500,
            'stock' => 5,
            'stock_minimo' => 1,
        ]);

        $response = $this->actingAs($this->almacenero)->get('/productos');

        $response->assertStatus(200);
        $response->assertSee('Cama Test');
    }
}
