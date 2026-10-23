<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    public const TIPO_ENTRADA = 'entrada';
    public const TIPO_SALIDA = 'salida';

    protected $table = 'movimientos';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'tipo',
        'cantidad',
        'motivo',
        'usuario_id',
        'fecha',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}