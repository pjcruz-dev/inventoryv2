<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap');
        
        :root {
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --spacing-3xl: 4rem;
            
            --text-xs: 0.75rem;
            --text-sm: 0.875rem;
            --text-base: 1rem;
            --text-lg: 1.125rem;
            --text-xl: 1.25rem;
            --text-2xl: 1.5rem;
            --text-3xl: 1.875rem;
            --text-4xl: 2.25rem;
        }
        
        body {
            background: linear-gradient(310deg, #f8f9fa 0%, #dee2e6 100%);
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
            line-height: 1.6;
            font-size: var(--text-base);
            color: #344767;
            display: flex;
            flex-direction: column;
        }
        
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .login-card {
            border: none;
            border-radius: 1.5rem;
            background: #f8f9fa;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            max-width: 450px;
            width: 100%;
        }
        
        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 
                12px 12px 20px rgba(163, 177, 198, 0.7),
                -12px -12px 20px rgba(255, 255, 255, 0.6),
                0 8px 32px rgba(31, 38, 135, 0.15);
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 1.5rem;
            background: linear-gradient(145deg, rgba(255,255,255,0.1), rgba(0,0,0,0.05));
            pointer-events: none;
        }
        
        .card-header {
            background: transparent;
            border: none;
            text-align: center;
            padding: 2rem 2rem 1rem;
            font-size: var(--text-2xl);
            font-weight: 600;
            color: #344767;
        }
        
        .card-body {
            padding: 1rem 2rem 2rem;
        }
        
        .form-control {
            border: 1px solid #d2d6da;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: var(--text-base);
            transition: all 0.15s ease-in;
            background: rgba(255, 255, 255, 0.8);
        }
        
        .form-control:focus {
            border-color: #cb0c9f;
            box-shadow: 0 0 0 0.2rem rgba(203, 12, 159, 0.25);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .form-label {
            font-weight: 600;
            color: #344767;
            margin-bottom: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(310deg, #cb0c9f 0%, #ad1457 100%);
            border: none;
            border-radius: 0.75rem;
            box-shadow: 
                0 4px 7px -1px rgba(203, 12, 159, 0.11),
                0 2px 4px -1px rgba(203, 12, 159, 0.07);
            transition: all 0.15s ease-in;
            position: relative;
            overflow: hidden;
            padding: 0.75rem 2rem;
            font-weight: 600;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: linear-gradient(310deg, #ad1457 0%, #880e4f 100%);
            transform: translateY(-1px);
            box-shadow: 
                0 7px 14px -3px rgba(203, 12, 159, 0.15),
                0 4px 6px -2px rgba(203, 12, 159, 0.1);
        }
        
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 
                inset 0 3px 5px rgba(0,0,0,0.125),
                0 2px 4px rgba(203, 12, 159, 0.1);
        }
        
        .btn-link {
            color: #cb0c9f;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.15s ease-in;
        }
        
        .btn-link:hover {
            color: #ad1457;
            text-decoration: underline;
        }
        
        .form-check-input:checked {
            background-color: #cb0c9f;
            border-color: #cb0c9f;
        }
        
        .form-check-input:focus {
            border-color: #cb0c9f;
            box-shadow: 0 0 0 0.25rem rgba(203, 12, 159, 0.25);
        }
        
        .invalid-feedback {
            display: block;
            font-size: var(--text-sm);
            color: #dc3545;
            margin-top: 0.25rem;
        }
        
        .is-invalid {
            border-color: #dc3545;
        }
        
        .is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .footer {
            background: transparent;
            padding: 1rem;
            text-align: center;
            color: #67748e;
            font-size: var(--text-sm);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            margin-top: auto;
        }
        
        .brand-title {
            font-size: var(--text-3xl);
            font-weight: 700;
            color: #344767;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .brand-subtitle {
            color: #67748e;
            font-size: var(--text-sm);
            text-align: center;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .card-header {
                padding: 1.5rem 1.5rem 1rem;
                font-size: var(--text-xl);
            }
            
            .card-body {
                padding: 1rem 1.5rem 1.5rem;
            }
            
            .brand-title {
                font-size: var(--text-2xl);
            }
        }
    </style>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="login-container">
        <div class="login-card card">
            <div class="card-header">
                <div class="brand-title">{{ config('app.name', 'ICTAssetV2') }}</div>
                <div class="brand-subtitle">Inventory Management System</div>
                {{ __('Login') }}
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            © {{ date('Y') }} {{ config('app.name', 'ICTAssetV2') }}. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
