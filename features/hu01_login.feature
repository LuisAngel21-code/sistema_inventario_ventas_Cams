# language: es
Característica: Inicio de sesión
  Como usuario del sistema
  Quiero iniciar sesión
  Para acceder de forma segura al sistema

  Escenario: Acceso con credenciales correctas
    Dado que existe el usuario "admin.behat@cams.com" con rol "administrador"
    Cuando inicio sesión con el correo "admin.behat@cams.com" y la clave "cams2026"
    Entonces las credenciales deben ser válidas

  Escenario: Acceso con credenciales incorrectas
    Dado que existe el usuario "admin.behat@cams.com" con rol "administrador"
    Cuando inicio sesión con el correo "admin.behat@cams.com" y la clave "clave-mala"
    Entonces las credenciales no deben ser válidas

  Escenario: Acceso sin credenciales
    Cuando intento iniciar sesión sin credenciales
    Entonces las credenciales no deben ser válidas
