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


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


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

// --- TIPO DE SERVICIOS ---
Route::get('/admin/tiposervicios/index', [ServiciosController::class,'indexTipoServicios'])->name('admin.tiposervicios.editor');
Route::get('/admin/tiposervicios/tabla', [ServiciosController::class,'tablaTipoServicios']);
Route::post('/admin/tiposervicios/nuevo', [ServiciosController::class, 'nuevoTipoServicios']);
Route::post('/admin/tiposervicios/informacion', [ServiciosController::class, 'informacionTipoServicios']);
Route::post('/admin/tiposervicios/posicion', [ServiciosController::class, 'actualizarPosicionTipoServicios']);
Route::post('/admin/tiposervicios/editar', [ServiciosController::class, 'editarTipoServicios']);



// --- SERVICIOS ---
Route::get('/admin/servicios/index', [ServiciosController::class,'indexServicios'])->name('admin.servicios.editor');
Route::get('/admin/servicios/tabla', [ServiciosController::class,'tablaServicios']);
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











