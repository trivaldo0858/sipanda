{{-- resources/views/superadmin/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Super Admin - SIPANDA</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0A63D8',
                        softbg: '#F3F4F8',
                        line: '#D7DCE5',
                        muted: '#9CA3AF'
                    },
                    boxShadow: {
                        card: '0 8px 25px rgba(0,0,0,0.05)',
                        button: '0 10px 20px rgba(10,99,216,0.18)'
                    },
                    borderRadius: {
                        xl2: '20px'
                    }
                }
            }
        }
    </script>

    <style>
        body{
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus{
            -webkit-box-shadow: 0 0 0 1000px white inset;
        }
    </style>
</head>

<body class="bg-softbg min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-sm">

    {{-- Brand --}}
    <div class="text-center mb-7">
        <h1 class="text-4xl font-extrabold text-primary tracking-tight">
            SIPANDA
        </h1>

        <p class="mt-2 text-gray-600 text-base">
            Sistem Posyandu Anak Digital
        </p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-xl2 shadow-card px-8 py-8">

        {{-- Error Session --}}
        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-600 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validation --}}
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-600 px-4 py-3 text-sm">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST"
              action="{{ route('superadmin.login.post') }}"
              class="space-y-5">
            @csrf

            {{-- Username --}}
            <div>
                <label class="block text-[11px] font-bold tracking-[2px] text-gray-600 uppercase mb-2">
                    Nama Pengguna
                </label>

                <div class="relative">
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan nama pengguna"
                        class="w-full border-0 border-b border-line bg-transparent pb-3 pr-9 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:border-primary transition"
                        required
                        autofocus
                    >

                    <div class="absolute right-0 top-0.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M15 19a4 4 0 00-8 0m8 0H7m8 0h2m-2 0a4 4 0 00-8 0m8-10a4 4 0 11-8 0 4 4 0 018 0"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-[11px] font-bold tracking-[2px] text-gray-600 uppercase mb-2">
                    Kata Sandi
                </label>

                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan kata sandi"
                        class="w-full border-0 border-b border-line bg-transparent pb-3 pr-9 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:border-primary transition"
                        required
                    >

                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute right-0 top-0.5 text-gray-400 hover:text-primary transition">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M15 7a3 3 0 11-6 0 3 3 0 016 0zm-9 13h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-3">
                <button
                    type="submit"
                    id="loginBtn"
                    class="w-full h-14 rounded-full bg-primary text-white text-xl font-semibold shadow-button hover:scale-[1.02] active:scale-[0.98] transition">
                    Masuk
                </button>
            </div>

        </form>
    </div>
</div>

<script>
const togglePassword = document.getElementById('togglePassword');
const passwordInput  = document.getElementById('password');
const loginBtn       = document.getElementById('loginBtn');

togglePassword.addEventListener('click', function () {
    passwordInput.type =
        passwordInput.type === 'password'
        ? 'text'
        : 'password';
});

document.querySelector('form').addEventListener('submit', function () {
    loginBtn.innerHTML = 'Memproses...';
    loginBtn.disabled = true;
    loginBtn.classList.add('opacity-70');
});
</script>

</body>
</html>