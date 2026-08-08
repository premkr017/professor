<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Registration</title>

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

        .register-container {
            width: 100%;
            max-width: 520px;
            padding: 20px;
        }

        .register-card {
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

        .register-card h1 {
            text-align: center;
            color: #111827;
            margin-bottom: 8px;
        }

        .register-card .subtitle {
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

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .register-btn {
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

        .register-btn:hover {
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

        .login-text {
            text-align: center;
            margin-top: 25px;

            color: #6b7280;
            font-size: 14px;
        }

        .login-text a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {

            .register-card {
                padding: 30px 22px;
            }
        }
    </style>
</head>

<body>

    <div class="register-container">

        <div class="register-card">

            <div class="logo">
                A
            </div>

            <h1>Create Admin Account</h1>

            <p class="subtitle">
                Register to set up your admin access
            </p>

            <form name="register" action="{{ route('admin.register.submit') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="name"
                        placeholder="Enter your full name"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email address"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="mobile">
                        Mobile Number
                    </label>

                    <input
                        type="tel"
                        id="mobile"
                        name="mobile"
                        placeholder="Enter your mobile number"
                        value="{{ old('mobile') }}"
                        pattern="[0-9]{10,15}"
                        title="Please enter a valid mobile number (10 to 15 digits)"
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
                        minlength="6"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Re-enter your password"
                        minlength="6"
                        required
                    >
                </div>

                <button type="submit" class="register-btn">
                    Create Account
                </button>

            </form>

            <div class="login-text">
                Already have an account?
                <a href="{{ route('admin.login') }}">
                    Login
                </a>
            </div>

        </div>

    </div>

</body>
</html>

