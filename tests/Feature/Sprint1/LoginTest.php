<?php

namespace Tests\Feature\Sprint1;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $sucursal = $this->crearSucursal();
        $this->crearUsuario('administrador', $sucursal->id);
    }

    // Dado: un usuario registrado y activo en el sistema
    // Cuando: se envían las credenciales correctas al formulario de acceso
    // Entonces: el sistema debe marcar la verificación pendiente y no dejar entrar aún
    #[Test]
    public function acceder_credencialesCorrectas_debePermitirAcceso(): void
    {
        $response = $this->post('/login', [
            'username' => 'administrador@cams.com',
            'password' => 'cams2026',
        ]);

        $response->assertSessionHas('pending_2fa');
        $this->assertGuest();
    }

    // Dado: un usuario que ingresa credenciales vacías
    // Cuando: se envía el formulario sin completar
    // Entonces: el sistema debe mostrar un error de validación
    #[Test]
    public function acceder_credencialesVacias_debeMostrarError(): void
    {
        $response = $this->post('/login', [
            'username' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['username', 'password']);
    }

    // Dado: un usuario que ingresa credenciales incorrectas
    // Cuando: se envían datos que no coinciden con los registrados
    // Entonces: el sistema debe mostrar un mensaje indicando que los datos no son válidos
    #[Test]
    public function acceder_credencialesIncorrectas_debeMostrarMensaje(): void
    {
        $response = $this->post('/login', [
            'username' => 'administrador@cams.com',
            'password' => 'clave-mala',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
