<?php

namespace Tests\Unit;

use App\Models\OtpCode;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OtpCodeTest extends TestCase
{
    // Dado: un código correcto y vigente
    // Cuando: se verifica el código
    // Entonces: retorna true
    #[Test]
    public function esValido_conCodigoCorrectoYVigente_debeRetornarTrue(): void
    {
        $otp = new OtpCode([
            'codigo' => '123456',
            'expira_en' => now()->addMinutes(5),
            'verificado' => false,
        ]);

        $this->assertTrue($otp->esValido('123456'));
    }

    // Dado: un código incorrecto
    // Cuando: se verifica el código
    // Entonces: retorna false
    #[Test]
    public function esValido_conCodigoIncorrecto_debeRetornarFalse(): void
    {
        $otp = new OtpCode([
            'codigo' => '123456',
            'expira_en' => now()->addMinutes(5),
            'verificado' => false,
        ]);

        $this->assertFalse($otp->esValido('000000'));
    }
}
