# language: es
Característica: Recuperación de contraseña
  Como usuario del sistema
  Quiero recuperar mi contraseña olvidada
  Para restablecer mi acceso al sistema

  Escenario: Recuperación con correo registrado
    Dado que existe el usuario "recuperar.behat@cams.com" con rol "administrador"
    Cuando solicito la recuperación de contraseña para "recuperar.behat@cams.com"
    Entonces debe generarse un token de recuperación

  Escenario: Recuperación con correo no registrado
    Cuando solicito la recuperación de contraseña para "noexiste.behat@cams.com"
    Entonces no debe generarse un token de recuperación

  Escenario: Establecer una nueva contraseña
    Dado que existe el usuario "recuperar2.behat@cams.com" con rol "administrador"
    Cuando solicito la recuperación de contraseña para "recuperar2.behat@cams.com"
    Y establezco la nueva contraseña "nueva2026"
    Entonces puedo iniciar sesión con la nueva contraseña
