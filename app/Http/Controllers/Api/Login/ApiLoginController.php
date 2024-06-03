<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use App\Models\ReintentoSms;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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

            // TIEMPO QUE DEBE ESPERAR EL USUARIO PARA REENVIAR CODIGO SMS
            $limiteSegundosSMS = 20;

            // QUITAR ESPACIOS QUE VIENEN DEL NUMERO
            $telefono = str_replace(' ', '', $request->telefono);

            // GENERAR CODIGO
            $codigo = '';
            for($i = 0; $i < 6; $i++) {
                $codigo .= mt_rand(0, 9);
            }

            if($infoUsuario = Usuario::where('telefono', $telefono)->first()){

                // USUARIO BLOQUEADO
                if($infoUsuario->activo == 0){
                    return ['success' => 1];
                }

                // FECHA DEL SERVIDOR
                $currentDate = Carbon::now('America/El_Salvador');

                // DIFERENCIA EN SEGUNDOS ENTRE LA FECHA ACTUAL DEL SERVIDOR Y LA FECHA DEL ULTIMO INTENTO SMS
                $secondsSinceLastAttempt = $currentDate->diffInSeconds($infoUsuario->fechareintento);

                // VERIFICAR SI HAN PASADO AL MENOS X SEGUNDOS
                $puedeReenviarSMS = 0;
                $secondsToWait = 0;

                if($secondsSinceLastAttempt >= $limiteSegundosSMS){
                    $puedeReenviarSMS = 1;
                }else{
                    // CALCULAR EL TIEMPO RESTANTE (CRONOMETRO), SI AUN NO SE PUEDE REENVIAR SMS
                    $secondsToWait = $limiteSegundosSMS - $secondsSinceLastAttempt;
                }

                // CERO, SE SETEA AL TIEMPO X DE ESPERA DE SEGUNDOS PARA EL CRONOMETRO EN LA APP
                if($secondsToWait <= 0){
                    $secondsToWait = $limiteSegundosSMS;
                }

                // YA SE PUEDE REENVIAR SMS Y SE HACE EL REENVIO, SE ACTUALIZA LA FECHA
                if ($puedeReenviarSMS == 1) {

                    //******* AQUI SE ENVIA SMS ***********

                    $detaRe = new ReintentoSms();
                    $detaRe->id_usuarios = $infoUsuario->id;
                    $detaRe->fecha = $currentDate;
                    $detaRe->tipo = 1;
                    $detaRe->save();

                    // ACTUALIZAR LA FECHA DE REINTENTO SMS DEL USUARIO
                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'codigo' => $codigo,
                            'fechareintento' => $currentDate
                        ]);
                }

                // TIEMPO QUE SE USA EN CRONOMETRO PARA APLICACION MOVIL
                $secondsToWait = $secondsToWait * 1000;

                DB::commit();
                return ['success' => 2, 'canretry' => $puedeReenviarSMS, 'segundos' => $secondsToWait];
            } else {

                // CUANDO EL TELEFONO A REGISTRAR ES NUEVO, SI ES UN NUMERO ERRONEO, NO GUARDARA NADA
                // EN EXCEPCION DE ENVIO SMS



                $currentDate = Carbon::now('America/El_Salvador');

                $registro = new Usuario();
                $registro->telefono = $telefono;
                $registro->codigo = $codigo;
                $registro->fecha = $currentDate;
                $registro->fechareintento = $currentDate;
                $registro->onesignal = null;
                $registro->activo = 1;
                $registro->verificado = 0;
                $registro->fecha_verificado = null;
                $registro->save();


                //******* AQUI SE ENVIA SMS ***********

                $detaRe = new ReintentoSms();
                $detaRe->id_usuarios = $registro->id;
                $detaRe->fecha = $currentDate;
                $detaRe->tipo = 2;
                $detaRe->save();

                //************************************
                // TIEMPO QUE SE USA EN CRONOMETRO PARA APLICACION MOVIL
                $limiteSegundosSMS = $limiteSegundosSMS * 1000;

                DB::commit();
                return ['success' => 2, 'canretry' => 1, 'segundos' => $limiteSegundosSMS];
            }
        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    // SOLICITUD DE CODIGO DE CONFIRMACION
    public function reintentoSMS(Request $request){

        $rules = array(
            'telefono' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // SEGUNDOS A ESPERAR
            $limiteSegundosSMS = 20;

            $telefono = str_replace(' ', '', $request->telefono);

            if($infoUsuario = Usuario::where('telefono', $telefono)->first()){

                // usuario inactivo
                if($infoUsuario->activo == 0){
                    return ['success' => 1];
                }

                // FECHA DEL SERVIDOR
                $currentDate = Carbon::now('America/El_Salvador');

                // DIFERENCIA EN SEGUNDOS ENTRE LA FECHA ACTUAL DEL SERVIDOR Y LA FECHA DEL ULTIMO INTENTO SMS
                $secondsSinceLastAttempt = $currentDate->diffInSeconds($infoUsuario->fechareintento);

                // VERIFICAR SI HAN PASADO AL MENOS X SEGUNDOS
                $puedeReenviarSMS = 0;
                $secondsToWait = 0;

                if($secondsSinceLastAttempt >= $limiteSegundosSMS){
                    $puedeReenviarSMS = 1;
                }else{
                    // CALCULAR EL TIEMPO RESTANTE (CRONOMETRO), SI AUN NO SE PUEDE REENVIAR SMS
                    $secondsToWait = $limiteSegundosSMS - $secondsSinceLastAttempt;
                }

                // CERO, SE SETEA AL TIEMPO X DE ESPERA DE SEGUNDOS PARA EL CRONOMETRO EN LA APP
                if($secondsToWait <= 0){
                    $secondsToWait = $limiteSegundosSMS;
                }


                //******* AQUI SE ENVIA SMS ***********

                if ($puedeReenviarSMS) {
                    $detaRe = new ReintentoSms();
                    $detaRe->id_usuarios = $infoUsuario->id;
                    $detaRe->fecha = $currentDate;
                    $detaRe->tipo = 3;
                    $detaRe->save();

                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'fechareintento' => $currentDate
                        ]);

                    // ENVIAR SMS, SE TOMARA EL MISMO CODIGO, NO SE ACTUALIZARA AQUI


                }

                //************************************
                // TIEMPO QUE SE USA EN CRONOMETRO PARA APLICACION MOVIL
                $secondsToWait = $secondsToWait * 1000;


                DB::commit();
                // en la App se reinicia el cronometro a 2 minutos para poder reintentar
                return ['success' => 2, 'canretry' => $puedeReenviarSMS, 'segundos' => $secondsToWait];
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

            $telefono = str_replace(' ', '', $request->telefono);
            $codigo = str_replace(' ', '', $request->codigo);

            if($infoUsuario = Usuario::where('telefono', $telefono)
                ->where('codigo', $codigo)
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
                return ['success' => 2, 'token' => $token, 'id' => strval($infoUsuario->id)];
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
