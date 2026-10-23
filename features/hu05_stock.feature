# language: es
Característica: Actualización de stock
  Como encargado de almacén
  Quiero actualizar el stock
  Para mantener las cantidades correctamente registradas

  Escenario: Actualizar la cantidad disponible
    Dado que existe un producto "Cama Stock" con stock 10 en la sucursal "Tienda Behat"
    Cuando actualizo el stock del producto a 15
    Entonces el stock del producto debe ser 15

  Escenario: Validar una cantidad inválida
    Dado que existe un producto "Cama Stock Dos" con stock 10 en la sucursal "Tienda Behat"
    Cuando intento actualizar el stock a -5
    Entonces la actualización debe ser rechazada

  Escenario: Visualizar el nuevo stock
    Dado que existe un producto "Cama Stock Tres" con stock 10 en la sucursal "Tienda Behat"
    Cuando actualizo el stock del producto a 20
    Entonces el stock del producto debe ser 20
