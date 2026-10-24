param(
    [string]$Resultado = "Aprobado"
)

$ErrorActionPreference = "Stop"
$raiz = Split-Path -Parent $PSScriptRoot
$datos = Get-Content (Join-Path $raiz "docs\pruebas\casos.json") -Raw -Encoding UTF8 | ConvertFrom-Json
$salida = Join-Path $raiz "docs\pruebas"

function MapearPrioridad([string]$p) {
    switch ($p) {
        "Alta"  { "high" }
        "Media" { "normal" }
        "Baja"  { "low" }
        default { "normal" }
    }
}

# ---------- CSV para Testomat ----------
$filasTestomat = foreach ($c in $datos.casos) {
    $descripcion = @(
        "**Precondiciones:** $($c.pre)",
        "",
        "**Datos de prueba:** $($c.datos)",
        "",
        "**Pasos:**",
        ($c.pasos -replace "`n", "`r`n"),
        "",
        "**Resultado esperado:** $($c.esperado)",
        "",
        "**Criterio de aceptacion:** $($c.criterio)",
        "",
        "**Automatizacion:** $($c.automatizacion)"
    ) -join "`r`n"

    [PSCustomObject]@{
        ID          = ""
        Title       = "$($c.id) - $($c.titulo)"
        Status      = "automated"
        Folder      = $c.folder
        Emoji       = ""
        Priority    = (MapearPrioridad $c.prioridad)
        Tags        = "$($c.hu), $($c.ca), $($c.prioridad)"
        Owner       = "Luis Angel"
        Description = $descripcion
        Labels      = ""
        Issues      = ""
    }
}

$csv = Join-Path $salida "Casos_Prueba_Testomat.csv"
$filasTestomat | Export-Csv -Path $csv -NoTypeInformation -Encoding UTF8
Write-Host "CSV generado: $csv"

# ---------- Excel ----------
$excel = New-Object -ComObject Excel.Application
$excel.Visible = $false
$excel.DisplayAlerts = $false

try {
    $libro = $excel.Workbooks.Add()
    while ($libro.Worksheets.Count -gt 1) {
        $libro.Worksheets.Item($libro.Worksheets.Count).Delete()
    }

    # Hoja Casos
    $hoja = $libro.Worksheets.Item(1)
    $hoja.Name = "Casos"

    $encabezados = @("ID","HU","CA","Titulo","Prioridad","Precondiciones","Datos de prueba","Pasos","Resultado esperado","Criterio de aceptacion","Nivel","Automatizacion","Resultado","Evidencia")
    for ($i = 0; $i -lt $encabezados.Count; $i++) {
        $hoja.Cells.Item(1, $i + 1) = $encabezados[$i]
    }

    $fila = 2
    foreach ($c in $datos.casos) {
        $valores = @($c.id, $c.hu, $c.ca, $c.titulo, $c.prioridad, $c.pre, $c.datos, $c.pasos, $c.esperado, $c.criterio, $c.nivel, $c.automatizacion, $Resultado, "docs/pruebas/evidencias")
        for ($i = 0; $i -lt $valores.Count; $i++) {
            $hoja.Cells.Item($fila, $i + 1) = [string]$valores[$i]
        }
        $fila++
    }

    $rango = $hoja.Range("A1:N1")
    $rango.Font.Bold = $true
    $rango.Interior.Color = 15773696
    $rango.Font.Color = 16777215
    $hoja.Rows.Item(1).RowHeight = 30
    $hoja.Range("A1:N$($fila - 1)").WrapText = $true
    $hoja.Range("A1:N$($fila - 1)").VerticalAlignment = -4160
    $hoja.Application.ActiveWindow.SplitRow = 1
    $hoja.Application.ActiveWindow.FreezePanes = $true

    $anchos = @(8,8,8,32,10,34,40,46,40,42,26,40,12,26)
    for ($i = 0; $i -lt $anchos.Count; $i++) {
        $hoja.Columns.Item($i + 1).ColumnWidth = $anchos[$i]
    }

    # Hoja Resumen
    $resumen = $libro.Worksheets.Add()
    $resumen.Name = "Resumen"
    $datosResumen = @(
        @("Proyecto", "Tienda Cams - Sistema de inventario y ventas"),
        @("Alcance", "Sprints 1 al 4 (11 historias, 33 criterios de aceptacion)"),
        @("Total de casos", $datos.casos.Count),
        @("Sprint 1", ($datos.casos | Where-Object { $_.folder -like "Sprint 1*" }).Count),
        @("Sprint 2", ($datos.casos | Where-Object { $_.folder -like "Sprint 2*" }).Count),
        @("Sprint 3", ($datos.casos | Where-Object { $_.folder -like "Sprint 3*" }).Count),
        @("Sprint 4", ($datos.casos | Where-Object { $_.folder -like "Sprint 4*" }).Count),
        @("Prioridad alta", ($datos.casos | Where-Object { $_.prioridad -eq "Alta" }).Count),
        @("Prioridad media", ($datos.casos | Where-Object { $_.prioridad -eq "Media" }).Count),
        @("Prioridad baja", ($datos.casos | Where-Object { $_.prioridad -eq "Baja" }).Count),
        @("Pruebas unitarias", 22),
        @("Pruebas de integracion", 34),
        @("Escenarios BDD (Behat)", 33),
        @("Pruebas E2E (Selenium)", 13),
        @("Defectos registrados", $datos.defectos.Count),
        @("Defectos cerrados", ($datos.defectos | Where-Object { $_.estado -eq "Cerrado" }).Count),
        @("Resultado de ejecucion", $Resultado)
    )
    $resumen.Cells.Item(1,1) = "Indicador"
    $resumen.Cells.Item(1,2) = "Valor"
    $fila = 2
    foreach ($d in $datosResumen) {
        $resumen.Cells.Item($fila,1) = [string]$d[0]
        $resumen.Cells.Item($fila,2) = [string]$d[1]
        $fila++
    }
    $rango = $resumen.Range("A1:B1")
    $rango.Font.Bold = $true
    $rango.Interior.Color = 15773696
    $rango.Font.Color = 16777215
    $resumen.Columns.Item(1).ColumnWidth = 32
    $resumen.Columns.Item(2).ColumnWidth = 60

    # Hoja Cobertura
    $cobertura = $libro.Worksheets.Add()
    $cobertura.Name = "Cobertura"
    $encCob = @("HU","Casos","Unitarias","Integracion","BDD","E2E")
    for ($i = 0; $i -lt $encCob.Count; $i++) {
        $cobertura.Cells.Item(1, $i + 1) = $encCob[$i]
    }
    $fila = 2
    foreach ($c in $datos.cobertura) {
        $cobertura.Cells.Item($fila,1) = $c.hu
        $cobertura.Cells.Item($fila,2) = $c.casos
        $cobertura.Cells.Item($fila,3) = $c.unitarias
        $cobertura.Cells.Item($fila,4) = $c.integracion
        $cobertura.Cells.Item($fila,5) = $c.bdd
        $cobertura.Cells.Item($fila,6) = $c.e2e
        $fila++
    }
    $rango = $cobertura.Range("A1:F1")
    $rango.Font.Bold = $true
    $rango.Interior.Color = 15773696
    $rango.Font.Color = 16777215
    $cobertura.Range("A1:F$($fila - 1)").WrapText = $true
    $anchosCob = @(10,18,38,38,34,34)
    for ($i = 0; $i -lt $anchosCob.Count; $i++) {
        $cobertura.Columns.Item($i + 1).ColumnWidth = $anchosCob[$i]
    }

    $rutaXlsx = Join-Path $salida "Casos_Prueba.xlsx"
    if (Test-Path $rutaXlsx) { Remove-Item $rutaXlsx -Force }
    $libro.SaveAs($rutaXlsx, 51)
    $libro.Close($false)
    Write-Host "Excel generado: $rutaXlsx"

    # ---------- Excel de defectos ----------
    $libro2 = $excel.Workbooks.Add()
    while ($libro2.Worksheets.Count -gt 1) {
        $libro2.Worksheets.Item($libro2.Worksheets.Count).Delete()
    }
    $hojaDef = $libro2.Worksheets.Item(1)
    $hojaDef.Name = "Defectos"
    $encDef = @("ID","Titulo","Severidad","Prioridad","Estado","Caso relacionado","Descripcion","Pasos","Esperado","Obtenido","Evidencia","Commit")
    for ($i = 0; $i -lt $encDef.Count; $i++) {
        $hojaDef.Cells.Item(1, $i + 1) = $encDef[$i]
    }
    $fila = 2
    foreach ($d in $datos.defectos) {
        $valores = @($d.id, $d.titulo, $d.severidad, $d.prioridad, $d.estado, $d.caso, $d.descripcion, $d.pasos, $d.esperado, $d.obtenido, $d.evidencia, $d.commit)
        for ($i = 0; $i -lt $valores.Count; $i++) {
            $hojaDef.Cells.Item($fila, $i + 1) = [string]$valores[$i]
        }
        $fila++
    }
    $rango = $hojaDef.Range("A1:L1")
    $rango.Font.Bold = $true
    $rango.Interior.Color = 15773696
    $rango.Font.Color = 16777215
    $hojaDef.Range("A1:L$($fila - 1)").WrapText = $true
    $anchosDef = @(8,44,12,12,10,14,52,40,36,36,36,10)
    for ($i = 0; $i -lt $anchosDef.Count; $i++) {
        $hojaDef.Columns.Item($i + 1).ColumnWidth = $anchosDef[$i]
    }
    $rutaDef = Join-Path $salida "Registro_Defectos.xlsx"
    if (Test-Path $rutaDef) { Remove-Item $rutaDef -Force }
    $libro2.SaveAs($rutaDef, 51)
    $libro2.Close($false)
    Write-Host "Defectos generado: $rutaDef"
}
finally {
    $excel.Quit()
    [System.Runtime.InteropServices.Marshal]::ReleaseComObject($excel) | Out-Null
    [GC]::Collect()
}
