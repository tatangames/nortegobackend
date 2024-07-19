<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SOLICITUDES PARA UNA TALA DE ARBOL
     */
    public function up(): void
    {
        Schema::create('solicitud_talaarbol', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_usuario')->unsigned();

            $table->dateTime('fecha');
            $table->string('nombre', 100);
            $table->string('telefono', 15);
            $table->string('direccion', 500);
            $table->string('imagen', 100);
            $table->text('nota')->nullable();
            $table->boolean('escrituras');
            $table->string('latitud', 100)->nullable();
            $table->string('longitud', 100)->nullable();

            // ESTADO DE SOLICITUD
            $table->integer('estado');
            $table->boolean('visible');

            $table->foreign('id_usuario')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_talaarbol');
    }
};
