<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SE REGISTRA TODOS LOS INTENTOS SMS DE USUARIO
     */
    public function up(): void
    {
        Schema::create('reintento_sms', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_usuarios')->unsigned();
            $table->dateTime('fecha');


            //1- cuando el usuario se registra primera vez
            //2- cuando el usuario ya esta registrado
            //3- cuando el usuario ya registrado pide reenvio de codigo

            $table->integer('tipo');


            $table->foreign('id_usuarios')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reintento_sms');
    }
};
