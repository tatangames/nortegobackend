<!DOCTYPE html>
<html lang="es">

<head>
    <title>Alcaldía Metapán</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/login/bootstrap.min.css') }}">

    <!-- icono del sistema -->
    <link href="{{ asset('images/icono-sistema.png') }}" rel="icon">
    <!-- libreria -->
    <link href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" type="text/css" rel="stylesheet" />

    <!-- estilo de toast -->
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <!-- estilo de sweet -->
    <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet">

    <style>
        html, body {
            height: 90%;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url({{ asset('images/fondo4.jpg') }});
            background-size: cover;
            background-repeat: no-repeat;

        }

        .demo-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-lg {
            padding: 12px 26px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        ::placeholder {
            font-size:14px;
            letter-spacing:0.5px;
        }

        .form-control-lg {
            font-size: 16px;
            padding: 25px 20px;
        }
        .font-500{
            font-weight:500;
        }
        .image-size-small{
            width:140px;
            margin:0 auto;
        }
        .image-size-small img{
            width:140px;
            margin-bottom:-70px;
        }

    </style>
</head>

<body>
<div class="container">
    <div>
        <div class="demo-container" style="margin-top: 25px">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12 mx-auto">
                        <div class="text-center image-size-small position-relative">
                            <img src="{{ asset('images/logo.png') }}" class="rounded-circle p-2 bg-white">
                        </div>
                        <div class="p-5 bg-white rounded shadow-lg">
                            <h3 class="mb-2 text-center pt-5"><strong>Alcaldía Municipal de Metapán</strong></h3>
                            <p class="text-center lead">Distrito Santa Ana Norte App</p>
                            <form>
                                <label style="margin-top: 10px" class="font-500">Usuario</label>
                                <input class="form-control form-control-lg mb-3" id="usuario" maxlength="50" autocomplete="off" type="text">
                                <label class="font-500">Contraseña</label>
                                <input class="form-control form-control-lg" id="password" maxlength="30" type="password" autocomplete="off">

                                <input type="button" value="Entrar" style="margin-top: 35px" onclick="login()" class="btn btn-primary btn-lg w-100 shadow-lg">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/alertaPersonalizada.js') }}"></script>


<script type="text/javascript">

    var inputPassword = document.getElementById("password");
    var inputUsuario = document.getElementById("usuario");

    inputPassword.addEventListener("keyup", function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            login();
        }
    });

    inputUsuario.addEventListener("keyup", function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            login();
        }
    });

    // inicio de sesion
    function login() {

        var usuario = document.getElementById('usuario').value;
        var password = document.getElementById('password').value;

        if(usuario === ''){
            toastr.error('Usuario es requerido');
            return;
        }

        if(password === ''){
            toastr.error('Contraseña es requerida');
            return;
        }

        openLoading();

        let formData = new FormData();
        formData.append('usuario', usuario);
        formData.append('password', password);


        axios.post('/login', formData, {
        })
            .then((response) => {
                closeLoading();

                if (response.data.success === 0) {
                    toastr.error('Validación incorrecta')
                } else if (response.data.success === 1) {
                    window.location = response.data.ruta;
                } else if (response.data.success === 2) {
                    toastr.error('Contraseña incorrecta');
                } else if (response.data.success === 3) {
                    toastr.error('Usuario no encontrado')
                } else {
                    toastr.error('Error al iniciar sesión');
                }

            })
            .catch((error) => {
                toastr.error('error al iniciar sesión');
                closeLoading();
            });
    }


</script>
</body>

</html>
