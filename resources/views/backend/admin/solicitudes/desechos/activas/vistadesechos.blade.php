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
                    <li class="breadcrumb-item active">Desechos</li>
                </ol>
            </div>
        </div>




    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado Desechos Activas</h3>
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

            var ruta = "{{ URL::to('/admin/solicitud/desechos/tabla') }}";
            $('#tablaDatatable').load(ruta);

            countdown();
            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/solicitud/desechos/tabla') }}";
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

            axios.post('/admin/solicitud/desechos/finalizar', formData, {
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

            console.log('pass');

            tableRows.forEach(function(row) {
                var checkbox = row.querySelector('.checkbox');
                if(checkbox != null){
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

            window.open("{{ URL::to('admin/solicitud/desechos/reportevarios') }}/" + reemplazo);
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


    </script>


@endsection
