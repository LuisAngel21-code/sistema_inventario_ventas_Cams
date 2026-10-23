# Ejecuta las tres capas de pruebas
$ErrorActionPreference = "Stop"

$php = "C:\php\php.exe"
$maven = "C:\maven\apache-maven-3.9.16\bin\mvn.cmd"
$javaHome = "C:\Program Files\Amazon Corretto\jdk17.0.19_10"

Write-Host "== Unitarias y de integracion (PHPUnit) ==" -ForegroundColor Cyan
& $php artisan test

Write-Host "== Integracion BDD (Behat) ==" -ForegroundColor Cyan
& $php vendor\bin\behat

Write-Host "== E2E (Selenium) ==" -ForegroundColor Cyan
Write-Host "Recuerda tener la app corriendo en http://localhost:8000"
$env:JAVA_HOME = $javaHome
Push-Location (Join-Path $PSScriptRoot "selenium")
& $maven test
Pop-Location
