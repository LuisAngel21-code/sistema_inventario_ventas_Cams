<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'marca',
        'proveedor',
        'imagen',
        'costo',
        'precio_base',
        'stock',
        'stock_minimo',
        'activo',
    ];

    protected $casts = [
        'costo' => 'float',
        'precio_base' => 'float',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean',
    ];

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function actualizarStock(int $cantidadNueva): void
    {
        // HU-05: guarda la nueva cantidad para que se vea en el inventario.
        $this->stock = $cantidadNueva;
        $this->save();
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}