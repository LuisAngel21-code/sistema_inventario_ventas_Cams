<?php

namespace Tests\Feature\Sprint3;

use App\Models\Movimiento;
use App\Models\Sucursal;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MovimientoTest extends TestCase
{
    private Usuario $almacenero;
    private Sucursal $sucursal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sucursal = $this->crearSucursal();
        $this->almacenero = $this->crearUsuario('encargado_almacen', $this->sucursal->id);
    }

    private function registrarMovimiento(string $tipo, int $cantidad): void
    {
        $producto = $this->crearProducto([
            'codigo' => 'CAMA-' . $tipo,
            'nombre' => 'Cama ' . $tipo,
        ]);

        Movimiento::create([
            'producto_id' => $producto->id,
            'sucursal_id' => $this->sucursal->id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'motivo' => 'Prueba',
            'usuario_id' => $this->almacenero->id,
            'fecha' => '2026-01-15 10:00:00',
        ]);
    }

    // Dado: movimientos de entrada y salida registrados
    // Cuando: el encargado consulta los movimientos
    // Entonces: debe ver ambos tipos de movimiento
    #[Test]
    public function movimientos_muestraEntradaYSalida(): void
    {
        $this->registrarMovimiento('entrada', 5);
        $this->registrarMovimiento('salida', 2);

        $response = $this->actingAs($this->almacenero)->get('/inventario/movimientos');

        $response->assertStatus(200);
        $response->assertSee('entrada');
        $response->assertSee('salida');
    }

    // Dado: un movimiento registrado con su producto
    // Cuando: se consulta la lista de movimientos
    // Entonces: debe mostrarse el producto, el tipo, la cantidad y la fecha
    #[Test]
    public function movimientos_muestraProductoTipoCantidadFecha(): void
    {
        $this->registrarMovimiento('entrada', 5);

        $response = $this->actingAs($this->almacenero)->get('/inventario/movimientos');

        $response->assertStatus(200);
        $response->assertSee('Cama entrada');
        $response->assertSee('entrada');
        $response->assertSee('5');
        $response->assertSee('15/01/2026');
    }

    // Dado: movimientos registrados en el sistema
    // Cuando: el encargado consulta el listado
    // Entonces: debe poder visualizar los movimientos consultados
    #[Test]
    public function movimientos_consultar_debeRetornarListado(): void
    {
        $this->registrarMovimiento('entrada', 3);

        $response = $this->actingAs($this->almacenero)->get('/inventario/movimientos');

        $response->assertStatus(200);
        $response->assertSee('Movimientos de inventario');
    }
}
