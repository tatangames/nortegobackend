<?php

namespace App\Http\Controllers\Backend\Configuracion\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use App\Models\TipoServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiciosController extends Controller
{

    public function __construct(){
        $this->middleware('auth:admin');
    }

    //************************** TIPOS DE SERVICIOS *******************************


    public function indexTipoServicios(){

        return view('backend.admin.configuracion.tiposervicio.vistatiposervicio');
    }


    public function tablaTipoServicios(){
        $listado = TipoServicio::orderBy('posicion', 'ASC')->get();

        return view('backend.admin.configuracion.tiposervicio.tablatiposervicio',compact('listado'));
    }



    public function nuevoTipoServicios(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            if ($info = TipoServicio::orderBy('posicion', 'DESC')->first()) {
                $nuevaPosicion = $info->posicion + 1;
            } else {
                $nuevaPosicion = 1;
            }

            $registro = new TipoServicio();
            $registro->nombre = $request->nombre;
            $registro->activo = 1;
            $registro->posicion = $nuevaPosicion;
            $registro->save();

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionTipoServicios(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = TipoServicio::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarPosicionTipoServicios(Request $request){

        $tasks = TipoServicio::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }


    public function editarTipoServicios(Request $request){

        $rules = array(
            'id' => 'required',
            'toggle' => 'required',
            'nombre' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        TipoServicio::where('id', $request->id)
            ->update([
                'nombre' => $request->nombre,
                'activo' => $request->toggle,
            ]);

        return ['success' => 1];
    }











    //************************** SERVICIOS *******************************


    public function indexServicios($idtiposervicio){

        $arrayTipoServicio = TipoServicio::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.configuracion.tiposervicio.servicios.vistaservicios', compact('arrayTipoServicio',
        'idtiposervicio'));
    }


    public function tablaServicios($idtiposervicio){

        $listado = Servicios::orderBy('posicion', 'ASC')
            ->where('id_tiposervicio', $idtiposervicio)
            ->get();

        return view('backend.admin.configuracion.tiposervicio.servicios.tablaservicios',compact('listado'));
    }


    public function nuevoServicios(Request $request){

        $regla = array(
            'nombre' => 'required',
            'idtiposervicio' => 'required',
        );

        // imagen, descripcion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

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

                if ($info = Servicios::orderBy('posicion', 'DESC')->first()) {
                    $nuevaPosicion = $info->posicion + 1;
                } else {
                    $nuevaPosicion = 1;
                }

                DB::beginTransaction();

                try {
                    $registro = new Servicios();
                    $registro->nombre = $request->nombre;
                    $registro->descripcion = $request->descripcion;
                    $registro->activo = 1;
                    $registro->posicion = $nuevaPosicion;
                    $registro->imagen = $nombreFoto;
                    $registro->id_tiposervicio = $request->idtiposervicio;
                    $registro->save();

                    DB::commit();
                    return ['success' => 1];
                }catch(\Throwable $e){
                    Log::info("error" . $e);
                    DB::rollback();
                    return ['success' => 99];
                }


            }
            else {
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }

    public function informacionServicios(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Servicios::where('id', $request->id)->first()){

            $arraylista = TipoServicio::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $info, 'arraylista' => $arraylista];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarPosicionServicios(Request $request){

        $tasks = Servicios::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }


    public function editarServicios(Request $request){

        // id, nombre, imagen, toggle

        $rules = array(
            'id' => 'required',
            'toggle' => 'required',
            'idtiposervicio' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        if ($request->hasFile('imagen')) {

            $infoDato = Servicios::where('id', $request->id)->first();

            $imagenOld = $infoDato->imagen;

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena . $tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.' . $request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre . strtolower($extension);
            $avatar = $request->file('imagen');
            $upload = Storage::disk('archivos')->put($nombreFoto, \File::get($avatar));

            if ($upload) {

                Servicios::where('id', $request->id)
                    ->update([
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion,
                        'activo' => $request->toggle,
                        'imagen' => $nombreFoto,
                        'id_tiposervicio' => $request->idtiposervicio
                    ]);

                if(Storage::disk('archivos')->exists($imagenOld)){
                    Storage::disk('archivos')->delete($imagenOld);
                }

                return ['success' => 1];
            } else {
                // error al subir imagen
                return ['success' => 99];
            }
        } else {
            Servicios::where('id', $request->id)
                ->update([
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'activo' => $request->toggle,
                    'id_tiposervicio' => $request->idtiposervicio
                ]);

            return ['success' => 1];
        }
    }


}
