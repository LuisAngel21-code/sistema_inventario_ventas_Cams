-- Limpia solo los datos generados por las pruebas (Behat y Selenium)
USE tienda_cams;
GO

DECLARE @productos TABLE (id INT);

INSERT INTO @productos (id)
SELECT id FROM productos WHERE codigo LIKE 'BDD-%' OR codigo LIKE 'SEL-%';

DELETE FROM detalle_ventas WHERE producto_id IN (SELECT id FROM @productos);
DELETE FROM ventas WHERE id NOT IN (SELECT DISTINCT venta_id FROM detalle_ventas);
DELETE FROM movimientos WHERE producto_id IN (SELECT id FROM @productos);
DELETE FROM detalle_compras WHERE producto_id IN (SELECT id FROM @productos);
DELETE FROM compras WHERE id NOT IN (SELECT DISTINCT compra_id FROM detalle_compras);
DELETE FROM stock WHERE producto_id IN (SELECT id FROM @productos);
DELETE FROM productos WHERE id IN (SELECT id FROM @productos);

DELETE FROM otp_codes WHERE usuario_id IN (SELECT id FROM usuarios WHERE username LIKE '%.behat@cams.com');
DELETE FROM recovery_tokens WHERE usuario_id IN (SELECT id FROM usuarios WHERE username LIKE '%.behat@cams.com');
DELETE FROM usuarios WHERE username LIKE '%.behat@cams.com';

DELETE FROM sucursales WHERE nombre LIKE 'Sucursal Selenium%' OR nombre IN ('Sucursal Behat Norte', 'Tienda Behat');

PRINT 'Datos de prueba eliminados';
GO
