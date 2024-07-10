<?php

namespace App\Http\Controllers\Backend\Configuracion\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use App\Models\CategoriaServicio;
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
        $listado = CategoriaServicio::orderBy('posicion', 'ASC')->get();

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

            if ($info = CategoriaServicio::orderBy('posicion', 'DESC')->first()) {
                $nuevaPosicion = $info->posicion + 1;
            } else {
                $nuevaPosicion = 1;
            }

            $registro = new CategoriaServicio();
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

        if($info = CategoriaServicio::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarPosicionTipoServicios(Request $request){

        $tasks = CategoriaServicio::all();

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

        CategoriaServicio::where('id', $request->id)
            ->update([
                'nombre' => $request->nombre,
                'activo' => $request->toggle,
            ]);

        return ['success' => 1];
    }











    //************************** SERVICIOS *******************************


    public function indexServicios($idcategoria){

        $arrayTipoServicio = CategoriaServicio::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.configuracion.tiposervicio.servicios.vistaservicios', compact('arrayTipoServicio',
        'idcategoria'));
    }


    public function tablaServicios($idcategoria){

        $listado = Servicios::orderBy('posicion', 'ASC')
            ->where('id_cateservicio', $idcategoria)
            ->get();

        return view('backend.admin.configuracion.tiposervicio.servicios.tablaservicios',compact('listado'));
    }


    public function nuevoServicios(Request $request){

        $regla = array(
            'nombre' => 'required',
            'idtiposervicio' => 'required',
            'id_cateservicio' => 'required'
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
                    $registro->tiposervicio = $request->idtiposervicio;
                    $registro->id_cateservicio = $request->id_cateservicio;
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

            $arrayCategorias = CategoriaServicio::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $info, 'arrayCategorias' => $arrayCategorias];
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
            'idtipo' => 'required',
            'idcategoria' => 'required'
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
                        'id_cateservicio' => $request->idcategoria,
                        'tiposervicio' => $request->idtipo,
                        'nombre' => $request->nombre,
                        'imagen' => $nombreFoto,
                        'descripcion' => $request->descripcion,
                        'activo' => $request->toggle,
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
                    'id_cateservicio' => $request->idcategoria,
                    'tiposervicio' => $request->idtipo,
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'activo' => $request->toggle,
                ]);

            return ['success' => 1];
        }
    }


}
