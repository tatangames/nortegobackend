<?php

namespace App\Http\Controllers\Api\Configuracion\Principal;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use App\Models\Slider;
use Illuminate\Http\Request;

class ApiPrincipalController extends Controller
{

    public function listadoPrincipal(){

        $arraySlider = Slider::where('activo', 1)->orderBy('posicion', 'ASC')->get();
        $arrayServicio = Servicios::where('activo', 1)->orderBy('posicion', 'ASC')->get();



        return ['success' => 1, 'slider' => $arraySlider, 'servicio' => $arrayServicio];
    }
}
