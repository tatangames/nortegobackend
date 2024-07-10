<?php

namespace App\Http\Controllers\Api\Configuracion\Principal;

use App\Http\Controllers\Controller;
use App\Models\CategoriaServicio;
use App\Models\Informacion;
use App\Models\NotaServicioBasico;
use App\Models\Servicios;
use App\Models\Slider;
use App\Models\TipoServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use GoogleMaps\GoogleMaps;



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


            DB::beginTransaction();

            try {


                $arraySlider = Slider::where('activo', 1)->orderBy('posicion', 'ASC')->get();
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

                $resultsBloque = array();
                $index = 0;

                $arrayTipoServicio = CategoriaServicio::orderBy('posicion', 'ASC')
                    ->where('activo', 1)
                    ->get();

                foreach ($arrayTipoServicio as $secciones){
                    array_push($resultsBloque,$secciones);

                    $subSecciones = Servicios::where('id_cateservicio', $secciones->id)
                        ->where('activo', 1) // para inactivarlo solo para administrador
                        ->orderBy('posicion', 'ASC')
                        ->get();

                    $resultsBloque[$index]->lista = $subSecciones;
                    $index++;
                }



                return ['success' => 2,
                    'codeandroid' => $newUpdateAndroid,
                    'slider' => $arraySlider,
                    'tiposervicio' => $arrayTipoServicio];

            }catch(\Throwable $e){
                Log::info("error" . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }
        else{
            // HAY ERROR AL OBTENER EL USUARIO.
            return ['success' => 99,
                'msg' => 'No hay token'];
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

            // *** VERIFICAR SI ES PERMITIDO DENTRO DEL RANGO ***

           if($request->latitud != null && $request->longitud != null){

               // DEL MISMO SERVICIO, QUE ESTAN ACTIVAS
               $arrayNotaServicio = NotaServicioBasico::where('id_servicio', $request->idservicio)
                    ->where('id_estado', 1)
                   ->get();

               // VERIFICAR COORDENADAS SI ESTAN DENTRO DEL MISMO RANGO

               foreach ($arrayNotaServicio as $dato){

                   $latitudeFrom = $dato->latitud;
                   $longitudeFrom = $dato->longitud;
                   $latitudeTo = $request->latitud;
                   $longitudeTo = $request->longitud;

                   // Verificar si están dentro del rango
                   $isWithinRange = $this->isWithinRange($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);

                   // Conocer la distancia
                   //'distance' => $this->haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)


                   if($isWithinRange){

                       $titulo = "Nota";
                       $mensaje = "Hay una Solicitud Pendiente en su Ubicación";

                       return ['success' => 1, 'titulo' => $titulo, "mensaje" => $mensaje];
                   }
               }
           }



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
                        $registro->id_estado = 1;
                        $registro->save();

                        DB::commit();
                        return ['success' => 2];
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


    // ********* VERIFICACION DE COORDENADAS *********

    private function isWithinRange($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $rangeInMeters = 20) {
        $distance = $this->haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);
        return $distance <= $rangeInMeters;
    }

    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
        // Convertir de grados a radianes
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        // Diferencias
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        // Fórmula Haversine
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distancia en metros
        return $earthRadius * $c;
    }





}
