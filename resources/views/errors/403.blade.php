<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso no autorizado</title>
    <style>
        body.error {
            color: #888ea8;
            height: 100%;
            font-size: 0.875rem;
            background: #fafafa;
            background-image: linear-gradient(to bottom, #a8edea 0%, #fed6e3 100%);
            margin: 0;
        }
        body.error .error-content {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 30px;
        }
        .error .mini-text {
            font-size: 33px;
            font-weight: 700;
            margin-bottom: 0;
            color: #0e1726;
        }
        .error .error-img {
            max-width: 363px;
            margin-bottom: 50px;
            width: 100%;
        }
        .error .error-text {
            font-size: 18px;
            color: #0e1726;
            font-weight: 600;
        }
        .error a.btn {
            width: 134px;
            padding: 6px;
            font-size: 17px;
            border: none;
            letter-spacing: 2px;
            box-shadow: none;
            display: block;
            margin: 0 auto;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body class="error">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 mr-auto mt-5 text-md-left text-center">
                <a href="{{ route('home') }}" class="ms-md-5">
                    <img alt="logo" src="{{ asset('images/logo2.svg') }}" style="width:62px;height:62px;">
                </a>
            </div>
        </div>
    </div>
    <div class="container-fluid error-content">
        <div>
            <p class="mini-text">Acceso no autorizado</p>
            <p class="error-text mb-5 mt-1">No tienes permiso para ver esta página.</p>
            <img src="{{ asset('images/error.svg') }}" alt="acceso no autorizado" class="error-img">
            <a href="{{ route('login') }}" class="btn btn-dark mt-5">Iniciar sesión</a>
        </div>
    </div>
</body>
</html>
