<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TIPOS DE SERVICIO
     */
    public function up(): void
    {
        Schema::create('categoria_servicio', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 50);
            $table->integer('posicion');
            $table->boolean('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_servicio');
    }
};
