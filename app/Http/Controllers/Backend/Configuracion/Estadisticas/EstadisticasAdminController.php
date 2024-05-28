<?php

namespace App\Http\Controllers\Backend\Configuracion\Estadisticas;

use App\Http\Controllers\Controller;
use App\Models\ReintentoSms;
use App\Models\Usuario;
use Illuminate\Http\Request;

class EstadisticasAdminController extends Controller
{
    public function __construct(){
        $this->middleware('auth:admin');
    }

    public function indexEstadisticaAdmin(){

        $conteoUsuario = Usuario::count();
        $conteoVerificado = Usuario::where('verificado', 1)->count();
        $conteoSms = ReintentoSms::count();


        return view('backend.admin.configuracion.estadisticasadmin.vistaestadisticasadmin', compact('conteoUsuario',
            'conteoVerificado', 'conteoSms'));
    }

}
