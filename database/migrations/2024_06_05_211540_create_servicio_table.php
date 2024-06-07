<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DIFERENTES SERVICIOS DE LA APP
     */
    public function up(): void
    {
        Schema::create('servicio', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_tiposervicio')->unsigned();
            $table->string('nombre', 50);
            $table->string('imagen', 100);
            $table->boolean('activo');
            $table->integer('posicion');

            $table->foreign('id_tiposervicio')->references('id')->on('tiposervicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio');
    }
};
