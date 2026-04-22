<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Super Admin - SIPANDA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .bg-gradient-sipanda {
            background: linear-gradient(135deg, #1a2a4a 0%, #2E86AB 100%);
        }
        .login-input {
            border: 1.5px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .login-input:focus {
            border-color: #2E86AB;
            box-shadow: 0 0 0 4px rgba(46, 134, 171, 0.1);
            outline: none;
        }
    </style>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center p-0 md:p-6">

    <div class="bg-white w-full max-w-5xl h-full md:h-[600px] rounded-none md:rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <div class="md:w-1/2 bg-gradient-sipanda p-12 flex flex-col justify-center items-center text-white relative">
            <div class="absolute top-0 left-0 w-32 h-32 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            
            <div class="text-center z-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl mb-6 shadow-inner">
                    <i class="bi bi-heart-pulse-fill text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold tracking-tight mb-2">SIPANDA</h1>
                <p class="text-blue-100 font-light opacity-80">Sistem Informasi Posyandu Anak Digital</p>
            </div>

            <div class="mt-12 hidden md:block opacity-20">
                <i class="bi bi-shield-check text-[150px]"></i>
            </div>
        </div>

        <div class="md:w-1/2 p-8 md:p-16 flex flex-col justify-center bg-white">
            <div class="mb-10">
                <h2 class="text-3xl font-extrabold text-slate-800 mb-2">Login Super Admin</h2>
                <p class="text-slate-500">Gunakan akun admin untuk mengelola sistem</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg flex items-center">
                    <i class="bi bi-exclamation-circle-fill text-red-500 mr-3"></i>
                    <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('superadmin.login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="username" value="{{ old('username') }}" 
                            class="login-input w-full pl-11 pr-4 py-3 rounded-xl text-slate-600 bg-slate-50 focus:bg-white" 
                            placeholder="Masukkan username admin" required autofocus>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" 
                            class="login-input w-full pl-11 pr-4 py-3 rounded-xl text-slate-600 bg-slate-50 focus:bg-white" 
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-500">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#2E86AB] hover:bg-[#1a6a8a] text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center justify-center">
                    <span>Masuk ke Sistem</span>
                    <i class="bi bi-arrow-right-short text-2xl ml-1"></i>
                </button>
            </form>

            <p class="mt-10 text-center text-xs text-slate-400">
                &copy; 2026 SIPANDA Digital - Dashboard Keamanan Tinggi
            </p>
        </div>
    </div>

</body>
</html>