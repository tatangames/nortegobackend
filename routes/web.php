<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Login\LoginController;
use App\Http\Controllers\Backend\Controles\ControlController;
use App\Http\Controllers\Backend\Perfil\PerfilController;
use App\Http\Controllers\Backend\Roles\PermisoController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\Configuracion\Estadisticas\EstadisticasAdminController;
use App\Http\Controllers\Backend\Configuracion\Slider\SliderController;
use App\Http\Controllers\Backend\Configuracion\Usuario\UsuarioController;
use App\Http\Controllers\Backend\Configuracion\Servicios\ServiciosController;
use App\Http\Controllers\Backend\Solicitud\SolicitudUsuarioController;


Route::get('/', [LoginController::class,'index'])->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---
Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

// --- ROLES ---
Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

// --- PERMISOS ---
Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

// --- SIN PERMISOS VISTA 403 ---
Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');

// --- PERFIL ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);


// --- ESTADISTICAS ---
Route::get('/admin/estadisticas/administrador', [EstadisticasAdminController::class,'indexEstadisticaAdmin'])->name('admin.estadisticas.administrador');

// --- SLIDER ---
Route::get('/admin/slider/index', [SliderController::class,'indexSlider'])->name('admin.slider.editor');
Route::get('/admin/slider/tabla', [SliderController::class,'tablaSlider']);
Route::post('/admin/slider/nuevo', [SliderController::class, 'nuevoSlider']);
Route::post('/admin/slider/informacion', [SliderController::class, 'informacionSlider']);
Route::post('/admin/slider/posicion', [SliderController::class, 'actualizarPosicionSlider']);
Route::post('/admin/slider/editar', [SliderController::class, 'editarSlider']);
Route::post('/admin/slider/borrar', [SliderController::class, 'borrarSlider']);

// --- CATEGORIA DE SERVICIOS ---
Route::get('/admin/categoria/index', [ServiciosController::class,'indexTipoServicios'])->name('admin.tiposervicios.editor');
Route::get('/admin/categoria/tabla', [ServiciosController::class,'tablaTipoServicios']);
Route::post('/admin/categoria/nuevo', [ServiciosController::class, 'nuevoTipoServicios']);
Route::post('/admin/categoria/informacion', [ServiciosController::class, 'informacionTipoServicios']);
Route::post('/admin/categoria/posicion', [ServiciosController::class, 'actualizarPosicionTipoServicios']);
Route::post('/admin/categoria/editar', [ServiciosController::class, 'editarTipoServicios']);

// --- SERVICIOS ---
Route::get('/admin/servicios/index/{idcategoria}', [ServiciosController::class,'indexServicios']);
Route::get('/admin/servicios/tabla/{idcategoria}', [ServiciosController::class,'tablaServicios']);
Route::post('/admin/servicios/nuevo', [ServiciosController::class, 'nuevoServicios']);
Route::post('/admin/servicios/informacion', [ServiciosController::class, 'informacionServicios']);
Route::post('/admin/servicios/posicion', [ServiciosController::class, 'actualizarPosicionServicios']);
Route::post('/admin/servicios/editar', [ServiciosController::class, 'editarServicios']);


// --- USUARIOS ---
Route::get('/admin/usuarios/index', [UsuarioController::class,'indexUsuario'])->name('admin.usuarios.admin');
Route::get('/admin/usuarios/tabla', [UsuarioController::class,'tablaUsuario']);
Route::post('/admin/usuarios/informacion', [UsuarioController::class, 'informacionUsuario']);
Route::post('/admin/usuarios/editar', [UsuarioController::class, 'editarUsuario']);
Route::get('/admin/usuarios/sms/index/{id}', [UsuarioController::class,'indexSMSEnviados']);
Route::get('/admin/usuarios/sms/tabla/{id}', [UsuarioController::class,'tablaSMSEnviados']);


// --- SOLICITUDES DE RED VIALES - ACTIVAS ---
Route::get('/admin/solicitud/redvial/index', [SolicitudUsuarioController::class,'indexSolicitudRedVial'])->name('admin.solicitud.redvial.activa.index');
Route::get('/admin/solicitud/redvial/tabla', [SolicitudUsuarioController::class,'tablaSolicitudRedVial']);
Route::post('/admin/solicitud/basico/mapa', [SolicitudUsuarioController::class,'mapaSolicitudBasica']);
Route::post('/admin/solicitud/redvial/finalizar', [SolicitudUsuarioController::class,'finalizarSolicitudRedVial']);
Route::get('/admin/solicitud/redvial/reportevarios/{listado}', [SolicitudUsuarioController::class, 'reportePdfRedVialVarios']);


// --- SOLICITUDES DE RED VIALES - FINALIZADA ---
Route::get('/admin/solicitud/redvialfinalizada/index', [SolicitudUsuarioController::class,'indexSolicitudRedVialFinalizada'])->name('admin.solicitud.redvial.finalizada.index');
Route::get('/admin/solicitud/redvialfinalizada/tabla', [SolicitudUsuarioController::class,'tablaSolicitudRedVialFinalizada']);


// --- SOLICITUDES DE ALUMBRADO ELECTRICO - ACTIVAS ---
Route::get('/admin/solicitud/alumbrado/index', [SolicitudUsuarioController::class,'indexSolicitudAlumbrado'])->name('admin.solicitud.alumbrado.activa.index');
Route::get('/admin/solicitud/alumbrado/tabla', [SolicitudUsuarioController::class,'tablaSolicitudAlumbrado']);
Route::post('/admin/solicitud/alumbrado/finalizar', [SolicitudUsuarioController::class,'finalizarSolicitudAlumbrado']);
Route::get('/admin/solicitud/alumbrado/reportevarios/{listado}', [SolicitudUsuarioController::class, 'reportePdfAlumbradoVarios']);


// --- SOLICITUDES DE ALUMBRADO ELECTRICO - FINALIZADA ---
Route::get('/admin/solicitud/alumbradofinalizada/index', [SolicitudUsuarioController::class,'indexSolicitudAlumbradoFinalizada'])->name('admin.solicitud.alumbrado.finalizada.index');
Route::get('/admin/solicitud/alumbradofinalizada/tabla', [SolicitudUsuarioController::class,'tablaSolicitudAlumbradoFinalizada']);


// --- SOLICITUDES DE DESECHOS SOLIDOS - ACTIVAS ---
Route::get('/admin/solicitud/desechos/index', [SolicitudUsuarioController::class,'indexSolicitudDesechos'])->name('admin.solicitud.desechos.activa.index');
Route::get('/admin/solicitud/desechos/tabla', [SolicitudUsuarioController::class,'tablaSolicitudDesechos']);
Route::post('/admin/solicitud/desechos/finalizar', [SolicitudUsuarioController::class,'finalizarSolicitudDesechos']);
Route::get('/admin/solicitud/desechos/reportevarios/{listado}', [SolicitudUsuarioController::class, 'reportePdfDesechosVarios']);

// --- SOLICITUDES DE DESECHOS SOLIDOS - FINALIZADA ---
Route::get('/admin/solicitud/desechosfinalizada/index', [SolicitudUsuarioController::class,'indexSolicitudDesechosFinalizada'])->name('admin.solicitud.desechos.finalizada.index');
Route::get('/admin/solicitud/desechosfinalizada/tabla', [SolicitudUsuarioController::class,'tablaSolicitudDesechosFinalizada']);

// --- MEDIO AMBIENTE: SOLICITUD TALA ARBOL
Route::get('/admin/mediambiente/solicitud/talaarbol/index', [SolicitudUsuarioController::class,'indexSolicitudTalaArbol'])->name('admin.solicitud.tala.arbol');
Route::get('/admin/mediambiente/solicitud/talaarbol/tabla', [SolicitudUsuarioController::class,'tablaSolicitudTalaArbol']);
Route::post('/admin/mediambiente/solicitud/informacion', [SolicitudUsuarioController::class,'informacionSolicitudTalaArbol']);
Route::post('/admin/mediambiente/solicitud/mapa', [SolicitudUsuarioController::class,'mapaSolicitudTalaArbol']);
Route::post('/admin/mediambiente/solicitud/finalizar', [SolicitudUsuarioController::class,'finalizarSolicitudTalaArbol']);

Route::get('/admin/mediambiente/solicitud-finalizada/talaarbol/index', [SolicitudUsuarioController::class,'indexSolicitudFinalizadaTalaArbol'])->name('admin.solicitud.finalizada.tala.arbol');
Route::get('/admin/mediambiente/solicitud-finalizada/talaarbol/tabla', [SolicitudUsuarioController::class,'tablaSolicitudFinalizadaTalaArbol']);

// --- DENUNCIA DE TALA DE ARBOL ---

Route::get('/admin/mediambiente/denuncia/talaarbol/index', [SolicitudUsuarioController::class,'indexDenunciaTalaArbol'])->name('admin.denuncia.tala.arbol');
Route::get('/admin/mediambiente/denuncia/talaarbol/tabla', [SolicitudUsuarioController::class,'tablaDenunciaTalaArbol']);

// --- SOLICITUDES DE CATASTRO ---
Route::get('/admin/catastro/activos/index', [SolicitudUsuarioController::class,'indexCatastroActivas'])->name('admin.catastro.activas');
Route::get('/admin/catastro/activos/tabla', [SolicitudUsuarioController::class,'tablaCatastroActivas']);
Route::post('/admin/catastro/solicitud/mapa', [SolicitudUsuarioController::class,'mapaCatastral']);
Route::post('/admin/catastro/actualizar/estado', [SolicitudUsuarioController::class,'actualizarEstadoCatastral']);

// --- FINALIZADAS DE CATASTRO ---
Route::get('/admin/catastro/finalizada/index', [SolicitudUsuarioController::class,'indexCatastroFinalizada'])->name('admin.catastro.finalizadas');
Route::get('/admin/catastro/finalizada/tabla', [SolicitudUsuarioController::class,'tablaCatastroFinalizada']);


// --- SOPORTE ACTUALIZACIONES ---
Route::get('/admin/soporteactualizaciones/index', [PerfilController::class,'indexSoporteActualizacion'])->name('admin.soporte.actualizaciones');
Route::post('/admin/soporteactualizaciones/actualizar', [PerfilController::class,'SoporteActualizacionUpdate']);




