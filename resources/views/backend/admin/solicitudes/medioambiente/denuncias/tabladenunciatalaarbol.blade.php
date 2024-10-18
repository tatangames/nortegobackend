<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>

                                <th>Fecha</th>
                                <th>Nota</th>
                                <th>Imagen</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr data-info="{{ $dato->id }}">

                                    <td>{{ $dato->fechaFormat }}</td>
                                    <td>{{ $dato->nota }}</td>

                                    <td style="text-align: center">
                                        <div class="col-md-12 animate-box">
                                            <img class="img-responsive img-fluid" src="{{ asset('storage/archivos/'.$dato->imagen)}}" alt="Imagen" data-toggle="modal" width="125px" height="125px" data-target="#modal1" onclick="getPath(this)">
                                        </div>

                                    </td>
                                    <td>

                                        <button type="button" style="margin: 5px" class="btn btn-primary btn-xs" onclick="modalInformacion({{ $dato->id }})">
                                            <i class="fas fa-info" title="Información"></i>&nbsp; Información
                                        </button>

                                        <button type="button" style="margin: 5px" class="btn btn-info btn-xs" onclick="vistaMapa({{ $dato->id }})">
                                            <i class="fas fa-map" title="Mapa"></i>&nbsp; Mapa
                                        </button>

                                        <button style="margin: 6px" type="button" class="btn btn-danger btn-xs" onclick="modalBorrar({{ $dato->id }})">
                                            <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
                                        </button>

                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(function () {

        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",
            "lengthMenu": [[10, 25, 50, 100, 150, -1], [10, 25, 50, 100, 150, "Todo"]],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "columnDefs": [
                {
                    "targets": 0, // La columna de fechas
                    "render": function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return moment(data, 'DD-MM-YYYY hh:mm A').format('YYYYMMDDHHmm');
                        }
                        return data;
                    }
                }
            ]
        });
    });


</script>
