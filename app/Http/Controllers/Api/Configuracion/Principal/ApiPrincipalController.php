<?php

namespace App\Http\Controllers\Api\Configuracion\Principal;

use App\Http\Controllers\Controller;
use App\Models\Informacion;
use App\Models\Servicios;
use App\Models\Slider;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiPrincipalController extends Controller
{

    public function listadoPrincipal(Request $request){

        // sacar usuario del token
        $tokenApi = $request->header('Authorization');

        if ($userToken = JWTAuth::user($tokenApi)) {

            // USUARIO BLOQUEADO
            if($userToken->activo == 0){
                return ['success' => 1];
            }


            $arraySlider = Slider::where('activo', 1)->orderBy('posicion', 'ASC')->get();
            $arrayServicio = Servicios::where('activo', 1)->orderBy('posicion', 'ASC')->get();
            $infoApp = Informacion::where('id', 1)->first();

            // VERIFICAR QUE EL CODIGO DE COMPILACION - ANDROID
            $newUpdateAndroid = 0;
            // SI ES -1 LA APP NO PUDO OBTENER EL IDENTIFICADOR
            if($request->codeapp != null && $request->codeapp != -1){
                // COMPARAR VERSION
                if($request->codeapp != $infoApp->code_android){
                    $newUpdateAndroid = 1;
                }
            }


            return ['success' => 2,
                'codeandroid' => $newUpdateAndroid,
                'slider' => $arraySlider,
                'servicio' => $arrayServicio];
        }
        else{
            // HAY ERROR AL OBTENER EL USUARIO.
            return ['success' => 99];
        }
    }
}
