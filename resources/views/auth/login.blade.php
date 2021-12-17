<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metas -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://ajifatur.github.io/assets/css/spandiv.css">

    <title>Log in</title>
    <style>
        body, body main {
            min-height: 100vh;
        }
        .login-box {
            text-align: center;
            width: 75%;
            margin: auto;
        }
    </style>
</head>
<body>
    <main class="d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6">
                    <form class="login-box" method="post" action="{{ route('auth.login') }}">
                        @csrf
                        <!-- <a href="/"><img class="mb-4" src="https://campusnet.id/assets/images/logo/campusnet.webp" alt="" height="60"></a> -->
                        <h1 class="h3 mb-3 fw-normal">Selamat Datang</h1>
                        @if($errors->has('message'))
                        <div class="alert alert-danger" role="alert">{{ $errors->first('message') }}</div>
                        @endif
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control {{ $errors->has('username') ? 'border-danger' : '' }}" value="{{ old('username') }}" placeholder="Email / Username" autofocus>
                            @if($errors->has('username'))
                            <div class="small text-danger text-start">{{ $errors->first('username') }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'border-danger' : '' }}" placeholder="Password">
                                <button type="button" class="btn {{ $errors->has('password') ? 'btn-outline-danger' : 'btn-outline-secondary' }} btn-toggle-password"><i class="bi-eye"></i></button>
                            </div>
                            @if($errors->has('password'))
                            <div class="small text-danger text-start">{{ $errors->first('password') }}</div>
                            @endif
                        </div>
                        <button class="w-100 btn btn-primary" type="submit">Log in</button>
                        @if(config('faturhelper.auth.socialite') == true)
                        <div class="btn-group mt-3">
                            <a href="{{ route('auth.login.provider', ['provider' => 'google']) }}" class="btn btn-outline-primary">Google</a>
                            <a href="{{ route('auth.login.provider', ['provider' => 'facebook']) }}" class="btn btn-outline-primary">Facebook</a>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="col-12 col-lg-6 d-none d-lg-block">
                    <img src="https://campusdigital.id/assets/images/illustration/undraw_Login_re_4vu2.svg" alt="img" class="img-fluid">
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajifatur.github.io/assets/spandiv.min.js"></script>
    <script>
        // Enable Everywhere
        Spandiv.EnableEverywhere();
    </script>
</body>
</html>