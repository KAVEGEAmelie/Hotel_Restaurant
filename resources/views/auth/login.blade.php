<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion - {{ config('app.name', 'Laravel') }}</title>

    <!-- Polices de caractères -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* CSS Professionnel Intégré */
        :root {
            --color-vert-foret: #228B22;
            --color-marron-titre: #6F4E37;
            --color-dore-leger: #D4AF37;
        }
        html { height: 100%; }
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            background-image: url('{{ asset('assets/img/login-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            text-align: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .login-logo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: -90px auto 20px auto; /* Fait remonter le logo */
        }
        .login-card h1 {
            font-family: 'Playfair Display', serif;
            color: var(--color-marron-titre);
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        .form-control {
            width: 100%;
            padding: 12px 12px 12px 45px; /* Espace pour l'icône */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box; /* Important */
        }
        .form-control:focus {
            outline: none;
            border-color: var(--color-vert-foret);
            box-shadow: 0 0 0 3px rgba(34, 139, 34, 0.2);
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: var(--color-vert-foret);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background-color: #1a681a;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-top: 1.5rem;
        }
        .login-options a {
            color: #555;
            text-decoration: none;
        }
        .login-options a:hover {
            text-decoration: underline;
        }
        .form-check-label {
            color: #555;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <a href="/"><img src="{{ asset('assets/img/logo.jpg') }}" alt="Logo Hôtel Le Printemps"></a>
            </div>
            <h1>Accès Administration</h1>

            <!-- Session Status -->
            @if (session('status'))
                <div style="color: green; margin-bottom: 1rem;">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Erreurs de validation -->
            @if ($errors->any())
                <div style="color: red; margin-bottom: 1rem; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email -->
                <div class="form-group">
                    <i class="bi bi-envelope"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email" class="form-control">
                </div>
                <!-- Mot de passe -->
                <div class="form-group">
                    <i class="bi bi-lock"></i>
                    <input id="password" type="password" name="password" required placeholder="Mot de passe" class="form-control">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn-submit">
                        Se connecter
                    </button>
                </div>

                <div class="login-options">
                    <label for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                        <span class="form-check-label">Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</body>
</html>
