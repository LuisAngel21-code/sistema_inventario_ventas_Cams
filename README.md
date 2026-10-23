# Tienda Cams

Sistema web para manejar el inventario y las ventas de la Mueblería Cams.
Permite registrar productos, controlar el stock por sucursal, registrar compras
y ventas, ver los movimientos, generar reportes y revisar el dashboard. Cada
usuario entra con su rol: administrador, jefe, encargado de almacén y encargado
de tienda.

## Qué necesitas instalar

- PHP 8.3 con las extensiones sqlsrv y pdo_sqlsrv
- ODBC Driver 18 para SQL Server
- Composer
- SQL Server 2022 y SSMS (o Docker)
- Git

No hace falta instalar Node.

## Cómo levantarlo

1. Clonar el repositorio y entrar a la carpeta.

   git clone https://github.com/LuisAngel21-code/sistema_inventario_ventas_Cams.git
   cd sistema_inventario_ventas_Cams

2. Instalar las dependencias.

   composer install

3. Crear el archivo .env copiando .env.example y poner los datos de tu
   SQL Server (usuario, contraseña y puerto).

4. Abrir SSMS y ejecutar el archivo database/tienda_cams_completo.sql.
   Eso crea la base de datos tienda_cams con sus tablas y datos de prueba.

5. Generar la clave de la aplicación.

   php artisan key:generate

6. Levantar el servidor.

   php artisan serve

7. Abrir http://localhost:8000 y entrar con admin@cams.com y la clave cams2026.
   La primera vez pide escanear un código QR con Google Authenticator.

## Usuarios de prueba

Todos usan la clave cams2026.

- admin@cams.com (administrador)
- jefe@cams.com (jefe)
- almacen@cams.com (encargado de almacén)
- tienda@cams.com (encargado de tienda)

## Pruebas

Primero preparar la base de datos de pruebas.

   powershell -File scripts/crear_bd_pruebas.ps1

Pruebas unitarias y de integración.

   C:\php\php.exe artisan test

Pruebas BDD con Behat (guardan datos en la base real).

   C:\php\php.exe vendor\bin\behat

Pruebas de navegador con Selenium (la app debe estar corriendo).

   cd selenium
   mvn test

Para correr las tres de una vez.

   powershell -File pruebas.ps1

Para borrar los datos que dejan Behat y Selenium.

   sqlcmd -C -S 127.0.0.1,1434 -U sa -P luis1234 -i scripts/limpiar_datos_prueba.sql
