<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servicio_catastro', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();

            $table->dateTime('fecha');
            $table->integer('estado');

            $table->integer('tipo_solicitud');

            $table->string('nombre', 100);
            $table->string('dui', 20);

            $table->string('latitud', 100)->nullable();
            $table->string('longitud', 100)->nullable();

            $table->boolean('visible');

            $table->foreign('id_usuario')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_catastro');
    }
};
