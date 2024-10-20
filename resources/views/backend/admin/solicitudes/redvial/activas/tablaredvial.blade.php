<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 6%">Reporte</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Nota</th>
                                <th>Imagen</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($listado as $dato)
                                <tr data-info="{{ $dato->id }}">
                                    <td style="width: 6%">

                                        <input type="checkbox" class="checkbox" style="width: 40px; height: 20px" />

                                    </td>
                                    <td>{{ $dato->fechaFormat }}</td>
                                    <td>{{ $dato->horaFormat }}</td>
                                    <td>{{ $dato->nota }}</td>

                                    <td style="text-align: center">
                                        <div class="col-md-12 animate-box">
                                            <img class="img-responsive img-fluid" src="{{ asset('storage/archivos/'.$dato->imagen)}}" alt="Imagen" data-toggle="modal" width="125px" height="125px" data-target="#modal1" onclick="getPath(this)">
                                        </div>
                                    <td>

                                        <button type="button" class="btn btn-success btn-xs" onclick="modalFinalizar({{ $dato->id }})">
                                            <i class="fas fa-check" title="Finalizar"></i>&nbsp; Finalizar
                                        </button>

                                        <button type="button" style="margin-left: 5px" class="btn btn-info btn-xs" onclick="vistaMapa({{ $dato->id }})">
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
            columnDefs: [
                { type: 'date-euro', targets: 0 } // Suponiendo que la columna de fecha es la primera (índice 0)
            ],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",
            "lengthMenu": [[100, 150, -1], [100, 150, "Todo"]],
            "language": {

                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
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
            "responsive": true, "lengthChange": true, "autoWidth": false,
        });
    });


</script>
