# language: es
Característica: Consulta de inventario
  Como encargado de almacén
  Quiero consultar el inventario
  Para conocer el stock disponible

  Escenario: Mostrar productos y cantidad disponible
    Dado que existe un producto "Cama Inventario" con stock 10 en la sucursal "Tienda Behat"
    Cuando consulto el inventario de la sucursal
    Entonces debo ver el producto con cantidad 10

  Escenario: Consultar productos de tipo cama
    Dado que existe un producto "Cama Inventario" con stock 10 en la sucursal "Tienda Behat"
    Cuando consulto el inventario de la categoría "Camas"
    Entonces solo debo ver productos de esa categoría

  Escenario: La cantidad coincide con el stock registrado
    Dado que existe un producto "Cama Inventario Tres" con stock 15 en la sucursal "Tienda Behat"
    Cuando consulto el inventario de la sucursal
    Entonces la cantidad mostrada debe coincidir con el stock registrado
