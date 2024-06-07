<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SOLO HABRA 1 SOLA FILA PARA INFORMACION
     */
    public function up(): void
    {
        Schema::create('informacion', function (Blueprint $table) {
            $table->id();

            // PARA MOSTRARLE AL USUARIO QUE HAY UNA NUEVA ACTUALIZACION
            // LA SE COMPARA SI SU VERSION NO ES LA ULTIMA

            $table->integer('code_android');
            $table->integer('code_ios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informacion');
    }
};
