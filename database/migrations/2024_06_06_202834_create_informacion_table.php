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

            // Para poder activar los modales
            $table->boolean('androd_modal');
            $table->boolean('ios_modal');

            // version de aplicacion
            $table->string('version_android', 50);
            $table->string('version_ios', 50);
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
