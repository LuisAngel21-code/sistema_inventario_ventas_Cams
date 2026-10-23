# language: es
Característica: Registro de ingreso de mercadería
  Como encargado de almacén
  Quiero registrar el ingreso de mercadería
  Para incrementar el stock de los productos recibidos

  Escenario: Seleccionar el producto y registrar la cantidad
    Dado que existe el usuario "almacen.behat@cams.com" con rol "encargado de almacen"
    Y que existe un producto "Cama Ingreso" con stock 10 en la sucursal "Tienda Behat"
    Cuando registro un ingreso de 5 unidades del producto
    Entonces debe quedar registrado un movimiento de entrada

  Escenario: Incrementar el stock al confirmar el ingreso
    Dado que existe el usuario "almacen.behat@cams.com" con rol "encargado de almacen"
    Y que existe un producto "Cama Ingreso Dos" con stock 10 en la sucursal "Tienda Behat"
    Cuando registro un ingreso de 5 unidades del producto
    Entonces el stock del producto debe incrementarse

  Escenario: Registrar el ingreso como movimiento
    Dado que existe el usuario "almacen.behat@cams.com" con rol "encargado de almacen"
    Y que existe un producto "Cama Ingreso Tres" con stock 10 en la sucursal "Tienda Behat"
    Cuando registro un ingreso de 3 unidades del producto
    Entonces debe quedar registrado un movimiento de entrada
