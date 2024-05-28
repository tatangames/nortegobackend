<?php

namespace App\Http\Controllers\Backend\Login;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest', ['except' => ['logout']]);
    }

    public function index(){

        if (Auth::guard('admin')->check()) {
            return redirect('/panel');
        }

        return view('frontend.login.vistalogin');
    }


    public function login(Request $request){

        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // si ya habia iniciado sesion, redireccionar
        if (Auth::check()) {
            return ['success'=> 1, 'ruta'=> route('admin.panel')];
        }

        $credentials = request()->only('usuario', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            //return redirect()->route('admin.index');
            return ['success'=> 1, 'ruta'=> route('admin.panel')];
        } else {
            return ['success' => 2];
        }
    }

    public function logout(Request $request){
        Auth::guard('admin')->logout();
        return redirect('/');
    }
}
