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
        // Plug-in para ordenar el formato de fecha y hora 'dd-mm-yyyy hh:mm AM/PM'
        $.fn.dataTable.ext.order['datetime-ddmmyyyy-hhmm'] = function(settings, colIdx) {
            return this.api().column(colIdx, { order: 'index' }).nodes().map(function(td, i) {
                var dateStr = $(td).text().trim();
                var dateParts = dateStr.match(/(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2}) (AM|PM)/);

                if (!dateParts) {
                    return 0;
                }

                var day = dateParts[1];
                var month = dateParts[2];
                var year = dateParts[3];
                var hour = parseInt(dateParts[4]);
                var minute = dateParts[5];
                var ampm = dateParts[6];

                // Convertir el formato de 12 horas a 24 horas
                if (ampm === "PM" && hour < 12) hour += 12;
                if (ampm === "AM" && hour === 12) hour = 0;

                return new Date(year, month - 1, day, hour, minute).getTime();
            });
        };

        // Inicializar la tabla DataTable con orden descendente en la columna de Fecha
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
            "order": [[0, 'desc']],  // Ordena la primera columna (Fecha) de forma descendente
            "columnDefs": [
                { "orderDataType": "datetime-ddmmyyyy-hhmm", "targets": [0] } // Aplica a la columna de Fecha
            ]
        });
    });
</script>
