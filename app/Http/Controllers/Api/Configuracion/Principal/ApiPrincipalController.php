<?php

namespace App\Http\Controllers\Api\Configuracion\Principal;

use App\Http\Controllers\Controller;
use App\Models\Informacion;
use App\Models\NotaServicioBasico;
use App\Models\Servicios;
use App\Models\Slider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;




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



    public function registrarServicioBasico(Request $request){

        $rules = array(
            'iduser' => 'required',
            'idservicio' => 'required',
        );

        // imagen, nota, latitud, longitud

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        $tokenApi = $request->header('Authorization');

        if ($userToken = JWTAuth::user($tokenApi)) {

            if ($request->hasFile('imagen')) {

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena . $tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.' . $request->imagen->getClientOriginalExtension();
                $nombreFoto = $nombre . strtolower($extension);
                $avatar = $request->file('imagen');
                $upload = Storage::disk('archivos')->put($nombreFoto, \File::get($avatar));

                if ($upload) {

                    DB::beginTransaction();

                    try {

                        $fechaHoy = Carbon::now('America/El_Salvador');

                        $registro = new NotaServicioBasico();
                        $registro->id_usuario = $userToken->id;
                        $registro->id_servicio = $request->idservicio;
                        $registro->imagen = $nombreFoto;
                        $registro->nota = $request->nota;
                        $registro->latitud = $request->latitud;
                        $registro->longitud = $request->longitud;
                        $registro->fecha = $fechaHoy;
                        $registro->save();

                        DB::commit();
                        return ['success' => 1];
                    }catch(\Throwable $e){
                        Log::info("error" . $e);
                        DB::rollback();
                        return ['success' => 99];
                    }

                } else {
                    // error al subir imagen
                    return ['success' => 99];
                }
            } else {
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }







}
