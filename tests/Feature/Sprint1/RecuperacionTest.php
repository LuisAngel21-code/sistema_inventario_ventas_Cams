<?php

namespace Tests\Feature\Sprint1;

use App\Models\RecoveryToken;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecuperacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $sucursal = $this->crearSucursal();
        $this->crearUsuario('administrador', $sucursal->id);
    }

    // Dado: un usuario que olvidó su contraseña
    // Cuando: envía el formulario sin escribir el correo
    // Entonces: el sistema debe indicar que el correo es obligatorio
    #[Test]
    public function recuperar_correoVacio_debeMostrarError(): void
    {
        $response = $this->post('/recuperar', [
            'username' => '',
        ]);

        $response->assertSessionHasErrors('username');
    }

    // Dado: un usuario registrado en el sistema
    // Cuando: envía su correo correctamente
    // Entonces: el sistema debe continuar con el proceso de recuperación
    #[Test]
    public function recuperar_correoRegistrado_debeContinuarProceso(): void
    {
        $response = $this->post('/recuperar', [
            'username' => 'administrador@cams.com',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseCount('recovery_tokens', 1);
    }

    // Dado: un usuario que solicitó la recuperación
    // Cuando: se genera su enlace de recuperación
    // Entonces: el enlace debe quedar vigente para establecer la nueva contraseña
    #[Test]
    public function recuperar_nuevaContrasena_debeGenerarTokenVigente(): void
    {
        $this->post('/recuperar', [
            'username' => 'administrador@cams.com',
        ]);

        $token = RecoveryToken::first();

        $this->assertNotNull($token);
        $this->assertTrue($token->sigueVigente());
    }
}
