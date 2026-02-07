<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Primkopkar "PRIMA"</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary: #64748b;
            --bg-color: #f8fafc;
            --text-main: #1e293b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), url("{{ asset('storage/img/background.webp') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            color: var(--text-main);
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: white;
            padding: 10px;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
        }

        .brand-logo img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .login-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }

        .login-header p {
            color: var(--secondary);
            font-size: 0.925rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #475569;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            z-index: 5;
            transition: color 0.2s ease;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: #fff;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.2s ease;
            height: 52px;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .form-control-custom:focus + i.input-icon {
            color: var(--primary);
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            padding: 0.5rem;
            cursor: pointer;
            z-index: 10;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: var(--text-main);
        }

        .btn-submit {
            width: 100%;
            height: 52px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert-custom {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
        }

        .footer-links {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--secondary);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .link-item {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .link-item:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.75rem;
                border-radius: 16px;
            }
        }

        /* Loading Spinner */
        .spinner {
            display: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading .spinner { display: block; }
        .loading .btn-text { display: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                <img src="{{ asset('storage/img/logo.webp') }}" alt="Logo Primkopkar">
            </div>
            <h1>Admin Panel</h1>
            <p>Silakan masuk untuk mengelola sistem</p>
        </div>

        @if ($errors->any())
            @php
                $isRateLimited = str_contains($errors->first(), 'Terlalu banyak');
            @endphp
            <div class="alert-custom" @if($isRateLimited) id="rate-limit-error" @endif>
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" id="loginForm">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email Administrator</label>
                <div class="input-group-custom">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control-custom" 
                           placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-group-custom">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control-custom" 
                           placeholder="••••••••" required>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span class="btn-text">Masuk</span>
                <div class="spinner"></div>
            </button>
        </form>

        <div class="footer-links">
            <div>Bukan admin? <a href="{{ route('login') }}" class="link-item">Login sebagai Peserta</a></div>
            <div style="font-size: 0.75rem; opacity: 0.6; margin-top: 0.5rem;">
                &copy; {{ date('Y') }} Primkopkar "PRIMA" PT. PRIMATEXCO INDONESIA
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
            
            this.setAttribute('aria-label', type === 'password' ? 'Tampilkan password' : 'Sembunyikan password');
        });

        // Form submission loading state
        const loginForm = document.querySelector('#loginForm');
        const submitBtn = document.querySelector('#submitBtn');

        loginForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Disable form when rate limited
        const rateLimitError = document.querySelector('#rate-limit-error');
        if (rateLimitError) {
            const emailInput = document.querySelector('#email');
            const passwordInput = document.querySelector('#password');
            const submitBtn = document.querySelector('#submitBtn');
            
            emailInput.disabled = true;
            passwordInput.disabled = true;
            submitBtn.disabled = true;
            
            emailInput.style.backgroundColor = '#f1f5f9';
            passwordInput.style.backgroundColor = '#f1f5f9';
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
    </script>
</body>
</html>
