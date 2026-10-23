<?php

namespace Tests\Unit;

use App\Models\RecoveryToken;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecoveryTokenTest extends TestCase
{
    // Dado: un token no usado y vigente
    // Cuando: se pregunta si sigue vigente
    // Entonces: retorna true
    #[Test]
    public function sigueVigente_conTokenNoUsadoYVigente_debeRetornarTrue(): void
    {
        $token = new RecoveryToken([
            'usado' => false,
            'expira_en' => now()->addMinutes(30),
        ]);

        $this->assertTrue($token->sigueVigente());
    }

    // Dado: un token ya usado
    // Cuando: se pregunta si sigue vigente
    // Entonces: retorna false
    #[Test]
    public function sigueVigente_conTokenUsado_debeRetornarFalse(): void
    {
        $token = new RecoveryToken([
            'usado' => true,
            'expira_en' => now()->addMinutes(30),
        ]);

        $this->assertFalse($token->sigueVigente());
    }
}
