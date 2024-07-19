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


    // --- GUARDAR DATOS SERVICIO BASICO ---
    Route::post('app/servicios/basicos/registrar', [ApiPrincipalController::class,'registrarServicioBasico']);

    // --- GUARDAR DATOS PARA SOLICITUD TALA DE ARBOL ---
    Route::post('app/servicios/talaarbol-solicitud/registrar', [ApiPrincipalController::class,'registrarTalaArbolSolicitud']);

    // --- GUARDAR DATOS PARA DENUNCIA TALA DE ARBOL ---
    Route::post('app/servicios/talaarbol-denuncia/registrar', [ApiPrincipalController::class,'registrarTalaArbolDenuncia']);

    // LISTADDO DE SOLICITUDES MIXTAS
    Route::post('app/solicitudes/listado', [ApiPrincipalController::class,'listadoSolicitudes']);




});









