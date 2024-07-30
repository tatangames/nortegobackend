<?php

namespace App\Http\Controllers\Backend\Solicitud;

use App\Http\Controllers\Controller;
use App\Models\DenunciaBasico;
use App\Models\DenunciaTalaArbol;
use App\Models\SolicitudTalaArbol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SolicitudUsuarioController extends Controller
{
    public function __construct(){
        $this->middleware('auth:admin');
    }


    public function indexSolicitudRedVial(){

        return view('backend.admin.solicitudes.redvial.activas.vistaredvial');
    }


    // TABLA PARA RED VIAL
    public function tablaSolicitudRedVial(){

        $listado = DenunciaBasico::where('id_servicio', 1) // RED VIAL
            ->where('estado', 1)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.redvial.activas.tablaredvial', compact('listado'));
    }

    public function mapaSolicitudBasica(Request $request){

        $infoNotaSer = DenunciaBasico::where('id', $request->id)->first();

        if($infoNotaSer->latitud != null && $infoNotaSer->longitud != null){

            $latitude = $infoNotaSer->latitud;
            $longitude = $infoNotaSer->longitud;

            $googleMapsUrl = "https://www.google.com/maps?q={$latitude},{$longitude}";
            return ['success' => 1, 'url' => $googleMapsUrl];
        }else{
            return ['success' => 2];
        }
    }


    public function finalizarSolicitudRedVial(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        DenunciaBasico::where('id', $request->id)
            ->update([
                'estado' => 2,
            ]);

        return ['success' => 1];
    }


    public function reportePdfRedVialVarios($idlista){

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('NetGo');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            NorteGo <br>
            Reporte Red Viales
            </div>";

        $porciones = explode("-", $idlista);

        // filtrado por x departamento y x año
        $arrayListado = DenunciaBasico::whereIn('id', $porciones)
            ->where('id_servicio', 1) // RED VIAL
            ->orderBy('fecha', 'DESC')
            ->get();


        $tabla .= "<table width='100%' id='tablaFor'>
                    <tbody>";


        $tabla .= "<tr>
                        <td style='font-weight: bold' width='6%'>Fecha</td>
                        <td style='font-weight: bold' width='12%'>Nota</td>
                        <td style='font-weight: bold' width='12%'>Imagen</td>

                    </tr>";

        foreach ($arrayListado as $dato) {

            $fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $imageUrl = asset('storage/archivos/'.$dato->imagen);

            $tabla .= "<tr>
                        <td width='6%'>$fechaFormat</td>
                        <td width='12%'>$dato->nota</td>
                        <td width='12%'>
                            <center>
                                <img src='".$imageUrl."' width='100px' height='100px' />
                            </center>
                         </td>
                    </tr>";
        }

        $tabla .= "</tbody></table>";


        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }




    // *************** FINALIZADAS *************************

    public function indexSolicitudRedVialFinalizada(){

        return view('backend.admin.solicitudes.redvial.finalizadas.vistaredvialfinalizada');
    }


    public function tablaSolicitudRedVialFinalizada(){
        $listado = DenunciaBasico::where('id_servicio', 1) // RED VIAL
            ->where('estado', 2) // FINALIZADAS
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.redvial.finalizadas.tablaredvialfinalizada', compact('listado'));
    }



    //***************** ALUMBRADO ELECTRICO ***********************

    public function indexSolicitudAlumbrado(){
        return view('backend.admin.solicitudes.alumbrado.activas.vistaalumbrado');
    }

    public function tablaSolicitudAlumbrado(){

        $listado = DenunciaBasico::where('id_servicio', 2) // ALUMBRADO
            ->where('estado', 1)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.alumbrado.activas.tablaalumbrado', compact('listado'));
    }


    public function finalizarSolicitudAlumbrado(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        DenunciaBasico::where('id', $request->id)
            ->update([
                'estado' => 2,
            ]);

        return ['success' => 1];
    }


    public function reportePdfAlumbradoVarios($idlista){

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('NetGo');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            NorteGo <br>
            Reporte Alumbrado Eléctrico
            </div>";

        $porciones = explode("-", $idlista);

        // filtrado por x departamento y x año
        $arrayListado = DenunciaBasico::whereIn('id', $porciones)
            ->where('id_servicio', 2) // ALUMBRADO ELECTRICO
            ->orderBy('fecha', 'DESC')
            ->get();


        $tabla .= "<table width='100%' id='tablaFor'>
                    <tbody>";


        $tabla .= "<tr>
                        <td style='font-weight: bold' width='6%'>Fecha</td>
                        <td style='font-weight: bold' width='12%'>Nota</td>
                        <td style='font-weight: bold' width='12%'>Imagen</td>

                    </tr>";

        foreach ($arrayListado as $dato) {

            $fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $imageUrl = asset('storage/archivos/'.$dato->imagen);

            $tabla .= "<tr>
                        <td width='6%'>$fechaFormat</td>
                        <td width='12%'>$dato->nota</td>
                        <td width='12%'>
                            <center>
                                <img src='".$imageUrl."' width='100px' height='100px' />
                            </center>
                         </td>
                    </tr>";
        }

        $tabla .= "</tbody></table>";


        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }


    public function indexSolicitudAlumbradoFinalizada(){

        return view('backend.admin.solicitudes.alumbrado.finalizadas.vistaalumbradofinalizada');
    }


    public function tablaSolicitudAlumbradoFinalizada(){

        $listado = DenunciaBasico::where('id_servicio', 2) // ALUMBRADO ELECTRICO
            ->where('estado', 2)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.redvial.finalizadas.tablaredvialfinalizada', compact('listado'));
    }






    //***************** DESECHOS SOLIDOS ***********************

    public function indexSolicitudDesechos(){

        return view('backend.admin.solicitudes.desechos.activas.vistadesechos');
    }

    public function tablaSolicitudDesechos(){
        $listado = DenunciaBasico::where('id_servicio', 3) // DESECHOS
        ->where('estado', 1)
        ->orderBy('fecha', 'DESC')
        ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.desechos.activas.tabladesechos', compact('listado'));
    }


    public function finalizarSolicitudDesechos(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        DenunciaBasico::where('id', $request->id)
            ->update([
                'estado' => 2,
            ]);

        return ['success' => 1];
    }


    public function reportePdfDesechosVarios($idlista){

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('NetGo');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            NorteGo <br>
            Reporte Desechos Solidos
            </div>";

        $porciones = explode("-", $idlista);

        // filtrado por x departamento y x año
        $arrayListado = DenunciaBasico::whereIn('id', $porciones)
            ->where('id_servicio', 3) // DESECHOS SOLIDOS
            ->orderBy('fecha', 'DESC')
            ->get();


        $tabla .= "<table width='100%' id='tablaFor'>
                    <tbody>";


        $tabla .= "<tr>
                        <td style='font-weight: bold' width='6%'>Fecha</td>
                        <td style='font-weight: bold' width='12%'>Nota</td>
                        <td style='font-weight: bold' width='12%'>Imagen</td>

                    </tr>";

        foreach ($arrayListado as $dato) {

            $fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $imageUrl = asset('storage/archivos/'.$dato->imagen);

            $tabla .= "<tr>
                        <td width='6%'>$fechaFormat</td>
                        <td width='12%'>$dato->nota</td>
                        <td width='12%'>
                            <center>
                                <img src='".$imageUrl."' width='100px' height='100px' />
                            </center>
                         </td>
                    </tr>";
        }

        $tabla .= "</tbody></table>";


        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }


    public function indexSolicitudDesechosFinalizada(){

        return view('backend.admin.solicitudes.desechos.finalizadas.vistadesechosfinalizada');
    }


    public function tablaSolicitudDesechosFinalizada(){

        $listado = DenunciaBasico::where('id_servicio', 3) // DESECHOS SOLIDOS
            ->where('estado', 2)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.desechos.finalizadas.tabladesechosfinalizada', compact('listado'));
    }


    public function indexSolicitudTalaArbol()
    {
        return view('backend.admin.solicitudes.medioambiente.solicitud.vistatalaarbolsolicitud');
    }


    public function tablaSolicitudTalaArbol()
    {
        $listado = SolicitudTalaArbol::where('estado', 1)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.medioambiente.solicitud.tablatalaarbolsolicitud', compact('listado'));
    }


    public function informacionSolicitudTalaArbol(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = SolicitudTalaArbol::where('id', $request->id)->first()){

            $fechaFormat = date("d-m-Y h:i A", strtotime($info->fecha));


            return ['success' => 1, 'info' => $info,
                'fechaFormat' => $fechaFormat];
        }else{
            return ['success' => 2];
        }
    }


    public function mapaSolicitudTalaArbol(Request $request){

        $infoNotaSer = SolicitudTalaArbol::where('id', $request->id)->first();

        if($infoNotaSer->latitud != null && $infoNotaSer->longitud != null){

            $latitude = $infoNotaSer->latitud;
            $longitude = $infoNotaSer->longitud;

            $googleMapsUrl = "https://www.google.com/maps?q={$latitude},{$longitude}";
            return ['success' => 1, 'url' => $googleMapsUrl];
        }else{
            return ['success' => 2];
        }
    }


    public function finalizarSolicitudTalaArbol(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        SolicitudTalaArbol::where('id', $request->id)
            ->update([
                'estado' => 2,
            ]);

        return ['success' => 1];
    }


    //*********************************************************************

    public function indexSolicitudFinalizadaTalaArbol()
    {
        return view('backend.admin.solicitudes.medioambiente.solicitud.finalizada.vistatalaarbolsolicitudfinalizada');
    }


    public function tablaSolicitudFinalizadaTalaArbol()
    {
        $listado = SolicitudTalaArbol::where('estado', 2)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.medioambiente.solicitud.finalizada.tablatalaarbolsolicitudfinalizada', compact('listado'));
    }



    //**********************************************************************

    public function indexDenunciaTalaArbol()
    {
        return view('backend.admin.solicitudes.medioambiente.denuncias.vistadenunciatalarbol');
    }



    public function tablaDenunciaTalaArbol()
    {
        $listado = DenunciaTalaArbol::where('estado', 1)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listado as $dato){
            $dato->fechaFormat = date("d-m-Y h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.solicitudes.medioambiente.denuncias.tabladenunciatalaarbol', compact('listado'));
    }






}
