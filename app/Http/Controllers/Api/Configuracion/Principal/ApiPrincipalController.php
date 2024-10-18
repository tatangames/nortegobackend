<?php

namespace App\Http\Controllers\Api\Configuracion\Principal;

use App\Http\Controllers\Controller;
use App\Models\CategoriaServicio;
use App\Models\Coordenadas;
use App\Models\DenunciaBasico;
use App\Models\DenunciaTalaArbol;
use App\Models\EstadoBasico;
use App\Models\Informacion;
use App\Models\NotaServicioBasico;
use App\Models\ServicioCatastro;
use App\Models\Servicios;
use App\Models\Slider;
use App\Models\SolicitudTalaArbol;
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
            if ($userToken->activo == 0) {
                return ['success' => 1];
            }

            DB::beginTransaction();

            try {

                $arraySlider = Slider::where('activo', 1)->orderBy('posicion', 'ASC')->get();
                $infoApp = Informacion::where('id', 1)->first();

                $resultsBloque = array();
                $index = 0;

                $arrayTipoServicio = CategoriaServicio::orderBy('posicion', 'ASC')
                    ->where('activo', 1)
                    ->get();

                foreach ($arrayTipoServicio as $secciones) {
                    array_push($resultsBloque, $secciones);

                    $subSecciones = Servicios::where('id_cateservicio', $secciones->id)
                        ->where('activo', 1) // para inactivarlo solo para administrador
                        ->orderBy('posicion', 'ASC')
                        ->get();

                    $resultsBloque[$index]->lista = $subSecciones;
                    $index++;
                }


                return ['success' => 2,
                    'modalandroid' => $infoApp->android_modal,
                    'modalios' => $infoApp->ios_modal,
                    'versionandroid' => $infoApp->version_android,
                    'versionios' => $infoApp->version_ios,

                    'slider' => $arraySlider,
                    'tiposervicio' => $arrayTipoServicio];

            } catch (\Throwable $e) {
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

        Log::info("ENTRA EN 1");

        $rules = array(
            'idservicio' => 'required',
        );

        // imagen, nota, latitud, longitud

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }
        Log::info("ENTRA EN 2");
        $tokenApi = $request->header('Authorization');

        if ($userToken = JWTAuth::user($tokenApi)) {

            // *** VERIFICAR SI ES PERMITIDO DENTRO DEL RANGO ***

            $infoServicio = Servicios::where('id', $request->idservicio)->first();

            // SE VERIFICA RANGO DE X METROS
            if($infoServicio->bloqueo_gps == 1){
                if($request->latitud != null && $request->longitud != null){

                    // DEL MISMO SERVICIO, QUE ESTAN ACTIVAS
                    $arrayNotaServicio = DenunciaBasico::where('id_servicio', $request->idservicio)
                        ->where('estado', 1)
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

                            Log::info("RETORNOOO");
                            return ['success' => 1, 'titulo' => $titulo, "mensaje" => $mensaje];
                        }
                    }
                }
            }
            DB::beginTransaction();

            try {
                Log::info("ENTRA EN 3");
                if ($request->hasFile('image')) {
                    Log::info("ENTRA EN 4");
                    $cadena = Str::random(15);
                    $tiempo = microtime();
                    $union = $cadena . $tiempo;
                    $nombre = str_replace(' ', '_', $union);

                    $extension = '.' . $request->imagen->getClientOriginalExtension();
                    $nombreFoto = $nombre . strtolower($extension);
                    $avatar = $request->file('image');
                    $upload = Storage::disk('archivos')->put($nombreFoto, \File::get($avatar));

                    if ($upload) {

                        $fechaHoy = Carbon::now('America/El_Salvador');

                        $registro = new DenunciaBasico();
                        $registro->id_usuario = $userToken->id;
                        $registro->id_servicio = $request->idservicio;
                        $registro->imagen = $nombreFoto;
                        $registro->nota = $request->nota;
                        $registro->latitud = $request->latitud;
                        $registro->longitud = $request->longitud;
                        $registro->fecha = $fechaHoy;
                        $registro->estado = 1;
                        $registro->visible = 1;
                        $registro->save();

                        Log::info("LLEGAAA");

                        DB::commit();
                        return ['success' => 2];
                    } else {
                        // error al subir imagen
                        Log::info("ENTRA EN 7");
                        return ['success' => 99];
                    }
                } else {
                    Log::info("ENTRA EN 6");
                    return ['success' => 99];
                }
            }catch(\Throwable $e){
                Log::info("error" . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            Log::info("ENTRA EN 5");
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




    public function registrarTalaArbolSolicitud(Request $request){

        $rules = array(
            'nombre' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'escritura' => 'required'
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

                        $registro = new SolicitudTalaArbol();
                        $registro->id_usuario = $userToken->id;
                        $registro->fecha = $fechaHoy;
                        $registro->nombre = $request->nombre;
                        $registro->telefono = $request->telefono;
                        $registro->direccion = $request->direccion;
                        $registro->imagen = $nombreFoto;
                        $registro->nota = $request->nota;
                        $registro->escrituras = $request->escritura;
                        $registro->latitud = $request->latitud;
                        $registro->longitud = $request->longitud;
                        $registro->fecha = $fechaHoy;
                        $registro->estado = 1;
                        $registro->visible = 1;
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



    public function registrarTalaArbolDenuncia(Request $request){

        $rules = array(
            'iduser' => 'required',
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

                        $registro = new DenunciaTalaArbol();
                        $registro->id_usuario = $userToken->id;
                        $registro->fecha = $fechaHoy;
                        $registro->imagen = $nombreFoto;
                        $registro->nota = $request->nota;
                        $registro->latitud = $request->latitud;
                        $registro->longitud = $request->longitud;
                        $registro->fecha = $fechaHoy;
                        $registro->estado = 1;
                        $registro->visible = 1;
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


    public function listadoSolicitudes(Request $request){

        $rules = array(
            'iduser' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        $tokenApi = $request->header('Authorization');

        if ($userToken = JWTAuth::user($tokenApi)) {

            DB::beginTransaction();

            try {

                $combinedArray = [];

                $arrayBasico = DenunciaBasico::where('id_usuario', $userToken->id)
                    ->where('visible', 1)
                    ->orderBy('fecha', 'DESC')
                    ->get();

                $hayDatosBool = false;

                foreach ($arrayBasico as $dato){
                    $hayDatosBool = true;
                    // SOLICITUD BASICA

                    // ESTADOS
                    if($dato->estado == 1){
                        $estado = "Solicitud Pendiente";
                    }else if($dato->estado == 2){
                        $estado = "Solicitud Procesada";
                    }
                    else{
                        $estado = "";
                    }
                    $fechaFormat = date("d-m-Y", strtotime($dato->fecha));

                    // nombre del servicio basico
                    $infoNomBas = Servicios::where('id', $dato->id_servicio)->first();


                    $combinedArray[] = [
                        'id' => $dato->id,
                        'tipo' => 1, // identificador que es el Array
                        'nombretipo' => $infoNomBas->nombre,
                        'estado' => $estado,
                        'nota' => $dato->nota,
                        'fecha' => $fechaFormat,
                        'nombre' => '',
                        'telefono' => '',
                        'direccion' => '',
                        'escritura' => 0,
                        'dui' => '',
                        'imagen' => ''
                    ];
                }

                //************************************************************


                $arraySoliTala = SolicitudTalaArbol::where('id_usuario', $userToken->id)
                    ->where('visible', 1)
                    ->orderBy('fecha', 'DESC')
                    ->get();

                foreach ($arraySoliTala as $dato){
                    $hayDatosBool = true;
                    // SOLICITUD TALA ARBOLES

                    // ESTADOS
                    if($dato->estado == 1){
                        $estado = "Solicitud Pendiente";
                    }else if($dato->estado == 2){
                        $estado = "Solicitud Procesada";
                    }
                    else{
                        $estado = "";
                    }

                    $fechaFormat = date("d-m-Y", strtotime($dato->fecha));

                    $combinedArray[] = [
                        'id' => $dato->id,
                        'tipo' => 2,  // identificador que es el Array
                        'nombretipo' => "Solicitud Tala de Árbol",
                        'estado' => $estado,
                        'nota' => $dato->nota,
                        'fecha' => $fechaFormat,
                        'nombre' => $dato->nombre,
                        'telefono' => $dato->telefono,
                        'direccion' => $dato->direccion,
                        'escritura' => $dato->escrituras,
                        'dui' => '',
                        'imagen' => $dato->imagen
                    ];
                }


                //************************************************************


                $arrayDenunciaTala = DenunciaTalaArbol::where('id_usuario', $userToken->id)
                    ->where('visible', 1)
                    ->orderBy('fecha', 'DESC')
                    ->get();

                foreach ($arrayDenunciaTala as $dato){
                    $hayDatosBool = true;
                    // DENUNCIA TALA DE ARBOLES

                    // ESTADOS
                    if($dato->estado == 1){
                        $estado = "Solicitud Pendiente";
                    }else if($dato->estado == 2){
                        $estado = "Solicitud Procesada";
                    }
                    else{
                        $estado = "";
                    }

                    $fechaFormat = date("d-m-Y", strtotime($dato->fecha));

                    $combinedArray[] = [
                        'id' => $dato->id,
                        'tipo' => 3,  // identificador que es el Array
                        'nombretipo' => "Denuncia Tala de Árbol",
                        'estado' => $estado,
                        'nota' => $dato->nota,
                        'fecha' => $fechaFormat,
                        'nombre' => '',
                        'telefono' => '',
                        'direccion' => '',
                        'escritura' => 0,
                        'dui' => '',
                        'imagen' => $dato->imagen
                    ];
                }


                //************************************************************


                $arrayCatastro = ServicioCatastro::where('id_usuario', $userToken->id)
                    ->where('visible', 1)
                    ->orderBy('fecha', 'DESC')
                    ->get();

                foreach ($arrayCatastro as $dato){
                    $hayDatosBool = true;
                    // CATASTRO


                    // ESTADOS
                    if($dato->estado == 1){
                        $estado = "Pendiente de Revisión";

                    }else if($dato->estado == 2){
                        $estado = "Solvente, Solvencia lista para Retirar";

                    }
                    else if($dato->estado == 3){
                        $estado = "Pendiente de Pago, pasar a ventanilla";
                    }else{
                        $estado = "";
                    }


                    $fechaFormat = date("d-m-Y", strtotime($dato->fecha));

                    $combinedArray[] = [
                        'id' => $dato->id,
                        'tipo' => 4,  // identificador que es el Array
                        'nombretipo' => "Solicitud de Solvencia Catastral",
                        'estado' => $estado,
                        'nota' => "",
                        'fecha' => $fechaFormat,
                        'nombre' => $dato->nombre,
                        'telefono' => '',
                        'direccion' => '',
                        'escritura' => 0,
                        'dui' => $dato->dui,
                        'imagen' => ''
                    ];
                }



                // ORDENAR DATOS
                usort($combinedArray, function ($a, $b) {
                    return strtotime($b['fecha']) - strtotime($a['fecha']);
                });

                $hayDatos = 0;

                if($hayDatosBool){
                    $hayDatos = 1;
                }


                DB::commit();
                return ['success' => 1, 'haydatos' => $hayDatos, 'listado' => $combinedArray];
            }catch(\Throwable $e){
                Log::info("error" . $e);
                DB::rollback();
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }



    // OCULTAR AL USUARIO
    public function ocultarSolicitudes(Request $request)
    {
        $rules = array(
            'id' => 'required',
            'tipo' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }



        $tokenApi = $request->header('Authorization');

        if ($userToken = JWTAuth::user($tokenApi)) {

            DB::beginTransaction();

            try {

                $idFila = $request->id;

                if($request->tipo == 1){
                    // BASICO

                    DenunciaBasico::where('id', $idFila)
                        ->update([
                            'visible' => 0,
                        ]);

                }else if($request->tipo == 2){
                    // SOLICITUD TALA DE ARBOL

                    SolicitudTalaArbol::where('id', $idFila)
                        ->update([
                            'visible' => 0,
                        ]);
                }
                else if($request->tipo == 3){
                    // DENUNCIA TALA DE ARBOL

                    DenunciaTalaArbol::where('id', $idFila)
                        ->update([
                            'visible' => 0,
                        ]);
                }
                else if($request->tipo == 4){
                    // CATASTRO

                    ServicioCatastro::where('id', $idFila)
                        ->update([
                            'visible' => 0,
                        ]);
                }

                DB::commit();
                return ['success' => 1];
            }catch(\Throwable $e){
                Log::info("error" . $e);
                DB::rollback();
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }



    public function registrarSolicitudCatastro(Request $request){

        $rules = array(
            'tiposoli' => 'required',
            'nombre' => 'required',
            'dui' => 'required'
        );

        // latitud, longitud

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        $tokenApi = $request->header('Authorization');

        if ($userToken = JWTAuth::user($tokenApi)) {

            DB::beginTransaction();

            try {

                $fechaHoy = Carbon::now('America/El_Salvador');

                $registro = new ServicioCatastro();
                $registro->id_usuario = $userToken->id;
                $registro->fecha = $fechaHoy;
                $registro->tipo_solicitud = $request->tiposoli;

                // 1- Pendiente de revision
                // 2- solvente, solvencia lista para retirar
                // 3- pendiente de pago, pasar a ventanilla

                $registro->estado = 1;
                $registro->nombre = $request->nombre;
                $registro->dui = $request->dui;
                $registro->latitud = $request->latitud;
                $registro->longitud = $request->longitud;
                $registro->visible = 1;
                $registro->save();


                DB::commit();
                return ['success' => 1];
            }catch(\Throwable $e){
                Log::info("error" . $e);
                DB::rollback();
                return ['success' => 99];
            }

        }else{
            Log::info("token no encontrado");
            return ['success' => 99];
        }
    }








}
