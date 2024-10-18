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
                    <li class="breadcrumb-item active">Red Vial Activas</li>
                </ol>
            </div>
        </div>



        <button type="button" style="margin: 10px" onclick="checkReporte()" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-square"></i>
            Reporte
        </button>

    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado Red Vial Activas</h3>
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

            var ruta = "{{ URL::to('/admin/solicitud/redvial/tabla') }}";
            $('#tablaDatatable').load(ruta);

            countdown();
            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/solicitud/redvial/tabla') }}";
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

            axios.post('/admin/solicitud/redvial/finalizar', formData, {
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


        function checkReporte(){

            var tableRows = document.querySelectorAll('#tabla tbody tr');

            var selected = [];

            if (tableRows.length === 0) {
                toastr.error('No hay registros');
                return;
            }

            tableRows.forEach(function(row) {
                var checkbox = row.querySelector('.checkbox');
                if(checkbox != null) {
                    if (checkbox.checked) {
                        var dataInfo = row.getAttribute('data-info');
                        selected.push(dataInfo);
                    }
                }
            });

            if (selected.length <= 0) {
                toastr.error('Seleccionar Mínimo 1 Fila')
                return;
            }

            let listado = selected.toString();
            let reemplazo = listado.replace(/,/g, "-");

            window.open("{{ URL::to('admin/solicitud/redvial/reportevarios') }}/" + reemplazo);
        }

        function vistaMapa(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/solicitud/basico/mapa', formData, {
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

        function getPath(img) {
            atributo = img.getAttribute("src");
            document.getElementById("imgModal").setAttribute("src", atributo);
        }

        function modalBorrar(id){

            Swal.fire({
                title: 'Borrar',
                text: "Eliminar el Registro",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id)
                }
            })
        }

        function borrarRegistro(id){

            let formData = new FormData();
            formData.append('id', id);
            openLoading();

            axios.post('/admin/solicitud/redvial/borrarregistro', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Borrado');
                        recargar();
                    }
                    else{
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }

    </script>


@endsection
