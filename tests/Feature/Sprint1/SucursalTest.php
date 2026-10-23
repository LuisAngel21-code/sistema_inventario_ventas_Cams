<?php

namespace Tests\Feature\Sprint1;

use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SucursalTest extends TestCase
{
    private Usuario $administrador;

    protected function setUp(): void
    {
        parent::setUp();

        $sucursal = $this->crearSucursal();
        $this->administrador = $this->crearUsuario('administrador', $sucursal->id);
    }

    // Dado: un administrador autenticado
    // Cuando: registra una nueva sucursal con sus datos principales
    // Entonces: la sucursal debe guardarse en el sistema
    #[Test]
    public function registrar_datosCompletos_debeGuardarSucursal(): void
    {
        $response = $this->actingAs($this->administrador)->post('/sucursales', [
            'nombre' => 'Tienda Sur',
            'direccion' => 'Av. Sur 789',
            'telefono' => '987654323',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('sucursales', ['nombre' => 'Tienda Sur']);
    }

    // Dado: un administrador autenticado
    // Cuando: consulta el listado de sucursales
    // Entonces: debe ver las sucursales registradas con su información
    #[Test]
    public function consultar_sucursalesRegistradas_debeMostrarLista(): void
    {
        $this->actingAs($this->administrador)->post('/sucursales', [
            'nombre' => 'Tienda Sur',
            'direccion' => 'Av. Sur 789',
            'telefono' => '987654323',
        ]);

        $response = $this->actingAs($this->administrador)->get('/sucursales');

        $response->assertStatus(200);
        $response->assertSee('Tienda Sur');
    }

    // Dado: una sucursal activa registrada
    // Cuando: el administrador cambia su estado
    // Entonces: la sucursal debe reflejar el nuevo estado sin borrarse
    #[Test]
    public function cambiar_estadoActivaInactiva_debeActualizar(): void
    {
        $this->actingAs($this->administrador)->post('/sucursales', [
            'nombre' => 'Tienda Sur',
            'direccion' => 'Av. Sur 789',
            'telefono' => '987654323',
        ]);

        $sucursal = \App\Models\Sucursal::where('nombre', 'Tienda Sur')->first();

        $response = $this->actingAs($this->administrador)
            ->put('/sucursales/' . $sucursal->id . '/estado');

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('sucursales', ['id' => $sucursal->id, 'activo' => false]);
    }
}
