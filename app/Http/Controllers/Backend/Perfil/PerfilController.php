<?php

namespace App\Http\Controllers\Backend\Perfil;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\Informacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PerfilController extends Controller
{
    public function __construct(){
        $this->middleware('auth:admin');
    }

    public function indexEditarPerfil(){
        $usuario = auth()->user();

        return view('backend.admin.perfil.vistaperfil', compact('usuario'));
    }

    public function editarUsuario(Request $request){

        $regla = array(
            'password' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        $usuario = auth()->user();

        Administrador::where('id', $usuario->id)
            ->update(['password' => bcrypt($request->password)]);

        return ['success' => 1];
    }


    // ***********************************************************************


    public function indexSoporteActualizacion(){

        $info = Informacion::where('id', 1)->first();

        return view('backend.admin.configuracion.soporteapp.vistasoporteapp', compact('info'));
    }

    public function SoporteActualizacionUpdate(Request $request){

        $regla = array(
            'versionandroid' => 'required',
            'versionios' => 'required',
            'toggleandroid' => 'required',
            'toggleios' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        Informacion::where('id', 1)
            ->update(['version_android' => $request->versionandroid,
                      'version_ios' => $request->versionios,
                      'android_modal' => $request->toggleandroid,
                      'ios_modal' => $request->toggleios]);

        return ['success' => 1];
    }

}
