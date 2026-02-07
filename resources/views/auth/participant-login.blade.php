<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Peserta - Sistem Arisan Primkopkar "PRIMA"</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a7f5c;
            --primary-light: #e9f7f2;
            --primary-dark: #146c4a;
            --secondary: #2d3748;
            --light-gray: #f8f9fa;
            --border-color: #e2e8f0;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: var(--secondary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow-x: hidden;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("{{ asset('storage/img/background.webp') }}") no-repeat center center / cover fixed;
        }
        
        .background-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(26, 127, 92, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(26, 127, 92, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 50% 50%, rgba(26, 127, 92, 0.03) 0%, transparent 30%);
            z-index: -1;
        }
        
        .container-fluid {
            max-width: 1200px;
            padding: 2rem;
        }
        
        .login-wrapper {
            display: flex;
            min-height: auto;
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            background-color: white;
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
        }
        
        .login-left::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.3;
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .brand-logo i, .brand-logo img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-right: 1rem;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: 12px;
        }
        
        .brand-name {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 400;
            margin-left: 0;
        }
        
        .login-left h1 {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        
        .login-left p {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }
        
        .features-list {
            list-style-type: none;
            margin-top: 2rem;
        }
        
        .features-list li {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        
        .features-list i {
            margin-right: 0.8rem;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: 50%;
            font-size: 0.8rem;
        }
        
        .login-right {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }
        
        .login-header {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .login-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        
        .login-header p {
            color: #718096;
            font-size: 1rem;
            margin: 0;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary);
            font-size: 0.95rem;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }
        
        .input-group .form-control {
            padding: 0.9rem 1rem 0.9rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .input-group .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 127, 92, 0.1);
        }
        
        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 10 !important;
            pointer-events: none;
            transition: color 0.3s;
        }
        
        .input-group:focus-within i:not(.fa-eye):not(.fa-eye-slash) {
            color: var(--primary) !important;
        }
        
        .btn-login {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            margin-top: 0.5rem;
            width: 100%;
        }
        
        .btn-login:hover {
            background-color: #146c4a !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 127, 92, 0.3);
        }
        
        .admin-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .admin-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            transition: color 0.2s;
        }
        
        .admin-link a:hover {
            color: var(--primary-dark);
        }
        
        .admin-link i {
            margin-right: 0.5rem;
        }
        
        .footer {
            text-align: center;
            margin-top: 2rem;
            color: #a0aec0;
            font-size: 0.85rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            background: #f8f9fa;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            z-index: 11;
            font-size: 1rem;
            height: 35px;
            width: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .password-toggle:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }

        #password {
            padding-right: 3.5rem !important;
        }

        /* Hide default browser password reveal icon */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
        
        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
            }
            
            .login-left {
                padding: 2.5rem;
            }
            
            .login-right {
                padding: 3rem 2.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding: 1rem;
            }
            
            .login-left, .login-right {
                padding: 2rem 1.5rem;
            }
            
            .login-left h1 {
                font-size: 2rem;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-left, .login-right {
            animation: fadeIn 0.8s ease-out;
        }
        
        .login-right {
            animation-delay: 0.2s;
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>
    
    <div class="container-fluid">
        <div class="login-wrapper">
            <!-- Left side - Brand & Welcome -->
            <div class="login-left">
                <div class="brand-logo">
                    <img src="{{ asset('storage/img/logo.webp') }}" alt="PRIMA Logo" style="width: 80px; height: 80px; object-fit: contain; margin-right: 1rem; background: white; padding: 5px; border-radius: 10px;">
                    <div style="text-align: left;">
                            <div class="brand-name" style="text-transform: uppercase; letter-spacing: 1px;">primkopkar prima</div>
                            <div class="brand-subtitle">PT. PRIMATEXCO INDONESIA</div>
                    </div>
                </div>
                
                <h1>Selamat Datang di Sistem Arisan Sepeda Motor</h1>
                <p>Sistem arisan digital Primkopkar "PRIMA" PT. PRIMATEXCO INDONESIA. Masuk untuk mengakses undian dan informasi arisan Anda.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Akses informasi undian secara real-time</li>
                    <li><i class="fas fa-check"></i> Pantau perkembangan arisan dengan mudah</li>
                    <li><i class="fas fa-check"></i> Sistem yang aman dan terpercaya</li>
                </ul>
            </div>
            
            <!-- Right side - Login Form -->
            <div class="login-right">
                <div class="login-header">
                    <h2>Login Peserta</h2>
                    <p>Masukkan kredensial Anda untuk mengakses sistem</p>
                </div>
                
                <form method="POST" action="{{ route('participant.login') }}" id="loginForm">
                    @csrf
                    
                    @if ($errors->any())
                        @php
                            $isRateLimited = str_contains($errors->first(), 'Terlalu banyak');
                        @endphp
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" @if($isRateLimited) id="rate-limit-error" @endif>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div><strong>Login Gagal!</strong> {{ $errors->first() }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- No. Undian Field -->
                    <div class="mb-3">
                        <label for="lottery_number" class="form-label">Username</label>
                        <div class="input-group">
                            <i class="fas fa-ticket-alt"></i>
                            <input type="text" class="form-control" id="lottery_number" name="lottery_number" 
                                   value="{{ old('lottery_number') }}" placeholder="Masukkan Username" required autofocus>
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password Anda" required>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Default password adalah NIK Anda</small>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        MASUK SEBAGAI PESERTA
                    </button>
                </form>
                
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form submission feedback
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
            submitBtn.disabled = true;
        });
        
        // Disable form when rate limited
        const rateLimitError = document.querySelector('#rate-limit-error');
        if (rateLimitError) {
            const usernameInput = document.getElementById('lottery_number');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            usernameInput.disabled = true;
            passwordInput.disabled = true;
            submitBtn.disabled = true;
            
            usernameInput.style.backgroundColor = '#f1f5f9';
            passwordInput.style.backgroundColor = '#f1f5f9';
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            // Auto-focus on lottery number field only if not rate limited
            document.getElementById('lottery_number').focus();
        }
    </script>
</body>
</html>
