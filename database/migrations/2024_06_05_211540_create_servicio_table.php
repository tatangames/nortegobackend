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
            $table->bigInteger('id_cateservicio')->unsigned();

            $table->integer('tiposervicio');

            $table->string('nombre', 50);
            $table->string('imagen', 100);
            $table->string('descripcion', 200)->nullable();
            $table->boolean('activo');
            $table->integer('posicion');


            // EJEMPLO UNA DENUNCIA DE BACHE, SE TOMA ALREDEDOR X METROS A LA ZONA
            $table->boolean('bloqueo_gps');

            $table->foreign('id_cateservicio')->references('id')->on('categoria_servicio');
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
