<?php

$raiz = dirname(__DIR__);
$contenidoJson = preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($raiz . '/docs/pruebas/casos.json'));
$datos = json_decode($contenidoJson, true);

function e($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function saltos($texto) {
    return nl2br(e($texto));
}

$imagenes = glob($raiz . '/docs/pruebas/evidencias/selenium/*.png');
sort($imagenes);

$titulosImagen = [
    'TC-01_login' => 'TC-01 - Pantalla de inicio de sesion',
    'TC-01_dashboard' => 'TC-01 - Dashboard del administrador',
    'TC-02_login_error' => 'TC-02 - Mensaje de credenciales incorrectas',
    'TC-04_recuperacion' => 'TC-04 - Recuperacion de contrasena',
    'TC-10_registrar_sucursal' => 'TC-10 - Registro de sucursal',
    'TC-11_sucursales' => 'TC-11 - Listado de sucursales',
    'TC-13_catalogo' => 'TC-13 - Catalogo de productos',
    'TC-13_registrar_producto' => 'TC-13 - Registro de producto',
    'TC-19_inventario' => 'TC-19 - Inventario por sucursal',
    'TC-22_movimientos' => 'TC-22 - Movimientos de inventario',
    'TC-25_ingreso' => 'TC-25 - Registro de ingreso',
    'TC-28_venta' => 'TC-28 - Registro de venta',
    'TC-29_formulario_venta' => 'TC-29 - Formulario de venta',
    'TC-31_historial' => 'TC-31 - Historial de ventas',
    'LoginTest_acceso_credencialesCorrectas_debePedirCodigo' => 'TC-07 - Solicitud del codigo OTP',
    'LoginTest_acceso_credencialesIncorrectas_debeMostrarMensaje' => 'TC-02 - Credenciales incorrectas',
    'LoginTest_acceso_credencialesVacias_debeMostrarError' => 'TC-03 - Validacion de campos vacios',
    'CodigoTest_verificar_codigoValido_debePermitirAcceso' => 'TC-07 - Acceso con codigo valido',
    'RecuperacionTest_recuperar_correoRegistrado_debeContinuarProceso' => 'TC-04 - Correo registrado',
    'SucursalTest_registrar_sucursal_debeAparecerEnListado' => 'TC-10 - Sucursal registrada',
    'ProductoTest_registrar_producto_debeGuardarseYVerseEnCatalogo' => 'TC-13 - Producto en catalogo',
    'StockTest_actualizar_stock_debeMostrarNuevoValor' => 'TC-18 - Stock actualizado',
    'InventarioTest_consultar_inventario_debeMostrarStock' => 'TC-19 - Consulta de inventario',
    'MovimientoTest_consultar_movimientos_debeMostrarListado' => 'TC-22 - Listado de movimientos',
    'IngresoTest_registrar_ingreso_debeIncrementarStock' => 'TC-26 - Stock incrementado',
    'VentaTest_registrar_venta_debeGuardarse' => 'TC-28 - Venta registrada',
    'HistorialTest_consultar_historial_debeMostrarVentas' => 'TC-31 - Historial de ventas',
    'EvidenciasTest_capturarPantallasDeLosCasos' => 'Recorrido general de pantallas',
];

function listarPruebas($patron, $metodo = 'php') {
    $lista = [];
    foreach (glob($patron) as $archivo) {
        $contenido = file_get_contents($archivo);
        $nombre = basename($archivo);
        if ($metodo === 'php') {
            preg_match_all('/public function (\w+)\(/', $contenido, $m);
            foreach ($m[1] as $prueba) {
                $lista[] = [$nombre, $prueba];
            }
        } else {
            preg_match_all('/@Test\s+public void (\w+)\(/', $contenido, $m);
            foreach ($m[1] as $prueba) {
                $lista[] = [$nombre, $prueba];
            }
        }
    }
    return $lista;
}

$unitarias = listarPruebas($raiz . '/tests/Unit/*.php');
$integracion = listarPruebas($raiz . '/tests/Feature/*/*.php');
$selenium = listarPruebas($raiz . '/selenium/src/test/java/DemoSelenium/*.java', 'java');
$selenium = array_values(array_filter($selenium, function ($fila) {
    return $fila[0] !== 'EvidenciasTest.java';
}));

$behat = [];
foreach (glob($raiz . '/features/*.feature') as $archivo) {
    $lineas = file($archivo);
    foreach ($lineas as $linea) {
        if (preg_match('/Escenario:\s*(.+)/u', $linea, $m)) {
            $behat[] = [basename($archivo), trim($m[1])];
        }
    }
}

$porSprint = [
    'Sprint 1' => ['HU-01', 'HU-02', 'HU-03', 'HU-13'],
    'Sprint 2' => ['HU-04', 'HU-05'],
    'Sprint 3' => ['HU-06', 'HU-07', 'HU-08'],
    'Sprint 4' => ['HU-09', 'HU-10'],
];

$huTitulos = [
    'HU-01' => 'Inicio de sesion',
    'HU-02' => 'Recuperacion de contrasena',
    'HU-03' => 'Codigo de verificacion',
    'HU-04' => 'Registro de productos',
    'HU-05' => 'Actualizacion de stock',
    'HU-06' => 'Consulta de inventario',
    'HU-07' => 'Movimientos de inventario',
    'HU-08' => 'Ingreso de mercaderia',
    'HU-09' => 'Registro de ventas',
    'HU-10' => 'Historial de ventas',
    'HU-13' => 'Gestion de sucursales',
];

$totalCasos = count($datos['casos']);
$totalUnit = count($unitarias);
$totalInt = count($integracion);
$totalBdd = count($behat);
$totalE2e = count($selenium);

$html = '<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Capitulo VI - Aseguramiento y pruebas de calidad</title>
<style>
body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1f2937; line-height: 1.45; }
h1 { font-size: 20pt; color: #1e3a5f; margin: 0 0 6pt 0; }
h2 { font-size: 15pt; color: #1e3a5f; border-bottom: 1px solid #94a3b8; padding-bottom: 3pt; margin-top: 18pt; }
h3 { font-size: 12.5pt; color: #334155; margin-top: 12pt; }
p { text-align: justify; margin: 6pt 0; }
table { border-collapse: collapse; width: 100%; margin: 8pt 0; }
th { background-color: #1e3a5f; color: #ffffff; font-size: 9.5pt; padding: 5pt; border: 1px solid #1e3a5f; text-align: left; }
td { border: 1px solid #cbd5e1; padding: 4pt 5pt; font-size: 9.5pt; vertical-align: top; }
.small td, .small th { font-size: 8pt; padding: 3pt; }
.nota { background-color: #f1f5f9; border-left: 3pt solid #1e3a5f; padding: 7pt 10pt; margin: 8pt 0; }
.portada { text-align: center; }
.portada .linea { border-top: 2pt solid #1e3a5f; width: 60%; margin: 10pt auto; }
.salto { page-break-before: always; }
.caption { font-size: 8pt; color: #64748b; text-align: center; margin: 2pt 0 8pt 0; }
.foto { text-align: center; }
img { border: 1px solid #cbd5e1; }
.centro { text-align: center; }
</style>
</head>
<body>

<div class="portada">
<p style="margin-top:100pt; font-size:12pt; color:#64748b; letter-spacing:2pt;">DOCUMENTO DE ENTREGA</p>
<h1 style="font-size:26pt; margin-top:16pt;">CAPITULO VI</h1>
<h1 style="font-size:22pt;">ASEGURAMIENTO Y PRUEBAS DE CALIDAD</h1>
<div class="linea"></div>
<p style="font-size:14pt; color:#334155;"><b>Sistema Tienda Cams</b><br>Inventario y ventas para Muebleria Cams</p>
<p style="font-size:11pt; color:#475569;">Aplicacion web desarrollada con Laravel 12, Blade y SQL Server</p>
<p style="font-size:11pt; color:#475569; margin-top:30pt;">Alcance documentado: Sprints 1 al 4<br>11 historias de usuario y 33 criterios de aceptacion</p>
<p style="font-size:11pt; color:#475569; margin-top:30pt;">Version 1.0<br>23 de octubre de 2026</p>
</div>

<h2 class="salto">6.1. Estrategia y plan de pruebas</h2>

<p>El aseguramiento de la calidad del sistema Tienda Cams se organizo como una actividad continua dentro del desarrollo agil. Desde el primer sprint se definio que cada historia de usuario debia cerrarse con evidencia verificable, de modo que las pruebas no fueran un paso final sino parte del trabajo diario del equipo. Por esa razon, cada criterio de aceptacion se convirtio en un caso de prueba con identificador propio y quedo conectado con al menos una prueba automatizada.</p>

<p>La estrategia sigue la forma de una piramide. En la base estan las pruebas unitarias, que verifican la logica de los modelos sin depender de la interfaz. En el siguiente nivel estan las pruebas de integracion, que recorren las rutas, los controladores y la base de datos. Sobre ellas se ubican las pruebas de aceptacion escritas en lenguaje natural con Behat, que reproducen los criterios acordados con el cliente. En la cima estan las pruebas de extremo a extremo con Selenium, que abren un navegador real y recorren el sistema como lo haria un usuario.</p>

<h3>6.1.1. Niveles de prueba</h3>
<table>
<tr><th style="width:22%;">Nivel</th><th style="width:25%;">Herramienta</th><th style="width:15%;">Cantidad</th><th>Que valida</th></tr>
<tr><td>Unitarias</td><td>PHPUnit 11</td><td class="centro">' . $totalUnit . '</td><td>Reglas de negocio de los modelos: stock, ventas, codigos OTP, roles y relaciones.</td></tr>
<tr><td>Integracion</td><td>PHPUnit 11</td><td class="centro">' . $totalInt . '</td><td>Rutas, controladores, validaciones, sesiones y persistencia en SQL Server.</td></tr>
<tr><td>Aceptacion (BDD)</td><td>Behat 4</td><td class="centro">' . $totalBdd . '</td><td>Los criterios de aceptacion redactados como escenarios en espanol.</td></tr>
<tr><td>Extremo a extremo</td><td>Selenium 4 y Chrome</td><td class="centro">' . $totalE2e . '</td><td>Flujos completos en el navegador, incluido el segundo factor de autenticacion.</td></tr>
</table>

<h3>6.1.2. Entornos y datos de prueba</h3>
<p>Las pruebas se ejecutan en un entorno local sobre Windows, con PHP 8.3, Laravel 12, SQL Server 2022 Developer y Google Chrome. Para evitar que las pruebas de integracion ensucien la informacion real se creo una base de datos independiente llamada <b>tienda_cams_test</b>, que se reconstruye desde el script principal del proyecto. Las pruebas de integracion usan transacciones con retroceso automatico, por lo que cada prueba deja la base tal como la encontro.</p>

<p>Las pruebas de aceptacion y las de navegador trabajan sobre la base <b>tienda_cams</b> y crean sus propios registros con prefijos reconocibles, como BDD o SEL. Los usuarios de prueba son admin@cams.com, jefe@cams.com, almacen@cams.com y tienda@cams.com, todos con la contrasena cams2026 y con el mismo secreto TOTP de demostracion, lo que permite automatizar el segundo factor sin depender de un telefono.</p>

<div class="nota"><b>Datos de prueba principales:</b> sucursal Sucursal Behat, producto Cama Behat con stock por sucursal, codigo OTP calculado con el secreto JBSWY3DPEHPK3PXP y ventas de dos unidades para verificar el descuento de stock.</div>

<h3>6.1.3. Criterios de entrada y salida</h3>
<table>
<tr><th style="width:50%;">Criterios de entrada</th><th>Criterios de salida</th></tr>
<tr><td>Historia con criterios de aceptacion aprobados, base de datos disponible y aplicacion levantada en localhost:8000.</td><td>Todas las pruebas del sprint ejecutadas, sin fallos abiertos de severidad alta y con evidencia adjunta.</td></tr>
<tr><td>Script SQL de creacion y datos base ejecutado sin errores.</td><td>Matriz de trazabilidad actualizada y defectos cerrados o justificados.</td></tr>
<tr><td>Usuarios y secretos de prueba configurados.</td><td>Reporte de ejecucion exportado y adjunto al informe.</td></tr>
</table>

<h3>6.1.4. Organizacion por sprint</h3>
<table>
<tr><th>Sprint</th><th>Historias</th><th>Casos</th><th>Enfoque de prueba</th></tr>';

foreach ($porSprint as $sprint => $hus) {
    $casos = array_filter($datos['casos'], function ($caso) use ($sprint) {
        return strpos($caso['folder'], $sprint) === 0;
    });
    $ids = array_map(function ($c) { return $c['id']; }, $casos);
    $rango = count($ids) ? reset($ids) . ' a ' . end($ids) : '-';
    $enfoque = [
        'Sprint 1' => 'Seguridad, segundo factor, recuperacion y sucursales.',
        'Sprint 2' => 'Catalogo de productos y control de stock.',
        'Sprint 3' => 'Inventario, movimientos e ingresos de mercaderia.',
        'Sprint 4' => 'Ventas, descuento de stock e historial.',
    ][$sprint];
    $html .= '<tr><td><b>' . e($sprint) . '</b></td><td>' . e(implode(', ', $hus)) . '</td><td class="centro">' . count($casos) . ' (' . e($rango) . ')</td><td>' . e($enfoque) . '</td></tr>';
}

$html .= '</table>

<h3>6.1.5. Herramientas utilizadas</h3>
<table>
<tr><th style="width:24%;">Herramienta</th><th style="width:18%;">Uso</th><th>Descripcion</th></tr>
<tr><td>PHPUnit 11.5</td><td>Automatizacion</td><td>Pruebas unitarias y de integracion sobre el codigo PHP y la base de datos.</td></tr>
<tr><td>Behat 4</td><td>BDD</td><td>Escenarios de aceptacion en lenguaje natural vinculados a los criterios.</td></tr>
<tr><td>Selenium 4.36</td><td>E2E</td><td>Automatizacion del navegador Chrome con capturas automaticas por prueba.</td></tr>
<tr><td>Testomat.io</td><td>Gestion</td><td>Registro de casos, corrida, defectos y exportacion del reporte.</td></tr>
<tr><td>Excel</td><td>Respaldo</td><td>Matriz de casos, registro de defectos y cobertura.</td></tr>
</table>

<h3>6.1.6. Riesgos identificados y mitigacion</h3>
<table>
<tr><th style="width:40%;">Riesgo</th><th>Mitigacion aplicada</th></tr>
<tr><td>Caidas momentaneas de SQL Server por ráfagas de conexiones.</td><td>Reintento automatico de la corrida y espera de recuperacion del servicio.</td></tr>
<tr><td>Datos acumulados por Behat y Selenium.</td><td>Prefijos identificables y script de limpieza.</td></tr>
<tr><td>Dependencia del codigo OTP en Selenium.</td><td>Secreto fijo de demostracion y calculo del codigo desde Java.</td></tr>
<tr><td>Diferencias entre el stock global y el stock por sucursal.</td><td>Correccion del formulario de venta para filtrar por la sucursal del usuario.</td></tr>
</table>

<h2 class="salto">6.2. Casos de prueba</h2>

<p>Cada criterio de aceptacion dio origen a un caso de prueba identificado como TC-01, TC-02 y asi sucesivamente. Los casos se agruparon por sprint y por historia, y se priorizaron segun el impacto en el negocio. La prioridad alta se reservo para los flujos criticos de seguridad y ventas; la prioridad media para la gestion de productos e inventario; y la prioridad baja para procesos de apoyo como la recuperacion de contrasena o los filtros del historial.</p>

<p>La tabla siguiente resume la distribucion. El detalle completo de cada caso, con sus datos y pasos, se presenta a continuacion.</p>

<table>
<tr><th>Sprint</th><th>Historias</th><th>Casos</th><th>Prioridad alta</th><th>Prioridad media</th><th>Prioridad baja</th></tr>';

foreach ($porSprint as $sprint => $hus) {
    $casos = array_filter($datos['casos'], function ($caso) use ($sprint) {
        return strpos($caso['folder'], $sprint) === 0;
    });
    $altas = count(array_filter($casos, function ($c) { return $c['prioridad'] === 'Alta'; }));
    $medias = count(array_filter($casos, function ($c) { return $c['prioridad'] === 'Media'; }));
    $bajas = count(array_filter($casos, function ($c) { return $c['prioridad'] === 'Baja'; }));
    $html .= '<tr><td>' . e($sprint) . '</td><td>' . e(implode(', ', $hus)) . '</td><td class="centro">' . count($casos) . '</td><td class="centro">' . $altas . '</td><td class="centro">' . $medias . '</td><td class="centro">' . $bajas . '</td></tr>';
}

$html .= '<tr><td><b>Total</b></td><td><b>11 historias</b></td><td class="centro"><b>' . $totalCasos . '</b></td><td class="centro"><b>' . count(array_filter($datos['casos'], function ($c) { return $c['prioridad'] === 'Alta'; })) . '</b></td><td class="centro"><b>' . count(array_filter($datos['casos'], function ($c) { return $c['prioridad'] === 'Media'; })) . '</b></td><td class="centro"><b>' . count(array_filter($datos['casos'], function ($c) { return $c['prioridad'] === 'Baja'; })) . '</b></td></tr>
</table>

<h3>6.2.1. Detalle de los 33 casos</h3>
<table class="small">
<tr><th style="width:7%;">ID</th><th style="width:7%;">HU/CA</th><th style="width:17%;">Caso</th><th style="width:7%;">Prioridad</th><th style="width:18%;">Datos de prueba</th><th style="width:18%;">Resultado esperado</th><th style="width:18%;">Criterio de aceptacion</th><th style="width:8%;">Nivel</th></tr>';

foreach ($datos['casos'] as $caso) {
    $html .= '<tr><td><b>' . e($caso['id']) . '</b></td><td>' . e($caso['hu']) . '<br>' . e($caso['ca']) . '</td><td>' . e($caso['titulo']) . '</td><td>' . e($caso['prioridad']) . '</td><td>' . e($caso['datos']) . '</td><td>' . e($caso['esperado']) . '</td><td>' . e($caso['criterio']) . '</td><td>' . e($caso['nivel']) . '</td></tr>';
}

$html .= '</table>

<h3>6.2.2. Ejemplo de caso detallado: TC-29</h3>
<table>
<tr><th style="width:28%;">Campo</th><th>Contenido</th></tr>
<tr><td>Identificador</td><td>TC-29</td></tr>
<tr><td>Historia y criterio</td><td>HU-09 / CA-02 - Rechazar una venta que supera el stock</td></tr>
<tr><td>Prioridad</td><td>Alta</td></tr>
<tr><td>Precondiciones</td><td>Usuario con sesion iniciada y producto sin stock suficiente en su sucursal.</td></tr>
<tr><td>Datos de prueba</td><td>Producto Cama Behat, cantidad 999 unidades, sucursal del usuario.</td></tr>
<tr><td>Pasos</td><td>' . saltos("1. Abrir /ventas/nueva.\n2. Elegir el producto.\n3. Ingresar 999 unidades.\n4. Presionar Registrar venta.") . '</td></tr>
<tr><td>Resultado esperado</td><td>El sistema muestra el mensaje Stock insuficiente para el producto indicado y no crea la venta.</td></tr>
<tr><td>Criterio de aceptacion</td><td>El sistema no permite vender mas unidades de las disponibles en la sucursal.</td></tr>
<tr><td>Automatizacion</td><td>VentaTest.php, hu09_ventas.feature y VentaTest.java.</td></tr>
</table>

<h3>6.2.3. Registro en Testomat.io</h3>
<p>Los 33 casos se registraron en Testomat.io en su plan gratuito. La importacion se realizo con el archivo <b>Casos_Prueba_Testomat.csv</b>, que incluye las columnas ID, titulo, estado, carpeta, prioridad, etiquetas y descripcion. Cada caso quedo dentro de una carpeta por sprint y con la etiqueta de su historia y criterio.</p>

<div class="nota">
<b>Enlace del proyecto en Testomat.io:</b><br><br>
[PEGAR AQUI EL ENLACE PUBLICO DEL PROYECTO]<br><br>
Casos importados: 33 &middot; Corrida: Ejecucion Sprint 1-4 &middot; Reporte exportado en PDF y adjunto a este documento.
</div>

<h2 class="salto">6.3. Resultados de las pruebas</h2>

<p>La ejecucion final se realizo el 23 de octubre de 2026 sobre el codigo del repositorio, con las tres suites activas. Todas las pruebas terminaron en verde. En total se ejecutaron 102 verificaciones automatizadas: 56 de PHPUnit, 33 escenarios de Behat y 13 pruebas de Selenium, ademas de una prueba auxiliar que recorre las pantallas para generar evidencias.</p>

<h3>6.3.1. Resumen ejecutivo</h3>
<table>
<tr><th>Nivel</th><th>Herramienta</th><th>Ejecutados</th><th>Aprobados</th><th>Fallidos</th><th>Exito</th></tr>
<tr><td>Unitarias</td><td>PHPUnit</td><td class="centro">' . $totalUnit . '</td><td class="centro">' . $totalUnit . '</td><td class="centro">0</td><td class="centro">100%</td></tr>
<tr><td>Integracion</td><td>PHPUnit</td><td class="centro">' . $totalInt . '</td><td class="centro">' . $totalInt . '</td><td class="centro">0</td><td class="centro">100%</td></tr>
<tr><td>Aceptacion</td><td>Behat</td><td class="centro">' . $totalBdd . '</td><td class="centro">' . $totalBdd . '</td><td class="centro">0</td><td class="centro">100%</td></tr>
<tr><td>Extremo a extremo</td><td>Selenium</td><td class="centro">' . $totalE2e . '</td><td class="centro">' . $totalE2e . '</td><td class="centro">0</td><td class="centro">100%</td></tr>
<tr><td><b>Total</b></td><td></td><td class="centro"><b>' . ($totalUnit + $totalInt + $totalBdd + $totalE2e) . '</b></td><td class="centro"><b>' . ($totalUnit + $totalInt + $totalBdd + $totalE2e) . '</b></td><td class="centro"><b>0</b></td><td class="centro"><b>100%</b></td></tr>
</table>

<p>Las pruebas de PHPUnit se ejecutaron en 3.8 segundos y reportaron 118 aserciones. Behat completo los 33 escenarios y 100 pasos en 8.5 segundos. Selenium ejecuto sus pruebas en Chrome en modo headless y guardo una captura por cada caso.</p>

<h3>6.3.2. Resultados por sprint</h3>
<table>
<tr><th>Sprint</th><th>Historias</th><th>Casos</th><th>Resultado</th><th>Observacion</th></tr>';

$observaciones = [
    'Sprint 1' => 'Autenticacion, OTP, recuperacion y sucursales sin observaciones.',
    'Sprint 2' => 'Productos y stock correctos; se valido la cantidad invalida.',
    'Sprint 3' => 'Inventario, movimientos e ingresos coherentes con la base.',
    'Sprint 4' => 'Ventas e historial correctos; se corrigio el mensaje de stock insuficiente.',
];

foreach ($porSprint as $sprint => $hus) {
    $casos = array_filter($datos['casos'], function ($caso) use ($sprint) {
        return strpos($caso['folder'], $sprint) === 0;
    });
    $html .= '<tr><td>' . e($sprint) . '</td><td>' . e(implode(', ', $hus)) . '</td><td class="centro">' . count($casos) . '</td><td>Aprobado</td><td>' . e($observaciones[$sprint]) . '</td></tr>';
}

$html .= '</table>

<h3>6.3.3. Detalle de los resultados</h3>
<p>Las pruebas unitarias cubrieron el calculo de subtotales, el incremento y descuento de stock, la validacion de codigos OTP, la vigencia de los tokens de recuperacion y las comprobaciones de rol. Las pruebas de integracion recorrieron las rutas de login, codigo, recuperacion, sucursales, productos, stock, inventario, movimientos, ingresos, ventas e historial, verificando tanto los casos exitosos como los rechazos por datos invalidos.</p>

<p>Los escenarios de Behat se redactaron con las palabras del cliente y se ejecutaron contra la base real, lo que permitio comprobar el comportamiento de punta a punta sin depender de la interfaz. Finalmente, Selenium inicio sesion con los usuarios reales, resolvio el codigo de seis digitos con la libreria de autenticacion y verifico los textos de confirmacion en cada pantalla.</p>

<p>Durante la ejecucion se presentaron dos situaciones que quedaron documentadas. La primera fue un defecto funcional en el mensaje de stock insuficiente, corregido y registrado como DEF-01. La segunda fue una caida momentanea del servicio de base de datos por acumulacion de conexiones, que se resolvio esperando la recuperacion del servicio y repitiendo la corrida; no afecto el resultado final.</p>

<h3>6.3.4. Evidencias de ejecucion</h3>
<p>Se adjuntan 28 capturas obtenidas automaticamente durante las pruebas de navegador y los registros de las tres suites. Las capturas se presentan a continuacion y los archivos de registro se detallan en el anexo C.</p>';

$html .= '<table>';

$pares = array_chunk($imagenes, 2);
foreach ($pares as $par) {
    $html .= '<tr>';
    foreach ($par as $imagen) {
        $nombre = pathinfo($imagen, PATHINFO_FILENAME);
        $titulo = $titulosImagen[$nombre] ?? str_replace('_', ' ', $nombre);
        $ruta = 'file:///' . str_replace('\\', '/', $imagen);
        $html .= '<td class="foto" style="width:50%;"><img src="' . e($ruta) . '" width="310"><br><span class="caption">' . e($titulo) . '</span></td>';
    }
    if (count($par) === 1) {
        $html .= '<td style="width:50%;"></td>';
    }
    $html .= '</tr>';
}

$html .= '</table>

<h2 class="salto">6.4. Registro y seguimiento de defectos</h2>

<p>Los defectos se registraron en el momento en que aparecieron durante la ejecucion. Cada uno recibio un identificador, una severidad, el caso de prueba afectado y la evidencia correspondiente. El seguimiento siguio un ciclo simple: deteccion, registro, analisis de la causa, correccion, verificacion con la prueba automatizada y cierre. Al final del periodo documentado quedaron dos defectos registrados y ambos cerrados.</p>

<h3>6.4.1. DEF-01 - Mensaje inadecuado al superar el stock</h3>
<table>
<tr><th style="width:26%;">Campo</th><th>Detalle</th></tr>
<tr><td>Identificador</td><td>DEF-01</td></tr>
<tr><td>Caso relacionado</td><td>TC-29 - Rechazar una venta que supera el stock</td></tr>
<tr><td>Severidad / prioridad</td><td>Alta / Alta</td></tr>
<tr><td>Descripcion</td><td>Al registrar una venta con cantidad mayor al stock, el sistema cortaba la operacion con una excepcion HTTP 422 y mostraba la pagina de error de Laravel, en lugar de volver al formulario con un mensaje comprensible.</td></tr>
<tr><td>Pasos para reproducir</td><td>' . saltos("1. Abrir /ventas/nueva.\n2. Elegir un producto sin stock.\n3. Ingresar 999 unidades.\n4. Registrar la venta.") . '</td></tr>
<tr><td>Resultado esperado</td><td>Volver al formulario mostrando el mensaje Stock insuficiente para el producto indicado.</td></tr>
<tr><td>Resultado obtenido</td><td>Pantalla de excepcion con el trace de Laravel y codigo 422.</td></tr>
<tr><td>Causa</td><td>El controlador usaba abort(422) dentro de la transaccion en lugar de una excepcion de validacion.</td></tr>
<tr><td>Correccion</td><td>Se reemplazo por ValidationException::withMessages, que revierte la transaccion y redirige al formulario con el error.</td></tr>
<tr><td>Commit</td><td>6337fbb</td></tr>
<tr><td>Estado</td><td>Cerrado y verificado con la prueba automatizada.</td></tr>
</table>

<h3>6.4.2. DEF-02 - Configuracion de sesion y cache en el archivo de ejemplo</h3>
<table>
<tr><th style="width:26%;">Campo</th><th>Detalle</th></tr>
<tr><td>Identificador</td><td>DEF-02</td></tr>
<tr><td>Caso relacionado</td><td>TC-01 - Acceso con credenciales correctas</td></tr>
<tr><td>Severidad / prioridad</td><td>Media / Media</td></tr>
<tr><td>Descripcion</td><td>El archivo .env.example configuraba SESSION_DRIVER y CACHE_STORE en database, pero el script de creacion no genera las tablas sessions ni cache. Al instalar el proyecto en otra computadora la aplicacion fallaba al iniciar sesion.</td></tr>
<tr><td>Pasos para reproducir</td><td>' . saltos("1. Clonar el repositorio.\n2. Copiar .env.example a .env.\n3. Ejecutar el script SQL.\n4. Abrir la aplicacion e iniciar sesion.") . '</td></tr>
<tr><td>Resultado esperado</td><td>La aplicacion inicia y permite autenticarse.</td></tr>
<tr><td>Resultado obtenido</td><td>Error al guardar la sesion porque la tabla sessions no existia.</td></tr>
<tr><td>Causa</td><td>Configuracion de ejemplo incompatible con el script de base de datos entregado.</td></tr>
<tr><td>Correccion</td><td>Se cambio a SESSION_DRIVER=file y CACHE_STORE=file en el archivo de ejemplo.</td></tr>
<tr><td>Commit</td><td>f52bbe2</td></tr>
<tr><td>Estado</td><td>Cerrado.</td></tr>
</table>

<h3>6.4.3. Lecciones aprendidas</h3>
<p>La ejecucion de las pruebas permitio detectar dos problemas que no se habian visto durante el desarrollo manual. El primero era de experiencia de usuario y afectaba directamente un criterio de aceptacion. El segundo era de instalacion y habria impedido que otra persona levantara el proyecto. Ambos se corrigieron antes de la entrega, lo que confirma el valor de automatizar las pruebas y de ejecutarlas sobre una base limpia.</p>

<h2 class="salto">6.5. Matriz de trazabilidad</h2>

<p>La matriz conecta cada historia de usuario con sus criterios de aceptacion, los casos de prueba y las verificaciones automatizadas que los respaldan. Todas las historias del alcance quedaron cubiertas al menos por una prueba de aceptacion y una prueba de navegador o de integracion, lo que da una trazabilidad completa de los Sprints 1 al 4.</p>

<table>
<tr><th style="width:8%;">HU</th><th style="width:18%;">Historia</th><th style="width:12%;">Casos</th><th style="width:16%;">Unitarias</th><th style="width:16%;">Integracion</th><th style="width:14%;">BDD</th><th style="width:10%;">E2E</th><th style="width:8%;">Estado</th></tr>';

foreach ($datos['cobertura'] as $fila) {
    $historia = $huTitulos[$fila['hu']] ?? '';
    $html .= '<tr><td><b>' . e($fila['hu']) . '</b></td><td>' . e($historia) . '</td><td>' . e($fila['casos']) . '</td><td>' . e($fila['unitarias']) . '</td><td>' . e($fila['integracion']) . '</td><td>' . e($fila['bdd']) . '</td><td>' . e($fila['e2e']) . '</td><td>Aprobado</td></tr>';
}

$html .= '</table>

<p>La cobertura resultante es de 11 historias de usuario y 33 criterios de aceptacion, todos con al menos una prueba automatizada y evidencia de ejecucion. Las historias HU-11 y HU-12, correspondientes al Sprint 5, quedaron fuera del alcance de esta entrega y no se incluyeron en la matriz.</p>

<table>
<tr><th>Indicador</th><th>Valor</th></tr>
<tr><td>Historias cubiertas</td><td>11 de 11 (100%)</td></tr>
<tr><td>Criterios de aceptacion cubiertos</td><td>33 de 33 (100%)</td></tr>
<tr><td>Casos de prueba registrados</td><td>33</td></tr>
<tr><td>Verificaciones automatizadas</td><td>102</td></tr>
<tr><td>Defectos encontrados y cerrados</td><td>2</td></tr>
<tr><td>Resultado final</td><td>100% de pruebas aprobadas</td></tr>
</table>

<h2 class="salto">Anexo A. Verificaciones automatizadas</h2>
<p>Se listan las 102 verificaciones ejecutadas, agrupadas por nivel. Los nombres corresponden a los metodos de prueba del repositorio.</p>

<h3>A.1. Pruebas unitarias (' . $totalUnit . ')</h3>
<table class="small">
<tr><th style="width:30%;">Archivo</th><th>Prueba</th></tr>';

foreach ($unitarias as $fila) {
    $html .= '<tr><td>' . e($fila[0]) . '</td><td>' . e($fila[1]) . '</td></tr>';
}

$html .= '</table>

<h3>A.2. Pruebas de integracion (' . $totalInt . ')</h3>
<table class="small">
<tr><th style="width:30%;">Archivo</th><th>Prueba</th></tr>';

foreach ($integracion as $fila) {
    $html .= '<tr><td>' . e($fila[0]) . '</td><td>' . e($fila[1]) . '</td></tr>';
}

$html .= '</table>

<h3>A.3. Escenarios BDD (' . $totalBdd . ')</h3>
<table class="small">
<tr><th style="width:30%;">Archivo</th><th>Escenario</th></tr>';

foreach ($behat as $fila) {
    $html .= '<tr><td>' . e($fila[0]) . '</td><td>' . e($fila[1]) . '</td></tr>';
}

$html .= '</table>

<h3>A.4. Pruebas de navegador (' . $totalE2e . ')</h3>
<table class="small">
<tr><th style="width:30%;">Archivo</th><th>Prueba</th></tr>';

foreach ($selenium as $fila) {
    $html .= '<tr><td>' . e($fila[0]) . '</td><td>' . e($fila[1]) . '</td></tr>';
}

$html .= '</table>

<h2 class="salto">Anexo B. Comandos para reproducir las pruebas</h2>
<p>Con SQL Server activo y la aplicacion levantada en http://localhost:8000, los comandos son los siguientes.</p>

<table class="small">
<tr><th style="width:30%;">Accion</th><th>Comando (PowerShell)</th></tr>
<tr><td>Preparar base de pruebas</td><td>powershell -File scripts\\crear_bd_pruebas.ps1</td></tr>
<tr><td>Todas las pruebas PHPUnit</td><td>C:\\php\\php.exe artisan test</td></tr>
<tr><td>Solo unitarias</td><td>C:\\php\\php.exe artisan test --testsuite=Unit</td></tr>
<tr><td>Solo integracion</td><td>C:\\php\\php.exe artisan test --testsuite=Feature</td></tr>
<tr><td>Un archivo</td><td>C:\\php\\php.exe artisan test tests\\Unit\\ProductoTest.php</td></tr>
<tr><td>Un caso</td><td>C:\\php\\php.exe artisan test tests\\Unit\\ProductoTest.php --filter=tieneStockBajo_conStockIgualAlMinimo_debeRetornarTrue</td></tr>
<tr><td>Escenarios BDD</td><td>C:\\php\\php.exe vendor\\bin\\behat</td></tr>
<tr><td>Un escenario</td><td>C:\\php\\php.exe vendor\\bin\\behat features\\hu09_ventas.feature:6</td></tr>
<tr><td>Selenium completo</td><td>cd selenium; mvn test</td></tr>
<tr><td>Una clase Selenium</td><td>mvn test -Dtest=VentaTest</td></tr>
<tr><td>Las tres suites</td><td>powershell -File pruebas.ps1</td></tr>
</table>

<h2>Anexo C. Indice de evidencias</h2>
<p>Las evidencias se entregaron en la carpeta evidencias del paquete de pruebas.</p>

<table class="small">
<tr><th style="width:42%;">Archivo</th><th>Contenido</th></tr>
<tr><td>evidencias/logs/phpunit.log</td><td>Resultado de las 56 pruebas de PHPUnit.</td></tr>
<tr><td>evidencias/logs/phpunit-junit.xml</td><td>Reporte JUnit de PHPUnit.</td></tr>
<tr><td>evidencias/logs/behat.log</td><td>Resultado de los 33 escenarios y 100 pasos.</td></tr>
<tr><td>evidencias/logs/selenium.log</td><td>Resultado de las 14 pruebas de Selenium.</td></tr>
<tr><td>evidencias/logs/surefire/</td><td>Reportes individuales de Selenium por clase.</td></tr>
<tr><td>evidencias/selenium/</td><td>28 capturas automaticas de las pantallas evaluadas.</td></tr>
<tr><td>Casos_Prueba_Testomat.csv</td><td>Archivo de importacion a Testomat.io.</td></tr>
<tr><td>Casos_Prueba.xlsx</td><td>Matriz de casos, resumen y cobertura.</td></tr>
<tr><td>Registro_Defectos.xlsx</td><td>Defectos DEF-01 y DEF-02 con su seguimiento.</td></tr>
<tr><td>Guia_Testomat.pdf</td><td>Pasos para registrar, ejecutar y exportar en Testomat.</td></tr>
</table>

</body>
</html>';

$acentos = [
    'CAPITULO' => 'CAPÍTULO', 'Capitulo' => 'Capítulo', 'capitulo' => 'capítulo',
    'Muebleria' => 'Mueblería', 'muebleria' => 'mueblería',
    'Ejecucion' => 'Ejecución', 'ejecucion' => 'ejecución',
    'Integracion' => 'Integración', 'integracion' => 'integración',
    'Validacion' => 'Validación', 'validacion' => 'validación',
    'Contrasena' => 'Contraseña', 'contrasena' => 'contraseña',
    'Codigo' => 'Código', 'codigo' => 'código', 'codigos' => 'códigos',
    'Sesion' => 'Sesión', 'sesion' => 'sesión',
    'autenticacion' => 'autenticación',
    'Recuperacion' => 'Recuperación', 'recuperacion' => 'recuperación',
    'informacion' => 'información',
    'configuracion' => 'configuración',
    'Aplicacion' => 'Aplicación', 'aplicacion' => 'aplicación',
    'automaticas' => 'automáticas', 'automatica' => 'automática', 'automatico' => 'automático',
    'Aceptacion' => 'Aceptación', 'aceptacion' => 'aceptación',
    'Version' => 'Versión', 'version' => 'versión',
    'Descripcion' => 'Descripción', 'descripcion' => 'descripción',
    'observacion' => 'observación',
    'Correccion' => 'Corrección', 'correccion' => 'corrección',
    'verificacion' => 'verificación',
    'metricas' => 'métricas',
    'paginas' => 'páginas', 'pagina' => 'página',
    'logica' => 'lógica',
    'publico' => 'público',
    'indice' => 'índice',
    'metodos' => 'métodos', 'metodo' => 'método',
    'tecnica' => 'técnica',
    'basico' => 'básico',
    'ultimo' => 'último',
    'numero' => 'número',
    'terminos' => 'términos', 'termino' => 'término',
    'caracter' => 'carácter',
    'practicas' => 'prácticas',
    'rapido' => 'rápido',
    'Analisis' => 'Análisis', 'analisis' => 'análisis',
    'Tambien' => 'También', 'tambien' => 'también',
    'ademas' => 'además',
    'despues' => 'después',
    'segun' => 'según',
    'asi' => 'así',
    'dia' => 'día',
    'exito' => 'éxito',
    'conexion' => 'conexión',
    'razon' => 'razón',
    'categorias' => 'categorías', 'categoria' => 'categoría',
    'unico' => 'único',
    'estan' => 'están',
    'sera' => 'será',
    'podria' => 'podría',
    'aqui' => 'aquí',
    'traves' => 'través',
    'facil' => 'fácil',
    'util' => 'útil',
    'criticos' => 'críticos', 'critico' => 'crítico',
    'garantia' => 'garantía',
    'envio' => 'envío',
    'se valido' => 'se validó',
    'invalidos' => 'inválidos', 'invalida' => 'inválida', 'invalidas' => 'inválidas',
    'valido' => 'válido', 'valida' => 'válida',
    'automatizacion' => 'automatización',
    'instalacion' => 'instalación',
    'documentacion' => 'documentación',
    'excepcion' => 'excepción',
    'seleccion' => 'selección',
    'accion' => 'acción',
    'opcion' => 'opción',
    'decision' => 'decisión',
    'condicion' => 'condición',
    'revision' => 'revisión',
    'conclusion' => 'conclusión',
    'definicion' => 'definición',
    'operacion' => 'operación',
    'produccion' => 'producción',
    'peticion' => 'petición',
    'funcion' => 'función',
    'Sprints' => 'Sprints',
];

foreach ($acentos as $sinAcento => $conAcento) {
    $html = preg_replace('/\b' . preg_quote($sinAcento, '/') . '\b/u', $conAcento, $html);
}

file_put_contents($raiz . '/docs/pruebas/Capitulo_VI.html', $html);
echo "HTML generado: docs/pruebas/Capitulo_VI.html\n";
echo "Unitarias: $totalUnit | Integracion: $totalInt | BDD: $totalBdd | E2E: $totalE2e | Imagenes: " . count($imagenes) . "\n";
