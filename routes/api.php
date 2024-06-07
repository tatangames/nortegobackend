<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Login\ApiLoginController;
use App\Http\Controllers\Api\Configuracion\Principal\ApiPrincipalController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::post('app/verificacion/telefono', [ApiLoginController::class,'verificacionTelefono']);
Route::post('app/reintento/telefono', [ApiLoginController::class,'reintentoSMS']);
Route::post('app/verificarcodigo/telefono', [ApiLoginController::class,'verificarCodigo']);


// ******************* RUTAS CON AUTENTIFICACION **********************
Route::middleware('verificarToken')->group(function () {

    // --- PANTALLA PRINCIPAL
    Route::post('app/principal/listado', [ApiPrincipalController::class,'listadoPrincipal']);

});









