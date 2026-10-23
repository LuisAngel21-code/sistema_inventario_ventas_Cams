<?php

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\DetalleVenta;
use App\Models\Movimiento;
use App\Models\OtpCode;
use App\Models\Producto;
use App\Models\RecoveryToken;
use App\Models\Stock;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\Venta;
use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Assert;

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class FeatureContext implements Context
{
    private ?Sucursal $sucursal = null;
    private ?Usuario $usuario = null;
    private ?Producto $producto = null;
    private ?Stock $stock = null;
    private ?OtpCode $otp = null;
    private ?RecoveryToken $token = null;
    private ?Venta $venta = null;
    private bool $credencialesValidas = false;
    private bool $registroRechazado = false;
    private bool $ventaRechazada = false;
    private string $codigoIngresado = '';
    private string $nuevaClave = '';
    private int $stockAntes = 0;

    private function sucursalBase(): Sucursal
    {
        if ($this->sucursal === null) {
            $this->sucursal = Sucursal::firstOrCreate(
                ['nombre' => 'Tienda Behat'],
                ['direccion' => 'Av. Principal 123', 'telefono' => '987654321', 'activo' => true]
            );
            $this->sucursal->activo = true;
            $this->sucursal->save();
        }

        return $this->sucursal;
    }

    private function rolDesdeTexto(string $texto): string
    {
        $texto = strtolower(trim($texto));

        return match ($texto) {
            'administrador', 'admin' => 'administrador',
            'jefe' => 'jefe',
            'encargado de almacen', 'encargado_almacen', 'almacen' => 'encargado_almacen',
            'encargado de tienda', 'encargado_tienda', 'tienda' => 'encargado_tienda',
            default => $texto,
        };
    }

    // ===================== HU-01 LOGIN =====================
    #[Given('que existe el usuario :correo con rol :rol')]
    public function existeUsuario(string $correo, string $rol): void
    {
        $sucursal = $this->sucursalBase();
        $rolInterno = $this->rolDesdeTexto($rol);

        $this->usuario = Usuario::firstOrCreate(
            ['username' => $correo],
            [
                'password_hash' => bcrypt('cams2026'),
                'rol' => $rolInterno,
                'sucursal_id' => $sucursal->id,
                'activo' => true,
            ]
        );

        $this->usuario->rol = $rolInterno;
        $this->usuario->activo = true;
        $this->usuario->save();
    }
    #[When('inicio sesión con el correo :correo y la clave :clave')]
    public function inicioSesion(string $correo, string $clave): void
    {
        $usuario = Usuario::where('username', $correo)->where('activo', true)->first();
        $this->credencialesValidas = $usuario !== null && $usuario->verificarCredenciales($clave);
    }
    #[When('intento iniciar sesión sin credenciales')]
    public function intentoSinCredenciales(): void
    {
        $validador = Validator::make([], [
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $this->credencialesValidas = !$validador->fails();
    }
    #[Then('las credenciales deben ser válidas')]
    public function credencialesValidas(): void
    {
        Assert::assertTrue($this->credencialesValidas, 'Se esperaban credenciales válidas');
    }
    #[Then('las credenciales no deben ser válidas')]
    public function credencialesInvalidas(): void
    {
        Assert::assertFalse($this->credencialesValidas, 'Se esperaban credenciales inválidas');
    }

    // ===================== HU-02 RECUPERACIÓN =====================
    #[When('solicito la recuperación de contraseña para :correo')]
    public function solicitoRecuperacion(string $correo): void
    {
        $usuario = Usuario::where('username', $correo)->first();
        $this->token = $usuario ? RecoveryToken::generarPara($usuario) : null;
    }
    #[Then('debe generarse un token de recuperación')]
    public function tokenGenerado(): void
    {
        Assert::assertNotNull($this->token, 'No se generó el token de recuperación');
        Assert::assertTrue($this->token->sigueVigente(), 'El token no está vigente');
    }
    #[Then('no debe generarse un token de recuperación')]
    public function tokenNoGenerado(): void
    {
        Assert::assertNull($this->token, 'No debía generarse un token para un correo inexistente');
    }
    #[When('establezco la nueva contraseña :clave')]
    public function establezcoNuevaContrasena(string $clave): void
    {
        Assert::assertNotNull($this->token, 'No hay token de recuperación');

        $usuario = Usuario::find($this->token->usuario_id);
        $usuario->password_hash = bcrypt($clave);
        $usuario->save();

        $this->token->usado = true;
        $this->token->save();
        $this->nuevaClave = $clave;
    }
    #[Then('puedo iniciar sesión con la nueva contraseña')]
    public function inicioConNuevaClave(): void
    {
        $usuario = Usuario::find($this->token->usuario_id);
        Assert::assertTrue($usuario->verificarCredenciales($this->nuevaClave));
    }

    // ===================== HU-03 CÓDIGO TEMPORAL =====================
    #[When('genero un código temporal para el usuario')]
    public function generoCodigoTemporal(): void
    {
        $this->otp = OtpCode::generarPara($this->usuario);
        $this->codigoIngresado = $this->otp->codigo;
    }
    #[Then('el código temporal debe ser válido')]
    public function codigoValido(): void
    {
        Assert::assertTrue(
            $this->otp->esValido($this->codigoIngresado),
            'El código vigente debía ser válido'
        );
    }
    #[When('ingreso un código incorrecto')]
    public function ingresoCodigoIncorrecto(): void
    {
        $this->codigoIngresado = '000000';
    }
    #[When('ingreso un código expirado')]
    public function ingresoCodigoExpirado(): void
    {
        $this->otp = OtpCode::create([
            'usuario_id' => $this->usuario->id,
            'codigo' => '123456',
            'expira_en' => now()->subMinutes(5),
            'verificado' => false,
        ]);
        $this->codigoIngresado = '123456';
    }
    #[Then('el código temporal debe ser rechazado')]
    public function codigoRechazado(): void
    {
        Assert::assertFalse(
            $this->otp->esValido($this->codigoIngresado),
            'El código debía ser rechazado'
        );
    }

    // ===================== HU-13 SUCURSALES =====================
    #[When('registro la sucursal :nombre')]
    public function registroSucursal(string $nombre): void
    {
        $this->sucursal = Sucursal::firstOrCreate(
            ['nombre' => $nombre],
            ['direccion' => 'Av. Sur 789', 'telefono' => '987654323', 'activo' => true]
        );
    }
    #[Then('la sucursal debe existir en la base de datos')]
    public function sucursalExiste(): void
    {
        Assert::assertTrue(Sucursal::where('nombre', $this->sucursal->nombre)->exists());
    }
    #[Then('debo ver la sucursal en el listado')]
    public function sucursalEnListado(): void
    {
        Assert::assertGreaterThanOrEqual(1, Sucursal::where('nombre', $this->sucursal->nombre)->count());
    }
    #[When('cambio el estado de la sucursal')]
    public function cambioEstadoSucursal(): void
    {
        $this->sucursal->activo = true;
        $this->sucursal->save();
        $this->sucursal->activo = false;
        $this->sucursal->save();
    }
    #[Then('la sucursal debe quedar inactiva')]
    public function sucursalInactiva(): void
    {
        Assert::assertFalse((bool) Sucursal::find($this->sucursal->id)->activo);
    }

    // ===================== HU-04 PRODUCTOS =====================
    #[When('registro el producto :nombre en la categoría :categoria con precio :precio y stock :stock')]
    public function registroProducto(string $nombre, string $categoria, float $precio, int $stock): void
    {
        $this->producto = Producto::create([
            'codigo' => 'BDD-' . uniqid(),
            'nombre' => $nombre,
            'categoria' => $categoria,
            'precio_base' => $precio,
            'stock' => $stock,
            'stock_minimo' => 1,
            'activo' => true,
        ]);
    }
    #[Then('el producto debe existir en la base de datos')]
    public function productoExiste(): void
    {
        Assert::assertTrue(Producto::where('codigo', $this->producto->codigo)->exists());
    }
    #[When('intento registrar un producto sin datos obligatorios')]
    public function intentoProductoSinDatos(): void
    {
        $validador = Validator::make([], [
            'codigo' => ['required'],
            'nombre' => ['required'],
            'categoria' => ['required'],
        ]);

        $this->registroRechazado = $validador->fails();
    }
    #[Then('el registro del producto debe ser rechazado')]
    public function registroProductoRechazado(): void
    {
        Assert::assertTrue($this->registroRechazado, 'El registro sin datos debía rechazarse');
    }
    #[Then('el producto debe aparecer en el catálogo')]
    public function productoEnCatalogo(): void
    {
        $encontrado = Producto::where('codigo', $this->producto->codigo)->where('activo', true)->first();
        Assert::assertNotNull($encontrado, 'El producto no aparece en el catálogo');
    }

    // ===================== HU-05 STOCK =====================
    #[Given('que existe un producto :nombre con stock :stock en la sucursal :sucursal')]
    public function existeProductoConStock(string $nombre, int $cantidad, string $sucursal): void
    {
        $this->sucursal = Sucursal::firstOrCreate(
            ['nombre' => $sucursal],
            ['direccion' => 'Av. Principal 123', 'telefono' => '987654321', 'activo' => true]
        );

        $this->producto = Producto::create([
            'codigo' => 'BDD-' . uniqid(),
            'nombre' => $nombre,
            'categoria' => 'Camas',
            'precio_base' => 3500,
            'stock' => $cantidad,
            'stock_minimo' => 1,
            'activo' => true,
        ]);

        $this->stock = Stock::create([
            'producto_id' => $this->producto->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => $cantidad,
        ]);
    }
    #[When('actualizo el stock del producto a :cantidad')]
    public function actualizoStock(int $cantidad): void
    {
        $this->producto->actualizarStock($cantidad);

        if ($this->stock) {
            $this->stock->cantidad = $cantidad;
            $this->stock->save();
        }
    }
    #[Then('el stock del producto debe ser :cantidad')]
    public function stockEsperado(int $cantidad): void
    {
        Assert::assertEquals($cantidad, Producto::find($this->producto->id)->stock);
    }
    #[When('intento actualizar el stock a :cantidad')]
    public function intentoActualizarStock(string $cantidad): void
    {
        $validador = Validator::make(['stock' => $cantidad], [
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $this->registroRechazado = $validador->fails();
    }
    #[Then('la actualización debe ser rechazada')]
    public function actualizacionRechazada(): void
    {
        Assert::assertTrue($this->registroRechazado, 'La cantidad inválida debía rechazarse');
    }

    // ===================== HU-06 INVENTARIO =====================
    #[When('consulto el inventario de la sucursal')]
    public function consultoInventario(): void
    {
        $this->stock = Stock::where('sucursal_id', $this->sucursal->id)
            ->where('producto_id', $this->producto->id)
            ->first();
    }
    #[Then('debo ver el producto con cantidad :cantidad')]
    public function productoConCantidad(int $cantidad): void
    {
        Assert::assertNotNull($this->stock, 'No se encontró el stock del producto');
        Assert::assertEquals($cantidad, $this->stock->cantidad);
    }
    #[When('consulto el inventario de la categoría :categoria')]
    public function consultoInventarioCategoria(string $categoria): void
    {
        $this->stock = Stock::whereHas('producto', function ($consulta) use ($categoria) {
            $consulta->where('categoria', $categoria);
        })->first();

        $this->registroRechazado = $this->stock !== null;
    }
    #[Then('solo debo ver productos de esa categoría')]
    public function soloCategoria(): void
    {
        Assert::assertTrue($this->registroRechazado, 'No se encontraron productos de la categoría');
        Assert::assertEquals('Camas', $this->stock->producto->categoria);
    }
    #[Then('la cantidad mostrada debe coincidir con el stock registrado')]
    public function cantidadCoincide(): void
    {
        $registrado = Stock::where('producto_id', $this->producto->id)
            ->where('sucursal_id', $this->sucursal->id)
            ->value('cantidad');

        Assert::assertEquals($registrado, $this->stock->cantidad);
    }

    // ===================== HU-07 MOVIMIENTOS =====================
    #[Given('que existen movimientos de entrada y salida')]
    public function existenMovimientos(): void
    {
        $sucursal = $this->sucursalBase();

        foreach ([['entrada', 5], ['salida', 2]] as $indice => [$tipo, $cantidad]) {
            $producto = Producto::create([
                'codigo' => 'BDD-MOV-' . $tipo . '-' . uniqid(),
                'nombre' => 'Producto ' . $tipo,
                'categoria' => 'Camas',
                'precio_base' => 100,
                'stock' => 10,
                'stock_minimo' => 1,
                'activo' => true,
            ]);

            Movimiento::create([
                'producto_id' => $producto->id,
                'sucursal_id' => $sucursal->id,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'motivo' => 'Prueba BDD',
                'fecha' => '2026-01-15 10:00:00',
            ]);
        }
    }
    #[When('consulto los movimientos de la sucursal')]
    public function consultoMovimientos(): void
    {
        $this->registroRechazado = Movimiento::where('sucursal_id', $this->sucursalBase()->id)->count() > 0;
    }
    #[Then('debo ver movimientos de entrada y salida')]
    public function veoEntradaYSalida(): void
    {
        $tipos = Movimiento::where('sucursal_id', $this->sucursalBase()->id)->pluck('tipo')->unique();

        Assert::assertTrue($tipos->contains('entrada'), 'Falta el movimiento de entrada');
        Assert::assertTrue($tipos->contains('salida'), 'Falta el movimiento de salida');
    }
    #[Then('cada movimiento debe mostrar producto, tipo, cantidad y fecha')]
    public function movimientoConCampos(): void
    {
        $movimiento = Movimiento::where('sucursal_id', $this->sucursalBase()->id)->first();

        Assert::assertNotNull($movimiento->producto);
        Assert::assertNotEmpty($movimiento->tipo);
        Assert::assertGreaterThan(0, $movimiento->cantidad);
        Assert::assertNotNull($movimiento->fecha);
    }
    #[Then('debo poder consultar los movimientos registrados')]
    public function puedoConsultarMovimientos(): void
    {
        Assert::assertTrue($this->registroRechazado, 'No se pudieron consultar movimientos');
    }

    // ===================== HU-08 INGRESO =====================
    #[When('registro un ingreso de :cantidad unidades del producto')]
    public function registroIngreso(int $cantidad): void
    {
        $this->stockAntes = (int) Stock::where('producto_id', $this->producto->id)
            ->where('sucursal_id', $this->sucursal->id)->value('cantidad');

        DB::transaction(function () use ($cantidad) {
            $stock = Stock::firstOrCreate([
                'producto_id' => $this->producto->id,
                'sucursal_id' => $this->sucursal->id,
            ], ['cantidad' => 0]);

            $stock->incrementar($cantidad);
            $this->producto->increment('stock', $cantidad);

            $compra = Compra::create([
                'sucursal_id' => $this->sucursal->id,
                'usuario_id' => $this->usuario->id,
                'total' => $this->producto->costo * $cantidad,
            ]);

            DetalleCompra::create([
                'compra_id' => $compra->id,
                'producto_id' => $this->producto->id,
                'cantidad' => $cantidad,
                'costo' => $this->producto->costo ?? 0,
            ]);

            Movimiento::create([
                'producto_id' => $this->producto->id,
                'sucursal_id' => $this->sucursal->id,
                'tipo' => Movimiento::TIPO_ENTRADA,
                'cantidad' => $cantidad,
                'motivo' => 'Ingreso BDD',
                'usuario_id' => $this->usuario->id,
            ]);
        });
    }
    #[Then('el stock del producto debe incrementarse')]
    public function stockIncrementado(): void
    {
        $stock = Stock::where('producto_id', $this->producto->id)
            ->where('sucursal_id', $this->sucursal->id)
            ->first();

        Assert::assertNotNull($stock);
        Assert::assertGreaterThan($this->stockAntes, (int) $stock->cantidad);
    }
    #[Then('debe quedar registrado un movimiento de entrada')]
    public function movimientoEntradaRegistrado(): void
    {
        Assert::assertTrue(
            Movimiento::where('producto_id', $this->producto->id)
                ->where('tipo', Movimiento::TIPO_ENTRADA)
                ->exists()
        );
    }

    // ===================== HU-09 VENTAS =====================
    #[When('registro una venta de :cantidad unidades del producto')]
    public function registroVenta(int $cantidad): void
    {
        $stock = Stock::where('producto_id', $this->producto->id)
            ->where('sucursal_id', $this->sucursal->id)
            ->first();

        $disponible = $stock ? $stock->cantidad : 0;

        if ($cantidad > $disponible) {
            $this->ventaRechazada = true;
            $this->venta = null;

            return;
        }

        $this->ventaRechazada = false;

        DB::transaction(function () use ($cantidad, $stock) {
            $total = $this->producto->precio_base * $cantidad;

            $this->venta = Venta::create([
                'sucursal_id' => $this->sucursal->id,
                'usuario_id' => $this->usuario->id,
                'subtotal' => $total,
                'descuento' => 0,
                'total' => $total,
                'tipo_pago' => 'efectivo',
                'comprobante_tipo' => 'boleta',
                'cliente_nombre' => 'Cliente BDD',
                'vendedor_nombre' => 'Vendedor BDD',
                'estado' => 'completada',
            ]);

            DetalleVenta::create([
                'venta_id' => $this->venta->id,
                'producto_id' => $this->producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $this->producto->precio_base,
            ]);

            $stock->decrementar($cantidad);
            $this->producto->decrement('stock', $cantidad);

            Movimiento::create([
                'producto_id' => $this->producto->id,
                'sucursal_id' => $this->sucursal->id,
                'tipo' => Movimiento::TIPO_SALIDA,
                'cantidad' => $cantidad,
                'motivo' => 'Venta BDD',
                'usuario_id' => $this->usuario->id,
            ]);
        });
    }
    #[Then('la venta debe existir en la base de datos')]
    public function ventaExiste(): void
    {
        Assert::assertNotNull($this->venta, 'No se registró la venta');
        Assert::assertTrue(Venta::where('id', $this->venta->id)->exists());
    }
    #[When('intento registrar una venta de :cantidad unidades del producto')]
    public function intentoVenta(int $cantidad): void
    {
        $this->registroVenta($cantidad);
    }
    #[Then('la venta debe ser rechazada')]
    public function ventaRechazadaEsperada(): void
    {
        Assert::assertTrue($this->ventaRechazada, 'La venta debía rechazarse por stock insuficiente');
    }
    #[Then('el stock del producto debe quedar en :cantidad')]
    public function stockQueda(int $cantidad): void
    {
        $stock = Stock::where('producto_id', $this->producto->id)
            ->where('sucursal_id', $this->sucursal->id)
            ->first();

        Assert::assertEquals($cantidad, $stock->cantidad);
    }

    // ===================== HU-10 HISTORIAL =====================
    #[Given('que existe una venta registrada')]
    public function existeVentaRegistrada(): void
    {
        $this->sucursal = $this->sucursalBase();

        $this->producto = Producto::create([
            'codigo' => 'BDD-HIS-' . uniqid(),
            'nombre' => 'Cama Historial',
            'categoria' => 'Camas',
            'precio_base' => 3500,
            'stock' => 10,
            'stock_minimo' => 1,
            'activo' => true,
        ]);

        $this->usuario ??= Usuario::firstOrCreate(
            ['username' => 'tienda.behat@cams.com'],
            [
                'password_hash' => bcrypt('cams2026'),
                'rol' => 'encargado_tienda',
                'sucursal_id' => $this->sucursal->id,
                'activo' => true,
            ]
        );

        $this->venta = Venta::create([
            'fecha' => '2026-01-15 10:00:00',
            'sucursal_id' => $this->sucursal->id,
            'usuario_id' => $this->usuario->id,
            'subtotal' => 7000,
            'descuento' => 0,
            'total' => 7000,
            'tipo_pago' => 'efectivo',
            'comprobante_tipo' => 'boleta',
            'cliente_nombre' => 'Cliente BDD',
            'vendedor_nombre' => 'Vendedor BDD',
            'estado' => 'completada',
        ]);

        DetalleVenta::create([
            'venta_id' => $this->venta->id,
            'producto_id' => $this->producto->id,
            'cantidad' => 2,
            'precio_unitario' => 3500,
        ]);
    }
    #[When('consulto el historial de ventas')]
    public function consultoHistorial(): void
    {
        $this->venta = Venta::with('detalleVentas.producto')
            ->where('id', $this->venta->id)
            ->first();
    }
    #[Then('debo ver la fecha, los productos y el monto total')]
    public function historialConDatos(): void
    {
        Assert::assertNotNull($this->venta->fecha);
        Assert::assertGreaterThan(0, $this->venta->detalleVentas->count());
        Assert::assertGreaterThan(0, $this->venta->total);
    }
    #[When('consulto el detalle de la venta')]
    public function consultoDetalleVenta(): void
    {
        $this->venta = Venta::with('detalleVentas.producto')->find($this->venta->id);
    }
    #[Then('debo ver los productos vendidos')]
    public function veoProductosVendidos(): void
    {
        Assert::assertGreaterThan(0, $this->venta->detalleVentas->count());
        Assert::assertNotNull($this->venta->detalleVentas->first()->producto);
    }
    #[When('filtro el historial entre :inicio y :fin')]
    public function filtroHistorial(string $inicio, string $fin): void
    {
        $this->registroRechazado = Venta::filtradasPorRango($inicio, $fin)
            ->where('id', $this->venta->id)
            ->exists();
    }
    #[Then('debo ver solo las ventas del rango')]
    public function ventasDelRango(): void
    {
        Assert::assertTrue($this->registroRechazado, 'La venta no aparece dentro del rango de fechas');
    }
}
