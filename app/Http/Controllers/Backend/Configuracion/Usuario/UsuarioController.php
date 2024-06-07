<?php

namespace App\Http\Controllers\Backend\Configuracion\Usuario;

use App\Http\Controllers\Controller;
use App\Models\ReintentoSms;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{

    public function __construct(){
        $this->middleware('auth:admin');
    }


    public function indexUsuario(){
        return view('backend.admin.configuracion.usuarios.vistausuario');
    }


    public function tablaUsuario(){
        $listado = Usuario::orderBy('fecha', 'DESC')->get();

        foreach ($listado as $dato){

            $conteo = ReintentoSms::where('id_usuarios', $dato->id)->count();
            $dato->conteosms = $conteo;

            $dato->fechaFormat = date("d-m-Y h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.configuracion.usuarios.tablausuario',compact('listado'));
    }

    public function informacionUsuario(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Usuario::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarUsuario(Request $request){

        $rules = array(
            'id' => 'required',
            'toggle' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        Usuario::where('id', $request->id)
            ->update([
                'activo' => $request->toggle,
            ]);

        return ['success' => 1];
    }


    public function indexSMSEnviados($id){

        return view('backend.admin.configuracion.usuarios.sms.vistasms', compact('id'));
    }


    public function tablaSMSEnviados($id){

        $listado = ReintentoSms::where('id_usuarios', $id)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){

            $dato->fechaFormat = date("d-m-Y h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.configuracion.usuarios.sms.tablasms', compact('listado'));
    }
}
