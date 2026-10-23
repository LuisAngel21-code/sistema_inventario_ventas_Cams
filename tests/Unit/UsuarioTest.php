<?php

namespace Tests\Unit;

use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    // Dado: un usuario con contraseña correcta
    // Cuando: se verifica la contraseña
    // Entonces: retorna true
    #[Test]
    public function verificarCredenciales_contrasenaCorrecta_debeRetornarTrue(): void
    {
        $usuario = new Usuario(['password_hash' => bcrypt('cams2026')]);

        $this->assertTrue($usuario->verificarCredenciales('cams2026'));
    }

    // Dado: un usuario con contraseña incorrecta
    // Cuando: se verifica la contraseña
    // Entonces: retorna false
    #[Test]
    public function verificarCredenciales_contrasenaIncorrecta_debeRetornarFalse(): void
    {
        $usuario = new Usuario(['password_hash' => bcrypt('cams2026')]);

        $this->assertFalse($usuario->verificarCredenciales('clave-mala'));
    }

    // Dado: un usuario con rol administrador
    // Cuando: se pregunta si es admin
    // Entonces: retorna true
    #[Test]
    public function esAdmin_conRolAdministrador_debeRetornarTrue(): void
    {
        $usuario = new Usuario(['rol' => 'administrador']);

        $this->assertTrue($usuario->esAdmin());
    }

    // Dado: un usuario con rol no administrador
    // Cuando: se pregunta si es admin
    // Entonces: retorna false
    #[Test]
    public function esAdmin_conRolNoAdministrador_debeRetornarFalse(): void
    {
        $usuario = new Usuario(['rol' => 'encargado_almacen']);

        $this->assertFalse($usuario->esAdmin());
    }

    // Dado: un usuario con rol jefe
    // Cuando: se pregunta si es jefe
    // Entonces: retorna true
    #[Test]
    public function esJefe_conRolJefe_debeRetornarTrue(): void
    {
        $usuario = new Usuario(['rol' => 'jefe']);

        $this->assertTrue($usuario->esJefe());
    }

    // Dado: un usuario con rol encargado de tienda
    // Cuando: se pregunta si es encargado de tienda
    // Entonces: retorna true
    #[Test]
    public function esEncargadoTienda_conRolTienda_debeRetornarTrue(): void
    {
        $usuario = new Usuario(['rol' => 'encargado_tienda']);

        $this->assertTrue($usuario->esEncargadoTienda());
    }
}
