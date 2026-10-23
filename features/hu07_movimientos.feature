# language: es
Característica: Consulta de movimientos de inventario
  Como encargado de almacén
  Quiero consultar los movimientos de inventario
  Para conocer los ingresos y salidas de productos

  Escenario: Ver movimientos de entrada y salida
    Dado que existen movimientos de entrada y salida
    Cuando consulto los movimientos de la sucursal
    Entonces debo ver movimientos de entrada y salida

  Escenario: Datos mínimos de cada movimiento
    Dado que existen movimientos de entrada y salida
    Cuando consulto los movimientos de la sucursal
    Entonces cada movimiento debe mostrar producto, tipo, cantidad y fecha

  Escenario: Consultar los movimientos registrados
    Dado que existen movimientos de entrada y salida
    Cuando consulto los movimientos de la sucursal
    Entonces debo poder consultar los movimientos registrados
