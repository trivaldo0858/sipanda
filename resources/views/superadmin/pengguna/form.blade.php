{{-- resources/views/superadmin/pengguna/form.blade.php --}}
@extends('layouts.superadmin')

@section('title', isset($pengguna) ? 'Edit Akun Staff' : 'Buat Akun Staff')

@section('content')
@php
    $isEdit = isset($pengguna);

    $selectedRole = old('role', $pengguna->role ?? '');
    $selectedPosyandu = old('id_posyandu', $pengguna->id_posyandu ?? '');

    $namaLengkap =
        old('nama_bidan', $pengguna->bidan->nama_bidan ?? null) ??
        old('nama_kader', $pengguna->kader->nama_kader ?? null) ??
        old('nama_ibu', $pengguna->orangTua->nama_ibu ?? null);

    $emailDummy = old('email', '');
@endphp

<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-4xl font-bold text-slate-900 tracking-tight">
            {{ $isEdit ? 'Edit Akun Staff' : 'Buat Akun Staff' }}
        </h1>

        <p class="mt-2 text-lg text-slate-600">
            {{ $isEdit
                ? 'Perbarui data akun Bidan, Kader, atau Orang Tua.'
                : 'Daftarkan Bidan atau Kader baru untuk memperluas jangkauan layanan Posyandu.' }}
        </p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            <ul class="space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- Form Card --}}
        <div class="xl:col-span-2">
            <div class="rounded-[28px] bg-white shadow-sm border border-slate-100 p-8 lg:p-10">

                <form
                    method="POST"
                    action="{{ $isEdit ? route('superadmin.pengguna.update', $pengguna->id_user) : route('superadmin.pengguna.store') }}"
                    class="space-y-8"
                >
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="{{ $selectedRole === 'Bidan' ? 'nama_bidan' : ($selectedRole === 'OrangTua' ? 'nama_ibu' : 'nama_kader') }}"
                            value="{{ $namaLengkap }}"
                            placeholder="Masukkan nama lengkap staff"
                            class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-xl text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600"
                            required
                        >
                    </div>

                    {{-- Email + Username --}}
                    <div class="grid md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">
                                Alamat Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ $emailDummy }}"
                                placeholder="contoh@posyandu.id"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                value="{{ old('username', $pengguna->username ?? '') }}"
                                placeholder="admin_kader123"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600"
                                required
                            >
                        </div>

                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">
                            Kata Sandi
                        </label>

                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Masukkan password' }}"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 pr-10 text-lg text-slate-800 placeholder:text-slate-300 focus:outline-none focus:border-blue-600"
                                {{ $isEdit ? '' : 'required' }}
                            >

                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute right-0 top-1 text-slate-400 hover:text-blue-600"
                            >
                                👁
                            </button>
                        </div>

                        <p class="mt-2 text-sm text-slate-400 italic">
                            Minimal 6 karakter.
                        </p>
                    </div>

                    {{-- Role + Posyandu --}}
                    <div class="grid md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">
                                Peran Profesional
                            </label>

                            <select
                                name="role"
                                id="roleSelect"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 focus:outline-none focus:border-blue-600"
                                {{ $isEdit ? 'disabled' : '' }}
                                required
                            >
                                <option value="">Pilih Peran</option>
                                <option value="Bidan" {{ $selectedRole == 'Bidan' ? 'selected' : '' }}>Bidan</option>
                                <option value="Kader" {{ $selectedRole == 'Kader' ? 'selected' : '' }}>Kader</option>
                                <option value="OrangTua" {{ $selectedRole == 'OrangTua' ? 'selected' : '' }}>Orang Tua</option>
                            </select>

                            @if($isEdit)
                                <input type="hidden" name="role" value="{{ $selectedRole }}">
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">
                                Unit Penugasan
                            </label>

                            <select
                                name="id_posyandu"
                                class="w-full border-0 border-b border-slate-200 bg-transparent pb-4 text-lg text-slate-800 focus:outline-none focus:border-blue-600"
                            >
                                <option value="">Pilih Unit/Wilayah</option>

                                @foreach($posyanduList as $item)
                                    <option
                                        value="{{ $item->id_posyandu }}"
                                        {{ $selectedPosyandu == $item->id_posyandu ? 'selected' : '' }}
                                    >
                                        {{ $item->nama_posyandu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Dynamic Field --}}
                    <div id="dynamicFields" class="grid md:grid-cols-2 gap-6"></div>

                    {{-- Submit --}}
                    <div class="pt-4">
                        <button
                            type="submit"
                            class="w-full h-16 rounded-full bg-blue-600 text-white text-xl font-semibold tracking-[2px] shadow-lg hover:bg-blue-700 transition"
                        >
                            {{ $isEdit ? 'SIMPAN PERUBAHAN' : 'INISIALISASI AKUN' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Side Info --}}
        <div>
            <div class="rounded-[28px] bg-[#EEF4FF] p-8 space-y-8 border border-blue-100">

                <div>
                    <h3 class="text-3xl font-bold text-blue-700 mb-6">
                        Panduan Registrasi
                    </h3>

                    <div class="space-y-7 text-slate-700">

                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold">1</div>
                            <div>
                                <p class="font-semibold">Validasi Identitas</p>
                                <p class="text-sm leading-7">Pastikan nama lengkap sesuai data resmi.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold">2</div>
                            <div>
                                <p class="font-semibold">Penentuan Peran</p>
                                <p class="text-sm leading-7">Bidan untuk klinis, Kader untuk administrasi lapangan.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold">3</div>
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
const roleSelect = document.getElementById('roleSelect');
const dynamicFields = document.getElementById('dynamicFields');
const password = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');

function renderFields(role){
    let html = '';

    if(role === 'Bidan'){
        html = `
            <div>
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">NIP</label>
                <input type="text" name="nip" class="w-full border-0 border-b border-slate-200 pb-4 focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">No Telepon</label>
                <input type="text" name="no_telp" class="w-full border-0 border-b border-slate-200 pb-4 focus:outline-none focus:border-blue-600">
            </div>
        `;
    }

    if(role === 'Kader'){
        html = `
            <div>
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">Wilayah</label>
                <input type="text" name="wilayah" class="w-full border-0 border-b border-slate-200 pb-4 focus:outline-none focus:border-blue-600">
            </div>
        `;
    }

    if(role === 'OrangTua'){
        html = `
            <div>
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">NIK</label>
                <input type="text" name="nik_orang_tua" class="w-full border-0 border-b border-slate-200 pb-4 focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-4">Alamat</label>
                <input type="text" name="alamat" class="w-full border-0 border-b border-slate-200 pb-4 focus:outline-none focus:border-blue-600">
            </div>
        `;
    }

    dynamicFields.innerHTML = html;
}

if(roleSelect){
    renderFields(roleSelect.value);

    roleSelect.addEventListener('change', function(){
        renderFields(this.value);
    });
}

togglePassword.addEventListener('click', function(){
    password.type = password.type === 'password' ? 'text' : 'password';
});
</script>
@endsection