<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SLIDER IMAGENES
     */
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 200)->nullable();
            $table->string('imagen', 100);
            $table->boolean('activo');
            $table->integer('posicion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
