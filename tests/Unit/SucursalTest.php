<?php

namespace Tests\Unit;

use App\Models\Sucursal;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SucursalTest extends TestCase
{
    // Dado: una sucursal con usuarios asignados
    // Cuando: se consulta la relación
    // Entonces: retorna la relación con usuarios
    #[Test]
    public function usuarios_conSucursalConUsuarios_debeRetornarRelacion(): void
    {
        $sucursal = new Sucursal();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $sucursal->usuarios()
        );
    }
}
