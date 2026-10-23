# language: es
Característica: Verificación con código temporal
  Como usuario del sistema
  Quiero ingresar un código temporal después de mi contraseña
  Para proteger la información de ventas e inventario

  Escenario: Código vigente
    Dado que existe el usuario "otp.behat@cams.com" con rol "administrador"
    Cuando genero un código temporal para el usuario
    Entonces el código temporal debe ser válido

  Escenario: Código incorrecto
    Dado que existe el usuario "otp.behat@cams.com" con rol "administrador"
    Cuando genero un código temporal para el usuario
    Y ingreso un código incorrecto
    Entonces el código temporal debe ser rechazado

  Escenario: Código expirado
    Dado que existe el usuario "otp.behat@cams.com" con rol "administrador"
    Cuando ingreso un código expirado
    Entonces el código temporal debe ser rechazado
