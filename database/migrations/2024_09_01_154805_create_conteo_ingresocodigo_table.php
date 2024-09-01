<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * LLEVA REGISTRO DE VECES QUE SE EQUIVOCO AL ESCRIBIR EL CODIGO DE INGRESO
     */
    public function up(): void
    {
        Schema::create('conteo_ingresocodigo', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuarios')->unsigned();
            $table->dateTime('fecha');

            $table->foreign('id_usuarios')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conteo_ingresocodigo');
    }
};
