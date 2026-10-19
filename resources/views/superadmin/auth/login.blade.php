@extends('layouts.auth')

@section('title', 'Posyandu-In')

@section('content')
    <div class="min-h-screen bg-slate-50 flex flex-col justify-center items-center p-6 font-sans">

        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-5xl font-black text-blue-600 tracking-tighter">
                Posyandu-In
            </h1>
            <p class="text-slate-500 font-medium text-sm mt-2">
                Sistem Posyandu Kabupaten Indramayu
            </p>
        </div>

        {{-- Login Card --}}
        <div
            class="w-full max-w-[440px] bg-white p-12 rounded-[3rem] shadow-2xl shadow-slate-200/60 border border-slate-50">

            <form action="{{ route('superadmin.login.post') }}" method="POST" class="space-y-8">
                @csrf

                {{-- Username --}}
                <div class="space-y-3">
                    <label for="username" class="block text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">
                        Nama Pengguna
                    </label>

                    <div
                        class="relative flex items-center border-b-2 border-slate-100 focus-within:border-blue-600 transition-all duration-300 pb-2">
                        <input id="username" type="text" name="username" value="{{ old('username') }}"
                            placeholder="Masukkan nama pengguna" autocomplete="username"
                            aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                            class="w-full bg-transparent border-none outline-none text-slate-700 placeholder:text-slate-300 py-1 px-1 font-medium">

                        {{-- Ikon User --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-slate-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>

                    @error('username')
                        <p class="text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-3">
                    <label for="password" class="block text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">
                        Kata Sandi
                    </label>

                    <div
                        class="relative flex items-center border-b-2 border-slate-100 focus-within:border-blue-600 transition-all duration-300 pb-2">
                        <input id="password" type="password" name="password" required placeholder="Masukkan kata sandi"
                            autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            class="w-full bg-transparent border-none outline-none text-slate-700 placeholder:text-slate-300 py-1 px-1 font-medium">

                        {{-- Ikon Lock --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-slate-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    @error('password')
                        <p class="text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Tombol Masuk --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-5 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 text-base">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection