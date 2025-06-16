<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Beynak</title>
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
    <div class="brand">BeyNek Custom</div>

    <h1 class="welcome-title">Welcome back</h1>

    <!-- 🔥 Here is your fixed form -->
    <form id="loginForm" method="POST" action="javascript:void(0);">
        @csrf

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" placeholder="example@gmail.com" required>
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

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const api_url = 'http://127.0.0.1:8000/api/auth';

    document.getElementById('loginForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        axios.post(`${api_url}/login`, { email, password })
            .then(response => {
                const token = response.data.token;
                const user_id = response.data.user.id;

                // Save token into localStorage
                localStorage.setItem('token', token);

                // Set axios default Authorization header for next requests
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

                axios.post('/web-login-by-token', { user_id: user_id })
                    .then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Login successful!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Go to Dashboard'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/";
                            }
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Session creation failed', 'error');
                    });
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Login Failed', error.response?.data?.message || 'Unknown error', 'error');
            });
    });
</script>
</body>
</html>
