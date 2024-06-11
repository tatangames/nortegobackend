<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaServicioBasico extends Model
{
    use HasFactory;
    protected $table = 'nota_serviciobasico';
    public $timestamps = false;
}
