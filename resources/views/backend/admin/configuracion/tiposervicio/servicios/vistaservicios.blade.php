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
                <button type="button" onclick="modalAgregar()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-square"></i>
                    Nuevo registro
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Servicios</li>
                    <li class="breadcrumb-item active">Listado</li>
                </ol>
            </div>

        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
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

    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">


                                    <div class="form-group">
                                        <label class="control-label"></label>
                                        <select class="form-control" id="select-tiposervicio-nuevo">
                                            <option value="1">Servicios Basicos</option>
                                            <option value="2">Tala de Arboles</option>
                                            <option value="3">Denuncia Whatsapp</option>
                                            <option value="4">Catastro</option>
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input style="color:#191818" autocomplete="off" type="text" id="nombre-nuevo" class="form-control" maxlength="200" />
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción (Opcional)</label>
                                        <input style="color:#191818"  autocomplete="off" type="text" id="descripcion-nuevo" class="form-control" maxlength="200" />
                                    </div>

                                    <div class="form-group">
                                        <label>Imagen (Ejemplo 600px X -)</label>
                                        <div class="col-md-10">
                                            <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png" />
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar</h4>
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
                                        <label>Nombre</label>
                                        <input style="color:#191818" type="text" autocomplete="off" id="nombre-editar" class="form-control" maxlength="200" />
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción (Opcional)</label>
                                        <input style="color:#191818" autocomplete="off" type="text" id="descripcion-editar" class="form-control" maxlength="200" />
                                    </div>


                                    <div class="form-group">
                                        <label class="control-label">Tipo de Pantalla</label>
                                        <select class="form-control" id="select-tiposervicio-editar">
                                            <option value="1">Servicios Basicos</option>
                                            <option value="2">Tala de Arboles</option>
                                            <option value="3">Denuncia Whatsapp</option>
                                            <option value="4">Catastro</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Categoría</label>
                                        <select class="form-control" id="select-categoria-editar">

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Estado</label>
                                        <br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="toggle">
                                            <div class="slider round">
                                                <span class="on">Activo</span>
                                                <span class="off">Inactivo</span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="form-group" id="contenedorgps" style="display: none">
                                        <label>Bloqueo GPS</label>
                                        <br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="togglegps">
                                            <div class="slider round">
                                                <span class="on">Activo</span>
                                                <span class="off">Inactivo</span>
                                            </div>
                                        </label>
                                    </div>


                                    <div class="form-group">
                                        <label>Imagen (Ejemplo 600px X -)</label>
                                        <div class="col-md-10">
                                            <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png" />
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="editarRegistro()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            let id = {{ $idcategoria }};
            var ruta = "{{ URL::to('/admin/servicios/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            let id = {{ $idcategoria }};
            var ruta = "{{ URL::to('/admin/servicios/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevoRegistro(){
            var nombre = document.getElementById('nombre-nuevo').value;
            var descripcion = document.getElementById('descripcion-nuevo').value;
            var idtiposervicio = document.getElementById('select-tiposervicio-nuevo').value;
            var imagen = document.getElementById("imagen-nuevo");

            if(nombre === ''){
                toastr.error('Nombre es requerido')
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }else{
                toastr.error('Imagen es Requerida')
                return;
            }

            let idcategoria = {{ $idcategoria }};

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('idtiposervicio', idtiposervicio);
            formData.append('id_cateservicio', idcategoria);

            formData.append('imagen', imagen.files[0]);

            axios.post('/admin/servicios/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/admin/servicios/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#descripcion-editar').val(response.data.info.descripcion);

                        if(response.data.info.activo === 1){
                            $("#toggle").prop("checked", true);
                        }else{
                            $("#toggle").prop("checked", false);
                        }

                        const divGPS = document.getElementById('contenedorgps');

                        // ID DE ALGUNOS SERVICIOS PARA MODIFICAR SI TIENE BLOQUEO GPS,
                        // COMO DENUNCIAS BASICAS DE BACHES O ALUMBRADO ELECTRICO
                        const array1 = [1,2,6];
                        const isAvailable = array1.includes(id);

                        if(response.data.info.bloqueo_gps === 1){
                            $("#togglegps").prop("checked", true);
                        }else{
                            $("#togglegps").prop("checked", false);
                        }
                        if (isAvailable) {
                            divGPS.style.display = 'block';
                        }else{
                            divGPS.style.display = 'none';
                        }

                        // MOVER EL SELECT AL TIPO DE SERVICIO

                        let idpos = response.data.info.tiposervicio;
                        let miSelect = document.getElementById('select-tiposervicio-editar');

                        if(idpos === 1){
                            miSelect.options.selectedIndex = 0;
                        }
                        else if(idpos === 2){
                            miSelect.options.selectedIndex = 1;
                        }
                        else if(idpos === 3){
                            miSelect.options.selectedIndex = 2;
                        }
                        else if(idpos === 4){
                            miSelect.options.selectedIndex = 3;
                        }

                        document.getElementById("select-categoria-editar").options.length = 0;

                       $.each(response.data.arrayCategorias, function( key, val ){
                           if(response.data.info.id_cateservicio  == val.id){
                               $('#select-categoria-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                           }else{
                               $('#select-categoria-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                           }
                       });


                    }else{
                        toastr.error('Información no encontrada');
                    }

                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function editarRegistro(){
            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;
            var idCategoria = document.getElementById('select-categoria-editar').value;
            var idtipo = document.getElementById('select-tiposervicio-editar').value;
            var imagen = document.getElementById("imagen-editar");
            let t = document.getElementById('toggle').checked;
            let toggle = t ? 1 : 0;

            if(nombre === ''){
                toastr.error('Nombre es requerido')
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }


            var actualizarBloqueo = 0;
            var toggleGps = 0;

            // VERIFICAR SI ACTUALIZARA TOOGLE GPS BLOQUEO
            const myDiv = document.getElementById('contenedorgps');
            if (myDiv.style.display === 'block') {
                actualizarBloqueo = 1;
                let t2 = document.getElementById('togglegps').checked;
                toggleGps = t2 ? 1 : 0;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('idcategoria', idCategoria);
            formData.append('idtipo', idtipo);
            formData.append('toggle', toggle);
            formData.append('actualizargps', actualizarBloqueo);
            formData.append('togglegps', toggleGps);

            formData.append('imagen', imagen.files[0]);

            axios.post('/admin/servicios/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }






    </script>


@endsection
