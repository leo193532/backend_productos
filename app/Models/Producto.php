<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    protected $table = 'productos';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'descripcion', 'precio'];
}
