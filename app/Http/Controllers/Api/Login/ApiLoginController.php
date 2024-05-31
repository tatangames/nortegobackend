<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use App\Models\ReintentoSms;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiLoginController extends Controller
{
    public function verificacionTelefono(Request $request)
    {
        $rules = array(
            'telefono' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $retryAfterMinutes = 2;

            // solicitud para iniciar sesion
            if($infoUsuario = Usuario::where('telefono', $request->telefono)->first()){

                // usuario inactivo
                if($infoUsuario->activo == 0){
                    return ['success' => 1];
                }

                // se verifica cuanto tiempo debe esperar el usuario para hacer un reintento de mensaje
                $currentDate = Carbon::now('America/El_Salvador');
                $minutesPassed = $currentDate->diffInMinutes($infoUsuario->fechareintento);

                if ($minutesPassed >= $retryAfterMinutes) {
                    // Puedes reintentar enviar el mensaje
                    $canRetry = true;
                    $minutesToWait = 0;
                } else {
                    // Debes esperar más tiempo
                    $canRetry = false;
                    $minutesToWait = $this->convertirMinutosASegundos($retryAfterMinutes - $minutesPassed);
                }



                //******* AQUI SE ENVIA SMS ***********

                if($canRetry){
                    $detaRe = new ReintentoSms();
                    $detaRe->id_usuarios = $infoUsuario->id;
                    $detaRe->fecha = $currentDate;
                    $detaRe->tipo = 1;
                    $detaRe->save();

                    $codigo = '';
                    for($i = 0; $i < 4; $i++) {
                        $codigo .= mt_rand(0, 9);
                    }

                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'codigo' => $codigo,
                            'fechareintento' => $currentDate
                        ]);

                    // Enviar codigo SMS
                }

                //************************************


                // Enviar datos
                DB::commit();
                return ['success' => 2, 'canretry' => $canRetry, 'segundos' => $minutesToWait];

            } else {

                // telefono no registrado, se debe registrar

                $codigo = '';
                for($i = 0; $i < 4; $i++) {
                    $codigo .= mt_rand(0, 9);
                }

                $currentDate = Carbon::now('America/El_Salvador');

                $registro = new Usuario();
                $registro->telefono = $request->telefono;
                $registro->codigo = $codigo;
                $registro->fecha = $currentDate;
                $registro->fechareintento = $currentDate;
                $registro->onesignal = null;
                $registro->activo = 1;
                $registro->verificado = 0;
                $registro->fecha_verificado = null;
                $registro->save();

                // Como es primera vez, se debera esperar 2 minutos para reintento en la App.
                $canRetry = false;
                $minutesToWait = 2;


                //******* AQUI SE ENVIA SMS ***********

                $detaRe = new ReintentoSms();
                $detaRe->id_usuarios = $registro->id;
                $detaRe->fecha = $currentDate;
                $detaRe->tipo = 2;
                $detaRe->save();


                //************************************


                DB::commit();
                return ['success' => 2, 'canretry' => $canRetry, 'segundos' => $minutesToWait];
            }
        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    private function convertirMinutosASegundos($minutos)
    {
        return $minutos * 60;
    }



    // SOLICITUD DE CODIGO DE CONFIRMACION
    public function reintentoSMS(Request $request){

        $rules = array(
            'telefono' => 'required',
            'codigo' => 'required'
        );


        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            if($infoUsuario = Usuario::where('telefono', $request->telefono)->first()){

                // usuario inactivo
                if($infoUsuario->activo == 0){
                    return ['success' => 1];
                }

                // Se verifica que el tiempo de espera es permitido
                $retryAfterMinutes = 2;

                $currentDate = Carbon::now('America/El_Salvador');

                $minutesPassed = $currentDate->diffInMinutes($infoUsuario->fechareintento);

                if ($minutesPassed >= $retryAfterMinutes) {
                    // Puedes reintentar enviar el mensaje
                    $canRetry = true;
                    $minutesToWait = 0;
                } else {
                    // Debes esperar más tiempo
                    $canRetry = false;
                    $minutesToWait = $retryAfterMinutes - $minutesPassed;
                }


                //******* AQUI SE ENVIA SMS ***********

                if($canRetry){
                    $detaRe = new ReintentoSms();
                    $detaRe->id_usuarios = $infoUsuario->id;
                    $detaRe->fecha = $currentDate;
                    $detaRe->tipo = 3;
                    $detaRe->save();

                    $codigo = '';
                    for($i = 0; $i < 4; $i++) {
                        $codigo .= mt_rand(0, 9);
                    }

                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'codigo' => $codigo,
                            'fechareintento' => $currentDate
                        ]);

                    // Enviar SMS


                }

                //************************************






                // en la App se reinicia el cronometro a 2 minutos para poder reintentar
                return ['success' => 2, 'canretry' => $canRetry, 'minutos' => $minutesToWait];
            }else{

                // telefono no encontrado
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    public function verificarCodigo(Request $request){

        $rules = array(
            'telefono' => 'required',
            'codigo' => 'required'
        );

        // idonesignal

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            if($infoUsuario = Usuario::where('telefono', $request->telefono)
                ->where('codigo', $request->codigo)
                ->first()){

                // usuario inactivo
                if($infoUsuario->activo == 0){
                    return ['success' => 1];
                }

                if($infoUsuario->verificado == 0){
                    $currentDate = Carbon::now('America/El_Salvador');

                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'verificado' => 1,
                            'fecha_verificado' => $currentDate
                        ]);
                }

                $token = JWTAuth::fromUser($infoUsuario);

                // actualizar id notificacion
                $idOneSignal = $request->idonesignal;

                if($idOneSignal != null){
                    if(strlen($idOneSignal) == 0){
                        // vacio no hacer nada
                    }else{
                        // Actualizar
                        Usuario::where('id', $infoUsuario->id)
                            ->update([
                                'onesignal' => $idOneSignal,
                            ]);
                    }
                }

                DB::commit();
                return ['success' => 2, 'token' => $token];
            }else{
                // codigo incorrecto
                return ['success' => 3];
            }
        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

}
