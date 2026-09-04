<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión</title>
    @vite([
        'resources/css/app.scss',
        'resources/scss/light/assets/main.scss',
        'resources/js/app.js',
    ])
    @livewireStyles

    <style>
        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background: #fafafa;
        }
        .auth-container {
            min-height: 100vh;
            display: flex;
            position: relative;
        }
        .auth-fixed-bg {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 50%;
            background-image: linear-gradient(-225deg, #231557 0%, #44107A 29%, rgb(255 19 97 / 75%) 100%);
            z-index: 1;
        }
        .auth-container .container {
            max-width: 1440px;
            position: relative;
            z-index: 2;
        }
        .login-card {
            border: none;
            box-shadow: none;
            background: transparent;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-fixed-bg d-none d-lg-block"></div>
        <div class="container mx-auto align-self-center py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 d-none d-lg-flex flex-column align-items-center text-center">
                    <img src="{{ asset('images/auth-cover.svg') }}" alt="" style="width:320px; max-width:80%;">
                    <h2 class="mt-4 text-white fw-bold">Peregrinos y Extranjeros</h2>
                    <p class="text-white-50">Panel de administración</p>
                </div>

                <div class="col-lg-5 col-md-8 col-12 mx-auto">
                    <div class="card login-card">
                        <div class="card-body">
                            <h2 class="fw-bold mb-1">Iniciar sesión</h2>
                            <p class="text-muted mb-4">Ingresa tu correo y contraseña para entrar</p>
                            <livewire:auth.login />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
