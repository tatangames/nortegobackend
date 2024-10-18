@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Datos</h1>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid" style="margin-left: 15px">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-gray-dark">
                    <div class="card-header">
                        <h3 class="card-title">Formulario Actualizaciones</h3>
                    </div>
                    <form>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Version Android</label>
                                <input type="text" id="version-android" class="form-control" maxlength="50"  value="{{ $info->version_android }}">
                            </div>

                            <div class="form-group">
                                <label>Version IOS</label>
                                <input type="text" id="version-ios" class="form-control" maxlength="50"  value="{{ $info->version_ios }}">
                            </div>


                            <div class="form-group">
                                <label>Activar para Android</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="toggle-android">
                                    <div class="slider round">
                                        <span class="on">Activo</span>
                                        <span class="off">Inactivo</span>
                                    </div>
                                </label>
                            </div>


                            <div class="form-group">
                                <label>Activar para IOS</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="toggle-ios">
                                    <div class="slider round">
                                        <span class="on">Activo</span>
                                        <span class="off">Inactivo</span>
                                    </div>
                                </label>
                            </div>


                        </div>

                        <div class="card-footer" style="float: right;">
                            <button type="button" class="btn btn-primary" onclick="actualizar()">Actualizar</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</section>

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

            let estadoAndroid = {{ $info->android_modal }};
            let estadoIos = {{ $info->ios_modal }};

            if(estadoAndroid == '1'){
                $("#toggle-android").prop("checked", true);
            }

            if(estadoIos == '1'){
                $("#toggle-ios").prop("checked", true);
            }

        });
    </script>


    <script>

        function actualizar(){

            var versionAndroid = document.getElementById('version-android').value;
            var versionIos = document.getElementById('version-ios').value;

            let tandroid = document.getElementById('toggle-android').checked;
            let toggleAndroid = tandroid ? 1 : 0;

            let tios = document.getElementById('toggle-ios').checked;
            let toggleIos = tios ? 1 : 0;

            if(versionAndroid === ''){
                toastr.error('Versión Android es requerida');
                return;
            }

            if(versionIos === ''){
                toastr.error('Versión IOS es requerida');
                return;
            }


            openLoading()
            var formData = new FormData();
            formData.append('versionandroid', versionAndroid);
            formData.append('versionios', versionIos);
            formData.append('toggleandroid', toggleAndroid);
            formData.append('toggleios', toggleIos);

            axios.post('/admin/soporteactualizaciones/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading()

                    if (response.data.success === 1) {
                        toastr.success('Actualizado');
                    }
                    else {
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('error al actualizar');
                });
        }
    </script>



@stop
