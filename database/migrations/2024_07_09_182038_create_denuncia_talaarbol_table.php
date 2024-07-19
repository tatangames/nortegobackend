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
        Schema::create('denuncia_talaarbol', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();

            $table->dateTime('fecha');
            $table->string('latitud', 100)->nullable();
            $table->string('longitud', 100)->nullable();
            $table->text('nota')->nullable();
            $table->string('imagen', 100)->nullable();
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
        Schema::dropIfExists('denuncia_talaarbol');
    }
};
