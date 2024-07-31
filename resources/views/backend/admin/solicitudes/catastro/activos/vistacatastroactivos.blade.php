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
                    <li class="breadcrumb-item active">Catastro</li>
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
                    <h3 class="card-title">Solvencia Catastral Pendientes</h3>
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

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estado</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">


                                    <div class="form-group">
                                        <input type="hidden" id="id-editar"/>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Respuesta</label>
                                        <select class="form-control" id="select-cambiar">
                                            <option value="2">Solvente, Solvencia lista para retirar</option>
                                            <option value="3">Pendiente de Pago, pasar a ventanilla</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarEstado()">Actualizar</button>
                </div>
            </div>
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

            var ruta = "{{ URL::to('/admin/catastro/activos/tabla') }}";
            $('#tablaDatatable').load(ruta);

            countdown();
            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/catastro/activos/tabla') }}";
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


        function vistaMapa(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/catastro/solicitud/mapa', formData, {
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
            let miSelect = document.getElementById('select-cambiar');
                miSelect.options.selectedIndex = 0;

            $('#id-editar').val(id);
            $('#modalEditar').modal('show');
        }


        function actualizarEstado(){

            var id = document.getElementById('id-editar').value;
            var estado = document.getElementById('select-cambiar').value;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('estado', estado);

            axios.post('/admin/catastro/actualizar/estado', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado')
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al editar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al editar');
                    closeLoading();
                });
        }



    </script>


@endsection
