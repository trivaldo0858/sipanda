@extends('layouts.auth')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-pink-50 px-4">
        <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 border border-pink-100">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-pink-600 mb-2">SIPANDA</h2>
                <p class="text-gray-500 font-medium">Login Super Admin</p>
                <div class="h-1 w-20 bg-pink-400 mx-auto mt-2 rounded-full"></div>
            </div>

            <form action="{{ route('superadmin.login.post') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pengguna</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <input type="text" name="username" id="username" required
                            class="block w-full pl-10 pr-3 py-3 border border-pink-200 rounded-xl focus:ring-pink-500 focus:border-pink-500 bg-pink-50/30 text-gray-900 placeholder-gray-400 sm:text-sm"
                            placeholder="Masukkan nama pengguna">
                    </div>
                </div>

                <div class="mb-8">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="block w-full pl-10 pr-3 py-3 border border-pink-200 rounded-xl focus:ring-pink-500 focus:border-pink-500 bg-pink-50/30 text-gray-900 placeholder-gray-400 sm:text-sm"
                            placeholder="Masukkan kata sandi">
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all duration-200 uppercase tracking-wider">
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-gray-400">
                &copy; 2026 SIPANDA Team - Posyandu Digital
            </div>
        </div>
    </div>
@endsection