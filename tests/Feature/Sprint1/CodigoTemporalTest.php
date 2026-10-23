<?php

namespace Tests\Feature\Sprint1;

use App\Models\Usuario;
use PragmaRX\Google2FA\Google2FA;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CodigoTemporalTest extends TestCase
{
    private Usuario $usuario;
    private Google2FA $google2fa;

    protected function setUp(): void
    {
        parent::setUp();

        $sucursal = $this->crearSucursal();
        $this->usuario = $this->crearUsuario('administrador', $sucursal->id);
        $this->google2fa = new Google2FA();

        $this->usuario->two_factor_secret = $this->google2fa->generateSecretKey();
        $this->usuario->save();
    }

    private function dejarVerificacionPendiente(): void
    {
        $this->withSession(['pending_2fa' => $this->usuario->id]);
    }

    // Dado: un usuario que ya ingresó su contraseña correctamente
    // Cuando: el sistema muestra la pantalla de verificación y se envía vacío
    // Entonces: se debe exigir el código de 6 dígitos
    #[Test]
    public function verificar_codigoVacio_debeMostrarError(): void
    {
        $this->dejarVerificacionPendiente();

        $response = $this->post('/login/verificar', ['codigo' => '']);

        $response->assertSessionHasErrors('codigo');
        $this->assertGuest();
    }

    // Dado: un usuario con verificación pendiente
    // Cuando: ingresa el código correcto y vigente de su aplicación
    // Entonces: el sistema debe permitir el acceso al panel
    #[Test]
    public function verificar_codigoValido_debePermitirAcceso(): void
    {
        $this->dejarVerificacionPendiente();
        $codigo = str_pad(
            (string) $this->google2fa->getCurrentOtp($this->usuario->two_factor_secret),
            6,
            '0',
            STR_PAD_LEFT
        );

        $response = $this->post('/login/verificar', ['codigo' => $codigo]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->usuario);
    }

    // Dado: un usuario con verificación pendiente
    // Cuando: ingresa un código que no coincide
    // Entonces: el sistema debe rechazar el acceso y mostrar un mensaje
    #[Test]
    public function verificar_codigoIncorrecto_debeRechazarAcceso(): void
    {
        $this->dejarVerificacionPendiente();

        $response = $this->post('/login/verificar', ['codigo' => '000000']);

        $response->assertSessionHasErrors('codigo');
        $this->assertGuest();
    }

    // Dado: un usuario con verificación pendiente
    // Cuando: el código ya superó el tiempo establecido
    // Entonces: el sistema debe rechazar el acceso y pedir uno nuevo
    #[Test]
    public function verificar_codigoExpirado_debeRechazarAcceso(): void
    {
        $this->dejarVerificacionPendiente();
        $codigoVencido = str_pad((string) $this->google2fa->oathTotp(
            $this->usuario->two_factor_secret,
            $this->google2fa->getTimestamp() - 20
        ), 6, '0', STR_PAD_LEFT);

        $response = $this->post('/login/verificar', ['codigo' => $codigoVencido]);

        $response->assertSessionHasErrors('codigo');
        $this->assertGuest();
    }
}
