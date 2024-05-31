<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Login\ApiLoginController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::post('app/verificacion/telefono', [ApiLoginController::class,'verificacionTelefono']);
Route::post('app/reintento/telefono', [ApiLoginController::class,'reintentoSMS']);
Route::post('app/verificarcodigo/telefono', [ApiLoginController::class,'verificarCodigo']);










