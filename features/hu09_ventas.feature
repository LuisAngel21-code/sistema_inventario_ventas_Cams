# language: es
Característica: Registro de ventas
  Como encargado de tienda
  Quiero registrar las ventas realizadas
  Para llevar un control de las operaciones y actualizar el inventario

  Escenario: Registrar una venta con stock disponible
    Dado que existe el usuario "tienda.behat@cams.com" con rol "encargado de tienda"
    Y que existe un producto "Cama Venta" con stock 10 en la sucursal "Tienda Behat"
    Cuando registro una venta de 2 unidades del producto
    Entonces la venta debe existir en la base de datos

  Escenario: Rechazar una venta que supera el stock
    Dado que existe el usuario "tienda.behat@cams.com" con rol "encargado de tienda"
    Y que existe un producto "Cama Venta Dos" con stock 10 en la sucursal "Tienda Behat"
    Cuando intento registrar una venta de 50 unidades del producto
    Entonces la venta debe ser rechazada

  Escenario: Disminuir el stock al confirmar la venta
    Dado que existe el usuario "tienda.behat@cams.com" con rol "encargado de tienda"
    Y que existe un producto "Cama Venta Tres" con stock 10 en la sucursal "Tienda Behat"
    Cuando registro una venta de 2 unidades del producto
    Entonces el stock del producto debe quedar en 8
