<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'optica.producto'; // tu tabla real
    protected $primaryKey = 'idproducto';

    public $timestamps = false;

    // Opcional: evitar asignación masiva
    protected $guarded = [];
}
