<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/icono-sistema.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight" style="color: white">NorteGo</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                @can('sidebar.roles.y.permisos')

                 <li class="nav-item">
                     <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Roles y Permisos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.permisos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permisos</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.usuarios.admin') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>

                    </ul>
                 </li>

                @endcan




                    <li class="nav-item">
                        <a href="{{ route('admin.estadisticas.administrador') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Estadísticas</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.slider.editor') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Slider</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.tiposervicios.editor') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Categoría Servicio</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-edit"></i>
                            <p>
                                Red Viales
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.redvial.activa.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Activas</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.redvial.finalizada.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Finalizadas</p>
                                </a>
                            </li>


                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-edit"></i>
                            <p>
                                Alumbrado Eléctrico
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.alumbrado.activa.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Activas</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.alumbrado.finalizada.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Finalizadas</p>
                                </a>
                            </li>


                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-edit"></i>
                            <p>
                                Desechos Sólidos
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.desechos.activa.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Activas</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.desechos.finalizada.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Finalizadas</p>
                                </a>
                            </li>


                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-edit"></i>
                            <p>
                                Solicitud Tala
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{ route('admin.solicitud.tala.arbol') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Solicitudes Activas</p>
                                </a>
                            </li>




                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-edit"></i>
                            <p>
                                Denuncia Tala
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">





                        </ul>
                    </li>




            </ul>
        </nav>

    </div>
</aside>






