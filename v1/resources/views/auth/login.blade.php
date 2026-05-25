<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | EasyTax</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --brand-primary: #18b36b;
            --brand-primary-hover: #159c5d;
            --brand-dark: #0b1f3a;
            --brand-secondary: #122b49;
            --brand-light: #f6f8fb;
            --text-main: #334155;
            --text-muted: #94a3b8;
            --white: #ffffff;
            --radius: 8px;
            --transition: 0.2s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--brand-dark), var(--brand-secondary));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: var(--radius);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 140px;
        }

        .login-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--brand-dark);
            text-align: center;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 14px;
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            font-size: 14px;
            font-weight: 500;
            color: var(--brand-dark);
            display: block;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: var(--radius);
            border: 1px solid #dbe3ec;
            font-size: 14px;
            transition: border var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--brand-primary);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 13px;
            color: var(--text-muted);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            margin-top: 5px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border-radius: var(--radius);
            background: var(--brand-primary);
            border: none;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background var(--transition);
            margin-top: 15px;
        }

        .btn:hover {
            background: var(--brand-primary-hover);
        }

        .error {
            font-size: 13px;
            color: red;
            margin-top: 5px;
        }

        .alert {
            background: #ffe6e6;
            color: #c00000;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .footer-text {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: var(--text-muted);
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="logo">
                <img src="{{ asset('/assets/images/logo.png') }}" alt="EasyTax Logo">
            </div>

            <div class="login-title">Agent Portal</div>
            <div class="login-subtitle">Secure Access to Dashboard</div>

            @if (session('error'))
                <div class="alert">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" required>
                        <span class="toggle-password" onclick="togglePassword()">Show</span>
                    </div>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember Me</label>
                </div>

                <button type="submit" class="btn">Login</button>
            </form>

            <div class="footer-text">
                © {{ date('Y') }} EasyTax. All rights reserved.
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const toggle = document.querySelector('.toggle-password');

            if (input.type === "password") {
                input.type = "text";
                toggle.innerText = "Hide";
            } else {
                input.type = "password";
                toggle.innerText = "Show";
            }
        }
    </script>

</body>

</html>
