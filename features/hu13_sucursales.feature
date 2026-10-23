# language: es
Característica: Gestión de sucursales
  Como administrador
  Quiero registrar y gestionar las sucursales
  Para organizar el inventario y las operaciones de cada sucursal

  Escenario: Registrar una sucursal
    Cuando registro la sucursal "Sucursal Behat Norte"
    Entonces la sucursal debe existir en la base de datos

  Escenario: Consultar las sucursales registradas
    Cuando registro la sucursal "Sucursal Behat Norte"
    Entonces debo ver la sucursal en el listado

  Escenario: Cambiar el estado de una sucursal
    Cuando registro la sucursal "Sucursal Behat Norte"
    Y cambio el estado de la sucursal
    Entonces la sucursal debe quedar inactiva
