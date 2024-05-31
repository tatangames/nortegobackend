<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * REGISTRO DE USUARIOS
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('telefono', 20)->unique();
            $table->string('codigo', 10)->nullable();
            $table->dateTime('fecha');
            $table->string('onesignal', 200)->nullable();
            $table->boolean('activo');

            // cuando el usuario ingreso el codigo por primera vez
            $table->boolean('verificado');
            $table->dateTime('fecha_verificado')->nullable();

            // tiempo para reintento
            $table->dateTime('fechareintento');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
