<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Cal.com</title>
    <style>
        /* (All your existing CSS, no changes) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.5;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            margin: 20px;
        }

        .brand {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 32px;
        }

        .welcome-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.2s ease;
            background-color: #fff;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .password-container {
            position: relative;
        }

        .forgot-link {
            position: absolute;
            right: 0;
            top: -24px;
            font-size: 14px;
            color: #6b7280;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: #3b82f6;
        }

        .sign-in-btn {
            width: 100%;
            background-color: #1e40af;
            color: white;
            border: none;
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 24px;
        }

        .sign-in-btn:hover {
            background-color: #1d4ed8;
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
            color: #6b7280;
            font-size: 14px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e5e7eb;
            z-index: 1;
        }

        .divider span {
            background-color: white;
            padding: 0 16px;
            position: relative;
            z-index: 2;
        }

        .secondary-btn {
            width: 100%;
            background-color: #fff;
            color: #374151;
            border: 2px solid #e5e7eb;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .secondary-btn:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        .google-icon {
            width: 18px;
            height: 18px;
        }

        .lock-icon {
            width: 16px;
            height: 16px;
        }

        .signup-link {
            text-align: center;
            margin-top: 32px;
            font-size: 14px;
            color: #6b7280;
        }

        .signup-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
            }
        }

    </style>
</head>
<body>
<div class="login-container">
    <div class="brand">Cal.com</div>

    <h1 class="welcome-title">Welcome back</h1>

    <!-- 🔥 Here is your fixed form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" placeholder="example@gmail.com" required value="{{ old('email') }}">
            @error('email')
                <div style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            @error('password')
                <div style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="sign-in-btn">Sign in</button>
    </form>


    <div class="divider">
        <span>or</span>
    </div>

    <button class="secondary-btn">
        <svg class="google-icon" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Sign in with Google
    </button>

    <button class="secondary-btn">
        <svg class="lock-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
        </svg>
        Sign in with SAML
    </button>

    <div class="signup-link">
        <a href="{{ route('register') }}">Don't have an account?</a>
    </div>
</div>
</body>
</html>
