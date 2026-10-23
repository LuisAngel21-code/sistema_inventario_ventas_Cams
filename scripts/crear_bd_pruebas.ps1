# Recrea la base de datos de pruebas (tienda_cams_test) desde el script principal
$ErrorActionPreference = "Stop"

$servidor = "127.0.0.1,1434"
$usuario = "sa"
$clave = "luis1234"

Write-Host "Eliminando y creando tienda_cams_test..."
& sqlcmd -C -S $servidor -U $usuario -P $clave -Q "IF DB_ID('tienda_cams_test') IS NOT NULL BEGIN ALTER DATABASE tienda_cams_test SET SINGLE_USER WITH ROLLBACK IMMEDIATE; DROP DATABASE tienda_cams_test; END; CREATE DATABASE tienda_cams_test;"

Write-Host "Cargando tablas y datos base..."
$origen = Join-Path $PSScriptRoot "..\database\tienda_cams_completo.sql"
$temporal = Join-Path $env:TEMP "tienda_cams_test.sql"

(Get-Content $origen -Raw) -replace 'tienda_cams', 'tienda_cams_test' | Set-Content $temporal -NoNewline
& sqlcmd -C -S $servidor -U $usuario -P $clave -i $temporal

Write-Host "Base de datos de pruebas lista."
