<?php

namespace App\Http\Controllers\Backend\Roles;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisoController extends Controller
{
    public function __construct(){
        $this->middleware('auth:admin');
    }

    public function index(){
        $roles = Role::all()->pluck('name', 'id');

        return view('backend.admin.rolesypermisos.permisos', compact('roles'));
    }

    public function tablaUsuarios(){
        $usuarios = Administrador::orderBy('id', 'ASC')->get();

        return view('backend.admin.rolesypermisos.tabla.tablapermisos', compact('usuarios'));
    }

    public function nuevoUsuario(Request $request){

        $regla = array(
            'nombre' => 'required',
            'usuario' => 'required',
            'password' => 'required',
            'rol' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();

        try {
            if(Administrador::where('usuario', $request->usuario)->first()){
                // usuario repetido
                return ['success' => 1];
            }

            $registro = new Administrador();
            $registro->nombre = $request->nombre;
            $registro->usuario = $request->usuario;
            $registro->password = Hash::make($request->password);
            $registro->activo = 1;
            $registro->save();

            $role = Role::findById($request->rol, 'api');
            $registro->assignRole($role->name);

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoUsuario(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        if($info = Administrador::where('id', $request->id)->first()){

            $roles = Role::all()->pluck('name', 'id');

            $idrol = $info->roles->pluck('id');

            return ['success' => 1,
                'info' => $info,
                'roles' => $roles,
                'idrol' => $idrol];

        }else{
            return ['success' => 2];
        }
    }

    public function editarUsuario(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'usuario' => 'required',
            'rol' => 'required',
            'toggle' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();

        try {

            if(Administrador::where('id', $request->id)->first()){

                // usuario repetido
                if(Administrador::where('usuario', $request->usuario)
                    ->where('id', '!=', $request->id)->first()){
                    return ['success' => 1];
                }

                $usuario = Administrador::find($request->id);
                $usuario->nombre = $request->nombre;
                $usuario->usuario= $request->usuario;
                $usuario->activo = $request->toggle;

                if($request->password != null){
                    // actualizar contraseña
                    $usuario->password = Hash::make($request->password);
                }

                $role = Role::findById($request->rol, 'api');
                $usuario->syncRoles($role->name);
                $usuario->save();

                DB::commit();
                return ['success' => 2];
            }else{
                // usuario no encontrado
                return ['success' => 99];
            }
        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function nuevoRol(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // verificar si existe el rol
        if(Role::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Role::create(['name' => $request->nombre, 'guard_name' => 'api']);

        return ['success' => 2];
    }

    public function nuevoPermisoExtra(Request $request){

        // verificar si existe el permiso
        if(Permission::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Permission::create(['name' => $request->nombre, 'guard_name' => 'api', 'description' => $request->descripcion]);

        return ['success' => 2];
    }

    public function borrarPermisoGlobal(Request $request){

        // buscamos el permiso el cual queremos eliminar
        $permission = Permission::findById($request->idpermiso)->delete();

        return ['success' => 1];
    }


}

