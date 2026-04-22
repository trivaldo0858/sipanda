<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPANDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a2a4a 0%, #2E86AB 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-logo .icon-wrap {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #2E86AB, #1a6a8a);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 12px;
        }
        .login-logo h4 { font-weight: 700; color: #1a2a4a; margin: 0; }
        .login-logo p  { color: #888; font-size: 13px; margin: 4px 0 0; }
        .form-label { font-weight: 600; font-size: 13px; color: #555; }
        .form-control {
            border-radius: 8px;
            border: 1.5px solid #e0e0e0;
            padding: 10px 14px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #2E86AB;
            box-shadow: 0 0 0 3px rgba(46,134,171,.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #2E86AB, #1a6a8a);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            font-size: 15px;
        }
        .btn-login:hover { opacity: .9; color: white; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="icon-wrap">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <h4>SIPANDA</h4>
            <p>Sistem Informasi Posyandu Anak Digital</p>
        </div>

        <p class="text-center text-muted small mb-4">
            <i class="bi bi-shield-lock me-1"></i> Login Super Admin
        </p>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('superadmin.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-person text-muted"></i>
                    </span>
                    <input type="text" name="username"
                           class="form-control border-start-0"
                           value="{{ old('username') }}"
                           placeholder="Masukkan username"
                           required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-lock text-muted"></i>
                    </span>
                    <input type="password" name="password"
                           class="form-control border-start-0"
                           placeholder="Masukkan password"
                           required>
                </div>
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>