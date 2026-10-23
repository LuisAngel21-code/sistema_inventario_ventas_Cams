# language: es
Característica: Consulta del historial de ventas
  Como encargado de tienda
  Quiero consultar el historial de ventas
  Para revisar y llevar un mejor control de las operaciones

  Escenario: Ver la lista de ventas
    Dado que existe una venta registrada
    Cuando consulto el historial de ventas
    Entonces debo ver la fecha, los productos y el monto total

  Escenario: Consultar el detalle de una venta
    Dado que existe una venta registrada
    Cuando consulto el detalle de la venta
    Entonces debo ver los productos vendidos

  Escenario: Filtrar las ventas por fecha
    Dado que existe una venta registrada
    Cuando filtro el historial entre "2026-01-01" y "2026-01-31"
    Entonces debo ver solo las ventas del rango
