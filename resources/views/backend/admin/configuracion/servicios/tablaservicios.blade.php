<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Imagen</th>
                            <th>Posición</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody id="tablecontents">
                        @foreach($listado as $dato)
                            <tr class="row1" data-id="{{ $dato->id }}">

                                <td>{{ $dato->id }}</td>
                                <td>{{ $dato->nombre }}</td>
                                <td>{{ $dato->descripcion }}</td>
                                <td>
                                    @if($dato->activo == 1)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>

                                <td>
                                    <center><img alt="logo" src="{{ url('storage/archivos/'.$dato->imagen) }}"  width="75px" height="75px" /></center>
                                </td>
                                <td>{{ $dato->posicion }}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-xs" onclick="informacion({{ $dato->id }})">
                                        <i class="fas fa-eye" title="Información"></i>&nbsp; Información
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
</section>

<script type="text/javascript">
    $(document).ready(function() {

        $( "#tablecontents" ).sortable({
            items: "tr",
            cursor: 'move',
            opacity: 0.6,
            update: function() {
                sendOrderToServer();
            }
        });

        function sendOrderToServer() {

            var order = [];
            $('tr.row1').each(function(index,element) {
                order.push({
                    id: $(this).attr('data-id'),
                    posicion: index+1
                });
            });

            openLoading();

            axios.post('/admin/servicios/posicion',  {
                'order': order
            })
                .then((response) => {
                    closeLoading();
                    toastr.success('Actualizado correctamente');
                    recargar();
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al actualizar');
                });
        }
    });

</script>
