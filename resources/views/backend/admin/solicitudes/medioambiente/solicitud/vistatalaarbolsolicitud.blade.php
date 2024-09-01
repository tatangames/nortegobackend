@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
    .custom-modal {
        max-width: 1000px;
    }

    .custom-modal .modal-content {
        height: 600px;
    }

    .custom-modal .modal-body {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-modal .modal-body .embed-responsive {
        width: 100%;
        height: 100%;
    }

    .custom-modal .modal-body .embed-responsive-item {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Cambia a 'cover' si prefieres que la imagen cubra todo el espacio */
    }
</style>


<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="form-group" style="width: 25%">
                    <label>Cronometro</label>
                    <label id="contador"></label>
                </div>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Listado</li>
                    <li class="breadcrumb-item active">Tala Arboles Activos</li>
                </ol>
            </div>
        </div>


        <!--
        <button type="button" style="margin: 10px" onclick="checkReporte()" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-square"></i>
            Reporte
        </button>
        -->

    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Solicitud Tala de Árbol Pendientes</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <div class="modal fade" id="modalInformacion">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-informacion">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="text" disabled class="form-control" id="fecha-info">
                                    </div>

                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" disabled class="form-control" id="telefono-info">
                                    </div>

                                    <div class="form-group">
                                        <label>Dirección</label>
                                        <input type="text" disabled class="form-control" id="direccion-info">
                                    </div>

                                    <div class="form-group">
                                        <label>Nota</label>
                                        <textarea type="text" disabled class="form-control" id="nota-info"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Tiene Escritura?</label>
                                        <input type="text" disabled class="form-control" id="escritura-info">
                                    </div>

                                    <div class="form-group">
                                        <label>Latitud</label>
                                        <input type="text" disabled class="form-control" id="latitud-info">
                                    </div>

                                    <div class="form-group">
                                        <label>Longitud</label>
                                        <input type="text" disabled class="form-control" id="longitud-info">
                                    </div>



                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <!--Cuadro modal para el Zoom de las fotos-->
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog custom-modal">
            <!--Contenido-->
            <div class="modal-content">
                <div class="modal-body mb-0 p-0">
                    <div class="embed-responsive embed-responsive-16by9 z-depth-1-half">
                        <img id="imgModal" src="" class="embed-responsive-item" alt="">
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <button class="btn btn-primary btn-anis ml-0" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <!--Fin Contenido-->
        </div>
    </div>





</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            var ruta = "{{ URL::to('/admin/mediambiente/solicitud/talaarbol/tabla') }}";
            $('#tablaDatatable').load(ruta);

            countdown();
            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/mediambiente/solicitud/talaarbol/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function countdown() {
            var seconds = 30;
            function tick() {
                var counter = document.getElementById("contador");
                seconds--;
                counter.innerHTML = "0:" + (seconds < 10 ? "0" : "") + String(seconds);
                if( seconds > 0 ) {
                    setTimeout(tick, 1000);
                } else {
                    recargar();
                    countdown();
                }
            }
            tick();
        }


        function modalFinalizar(id){

            Swal.fire({
                title: 'Finalizar',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    finalizarSolicitud(id);
                }
            })
        }


        function finalizarSolicitud(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/mediambiente/solicitud/finalizar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Finalizado');
                        recargar();
                    }
                    else {
                        toastr.error('Error al finalizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al finalizar');
                    closeLoading();
                });
        }



        function vistaMapa(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/mediambiente/solicitud/mapa', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        window.open(response.data.url, '_blank');
                    }
                    else if(response.data.success === 2){
                        // NO HAY COORDENADAS
                        toastr.error('No se encontro Latitud y Longitud del Usuario')
                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }



        function modalInformacion(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/mediambiente/solicitud/informacion', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("formulario-informacion").reset();

                        $('#fecha-info').val(response.data.fechaFormat);
                        $('#telefono-info').val(response.data.info.telefono);
                        $('#direccion-info').val(response.data.info.direccion);
                        $('#nota-info').val(response.data.info.nota);

                        if(response.data.info.escrituras == 1){
                            $('#escritura-info').val("Si");
                        }else{
                            $('#escritura-info').val("No");
                        }

                        $('#latitud-info').val(response.data.info.latitud);
                        $('#longitud-info').val(response.data.info.longitud);

                        $('#modalInformacion').modal('show');
                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function getPath(img) {
            atributo = img.getAttribute("src");
            document.getElementById("imgModal").setAttribute("src", atributo);
        }


    </script>


@endsection
