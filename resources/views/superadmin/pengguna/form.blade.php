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
    </div>

    {{-- TAMPILAN ERROR (PENTING: Untuk tahu alasan gagal) --}}
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm mb-6">
            <p class="font-bold">Gagal menyimpan:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('superadmin.pengguna.update', $userEdit->id_user) : route('superadmin.pengguna.store') }}" class="space-y-10">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Role dikunci ke Bidan --}}
        <input type="hidden" name="role" value="Bidan">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 text-left">
            <div class="xl:col-span-2 space-y-8">
                
                {{-- SEKSI 1: IDENTITAS --}}
                <div class="rounded-[28px] bg-white shadow-sm border border-slate-100 p-8 lg:p-10 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-[2px] bg-blue-600"></div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Identitas Pengguna</h2>
                    </div>

                    <div class="space-y-6 text-left">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $namaLengkap }}" placeholder="Masukkan nama lengkap staff"
                                class="w-full rounded-2xl bg-slate-50 border-0 p-4 text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 text-left">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', $userEdit->email ?? '') }}" placeholder="contoh@posyandu.id"
                                    class="w-full rounded-2xl bg-slate-50 border-0 p-4 text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Username</label>
                                <input type="text" name="username" value="{{ old('username', $userEdit->username ?? '') }}" placeholder="Masukkan username"
                                    class="w-full rounded-2xl bg-slate-50 border-0 p-4 text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEKSI 2: KEAMANAN & UNIT --}}
                <div class="rounded-[28px] bg-white shadow-sm border border-slate-100 p-8 lg:p-10 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-[2px] bg-blue-600"></div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Keamanan & Penugasan</h2>
                    </div>

                    <div class="space-y-6 text-left">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Kata Sandi</label>
                            <input type="password" name="password" id="password" placeholder="Minimal 6 karakter"
                                class="w-full rounded-2xl bg-slate-50 border-0 p-4 text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all" {{ $isEdit ? '' : 'required' }}>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 text-left">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Peran Profesional</label>
                                <div class="w-full rounded-2xl bg-[#f0f7ff] border border-blue-100 p-4 font-medium text-slate-900">
                                    Bidan
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Unit Penugasan</label>
                                <select name="id_posyandu[]" class="w-full rounded-2xl bg-slate-50 border-0 p-4 text-slate-700 cursor-pointer focus:ring-2 focus:ring-blue-500" required>
                                    <option value="" disabled selected>Pilih Unit</option>
                                    @foreach($posyanduList as $item)
                                        <option value="{{ $item->id_posyandu }}" {{ ($isEdit && $userEdit->posyanduList->contains($item->id_posyandu)) ? 'selected' : '' }}>
                                            {{ $item->nama_posyandu }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT (HARUS DI DALAM FORM) --}}
                <button type="submit" class="w-full h-16 rounded-full bg-blue-600 text-white text-xl font-bold tracking-widest shadow-lg hover:bg-blue-700 transition-all active:scale-[0.98]">
                    {{ $isEdit ? 'SIMPAN PERUBAHAN' : 'VERIFIKASI & BUAT AKUN' }}
                </button>
            </div>

            {{-- SIDEBAR PANDUAN --}}
            <div class="xl:col-span-1">
                <div class="rounded-[28px] bg-[#EEF4FF] p-8 space-y-8 border border-blue-100 sticky top-10 text-left">
                    <h3 class="text-4xl font-bold text-blue-700 leading-tight">Panduan<br>Registrasi</h3>
                    
                    <div class="space-y-8">
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">1</div>
                            <div>
                                <p class="font-bold text-slate-800">Validasi Identitas</p>
                                <p class="text-sm text-slate-500">Pastikan nama lengkap sesuai data resmi.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">2</div>
                            <div>
                                <p class="font-bold text-slate-800">Penentuan Peran</p>
                                <p class="text-sm text-slate-500">Bidan bertugas untuk layanan klinis.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">3</div>
                            <div>
                                <p class="font-bold text-slate-800">Aktivasi Akun</p>
                                <p class="text-sm text-slate-500">Akun bisa langsung digunakan login.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-blue-50">
                        <p class="font-bold text-slate-800 mb-1">Butuh Bantuan Cepat?</p>
                        <p class="text-sm text-slate-500">Hubungi IT Support Posyandu.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection