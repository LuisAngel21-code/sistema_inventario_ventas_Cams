# Tienda Cams

Sistema web para la gestión del inventario y las ventas de la Mueblería Cams.
Permite registrar productos, controlar el stock por sucursal, registrar compras
y ventas, consultar movimientos, generar reportes y visualizar el estado del
negocio en un panel. El acceso es diferenciado según el rol del usuario
(administrador, jefe, encargado de almacén y encargado de tienda).

## Instalación

Requisitos previos: tener instalados PHP con Composer, Node.js y SQL Server.

1. Clonar el repositorio y entrar a la carpeta del proyecto.

2. Instalar las dependencias con composer install.

3. Crear la base de datos ejecutando el archivo database/tienda_cams.sql
   en SQL Server Management Studio.

4. Copiar el archivo .env.example a .env y completar los datos de
   conexión a la base de datos.

5. Generar la clave de la aplicación con php artisan key:generate.

6. Cargar los datos iniciales con php artisan db:seed.

7. Iniciar el servidor con php artisan serve y abrir http://localhost:8000
   en el navegador.

## Pruebas

Se usan tres capas: unitarias y de integración con PHPUnit, integración BDD
con Behat y pruebas de extremo a extremo con Selenium.

Antes de ejecutar PHPUnit, preparar la base de datos de pruebas:

    powershell -File scripts/crear_bd_pruebas.ps1

Ejecutar las pruebas unitarias y de integración:

    C:\php\php.exe artisan test

Ejecutar los escenarios BDD (escriben en la base de datos real tienda_cams):

    C:\php\php.exe vendor\bin\behat

Ejecutar las pruebas de navegador (requiere la app corriendo en el puerto 8000):

    cd selenium
    mvn test

Para ejecutar las tres capas de una vez:

    powershell -File pruebas.ps1

Para limpiar los datos creados por Behat y Selenium:

    sqlcmd -C -S 127.0.0.1,1434 -U sa -P luis1234 -i scripts/limpiar_datos_prueba.sql
