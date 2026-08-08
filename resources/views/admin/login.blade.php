<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;

            background: #2563eb;
            color: #ffffff;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 28px;
            font-weight: bold;
        }

        .login-card h1 {
            text-align: center;
            color: #111827;
            margin-bottom: 8px;
        }

        .login-card .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            height: 48px;
            padding: 0 15px;

            border: 1px solid #d1d5db;
            border-radius: 10px;

            outline: none;
            font-size: 15px;

            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 25px;

            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .forgot-password {
            color: #2563eb;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            height: 50px;

            border: none;
            border-radius: 10px;

            background: #2563eb;
            color: white;

            font-size: 16px;
            font-weight: 600;

            cursor: pointer;
            transition: 0.3s;
        }

.login-btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .alert {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .register-text {
            text-align: center;
            margin-top: 25px;

            color: #6b7280;
            font-size: 14px;
        }

        .register-text a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {

            .login-card {
                padding: 30px 22px;
            }

            .login-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="login-card">

            <div class="logo">
                A
            </div>

            <h1>Admin Login</h1>

            <p class="subtitle">
                Login to access your admin panel
            </p>

<form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="login-options">

                    <label class="remember">
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>

                    <a href="#" class="forgot-password">
                        Forgot Password?
                    </a>

                </div>

                <button type="submit" class="login-btn">
                    Login
                </button>

            </form>

            <div class="register-text">
                Don't have an account?
                <a href="{{ route('admin.register') }}">
                    Create Account
                </a>
            </div>

        </div>

    </div>

</body>
</html>
