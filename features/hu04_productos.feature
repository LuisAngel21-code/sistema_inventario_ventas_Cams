# language: es
Característica: Registro de productos
  Como encargado de almacén
  Quiero registrar productos
  Para mantener actualizado el catálogo de productos

  Escenario: Registrar una cama
    Cuando registro el producto "Cama Behat" en la categoría "Camas" con precio 3500 y stock 10
    Entonces el producto debe existir en la base de datos

  Escenario: Datos obligatorios del producto
    Cuando intento registrar un producto sin datos obligatorios
    Entonces el registro del producto debe ser rechazado

  Escenario: El producto aparece en el catálogo
    Cuando registro el producto "Colchon Behat" en la categoría "Colchones" con precio 2500 y stock 5
    Entonces el producto debe aparecer en el catálogo
