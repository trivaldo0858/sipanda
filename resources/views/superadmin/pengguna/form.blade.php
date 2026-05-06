{{-- resources/views/superadmin/pengguna/form.blade.php --}}
@extends('layouts.superadmin')

@section('title', isset($pengguna_single) ? 'Edit Akun Staff' : 'Buat Akun Staff')

@section('content')
@php
    $userEdit = $pengguna_single ?? null; 
    $isEdit = isset($userEdit);
    
    $namaLengkap = '';
    if($isEdit){
        $namaLengkap = $userEdit->bidan->nama_bidan ?? ($userEdit->kader->nama_kader ?? ($userEdit->orangTua->nama_ibu ?? ''));
    }
    $namaLengkap = old('nama', $namaLengkap);
@endphp

<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-4xl font-bold text-slate-900 tracking-tight">
            {{ $isEdit ? 'Edit Akun Staff' : 'Buat Akun Staff' }}
        </h1>

        <p class="mt-2 text-lg text-slate-600">
            Daftarkan Bidan baru untuk memperluas jangkauan layanan Posyandu.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700 shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 shadow-sm">
            <p class="font-bold mb-2 text-sm uppercase tracking-wider">Terjadi Kesalahan:</p>
            <ul class="space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- Form Card --}}
        <div class="xl:col-span-2">
            <div class="rounded-[28px] bg-white shadow-sm border border-slate-100 p-8 lg:p-10">

                <form
                    method="POST"
                    action="{{ $isEdit ? route('superadmin.pengguna.update', $userEdit->id_user) : route('superadmin.pengguna.store') }}"
                    class="space-y-10"
                >
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-bold tracking-[2px] text-slate-400 uppercase mb-4">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="nama"
                            value="{{ $namaLengkap }}"
                            placeholder="Masukkan nama lengkap staff"
                            class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-xl text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600 transition-all"
                            required
                        >
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-400 uppercase mb-4">
                                Alamat Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $userEdit->email ?? '') }}"
                                placeholder="contoh@posyandu.id"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600 transition-all"
                            >
                        </div>

                        {{-- Username --}}
                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-400 uppercase mb-4">
                                Username
                            </label>
                            <input
                                type="text"
                                name="username"
                                value="{{ old('username', $userEdit->username ?? '') }}"
                                placeholder="Masukkan username"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600 transition-all"
                                required
                            >
                        </div>
                    </div>

                    {{-- Kata Sandi --}}
                    <div>
                        <label class="block text-xs font-bold tracking-[2px] text-slate-400 uppercase mb-4">
                            Kata Sandi
                        </label>

                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="••••••••"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 pr-10 text-lg text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600 transition-all"
                                {{ $isEdit ? '' : 'required' }}
                            >

                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute right-0 top-1 text-slate-300 hover:text-blue-600 focus:outline-none"
                            >
                                <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        {{-- Peran --}}
                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-400 uppercase mb-4">
                                Peran Profesional
                            </label>
                            <select name="role" class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 focus:outline-none focus:border-blue-600 appearance-none cursor-default" required>
                                <option value="Bidan">Bidan</option>
                            </select>
                        </div>

                        {{-- Unit --}}
                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-400 uppercase mb-4">
                                Unit Penugasan
                            </label>
                            <select name="id_posyandu[]" class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 focus:outline-none focus:border-blue-600 transition-all cursor-pointer" required>
                                <option value="" disabled selected>Pilih Unit/Wilayah</option>
                                @foreach($posyanduList as $item)
                                    <option value="{{ $item->id_posyandu }}" {{ ($isEdit && $userEdit->posyanduList->contains($item->id_posyandu)) ? 'selected' : '' }}>
                                        {{ $item->nama_posyandu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="pt-4">
                        <button
                            type="submit"
                            class="w-full h-16 rounded-full bg-blue-600 text-white text-xl font-bold tracking-[1px] shadow-lg hover:bg-blue-700 transition-all"
                        >
                            {{ $isEdit ? 'SIMPAN PERUBAHAN' : 'VERIFIKASI & BUAT AKUN' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Side Info: PANDUAN REGISTRASI (VERSI AWAL BANGET) --}}
        <div>
            <div class="rounded-[28px] bg-[#EEF4FF] p-8 space-y-8 border border-blue-100 sticky top-10">

                <div>
                    <h3 class="text-3xl font-bold text-blue-700 mb-6">
                        Panduan Registrasi
                    </h3>

                    <div class="space-y-7 text-slate-700">

                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">1</div>
                            <div>
                                <p class="font-semibold">Validasi Identitas</p>
                                <p class="text-sm leading-7">Pastikan nama lengkap sesuai data resmi.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">2</div>
                            <div>
                                <p class="font-semibold">Penentuan Peran</p>
                                <p class="text-sm leading-7">Bidan untuk klinis, Kader untuk administrasi lapangan.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">3</div>
                            <div>
                                <p class="font-semibold">Aktivasi Akun</p>
                                <p class="text-sm leading-7">Setelah dibuat, akun siap digunakan login.</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="rounded-2xl bg-white/60 border border-white p-5">
                    <p class="font-semibold text-slate-800 mb-1">Butuh Bantuan Cepat?</p>
                    <p class="text-sm text-slate-600 leading-6">
                        Hubungi IT Support Posyandu untuk pengaturan akses lanjutan.
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');

    toggleBtn.addEventListener('click', function() {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeOpen.classList.toggle('hidden');
        eyeClosed.classList.toggle('hidden');
    });
</script>
@endsection