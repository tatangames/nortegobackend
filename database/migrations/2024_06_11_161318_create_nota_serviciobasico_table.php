<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * REGISTRO DE SERVICIOS BASICOS
     */
    public function up(): void
    {
        Schema::create('nota_serviciobasico', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_usuario')->unsigned();
            $table->bigInteger('id_servicio')->unsigned();
            $table->string('imagen', 100);
            $table->string('nota', 2000)->nullable();
            $table->string('latitud', 100)->nullable();
            $table->string('longitud', 100)->nullable();
            $table->dateTime('fecha');

            $table->foreign('id_usuario')->references('id')->on('usuarios');
            $table->foreign('id_servicio')->references('id')->on('servicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_serviciobasico');
    }
};
