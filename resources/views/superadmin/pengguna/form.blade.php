
{{-- resources/views/superadmin/pengguna/form.blade.php --}}
@extends('layouts.superadmin')

@section('title', $pengguna_single ?? false ? 'Edit Akun Pengguna' : 'Registrasi Akun Pengguna')

@section('content')
    @php
        $userEdit = $pengguna_single ?? null;
        $isEdit = $userEdit !== null;

        $namaLengkap = old('nama', $userEdit?->bidan?->nama_bidan ?? '');
        $username = old('username', $userEdit?->username ?? '');
        $nip = old('nip', $userEdit?->bidan?->nip ?? '');
        $noTelp = old('no_telp', $userEdit?->bidan?->no_telp ?? '');
        $idPosyandu = old('id_posyandu', $userEdit?->id_posyandu ?? '');
    @endphp

    <div class="mx-auto max-w-6xl space-y-5 pb-10">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-8 text-slate-800">
                    {{ $isEdit ? 'Edit Akun Pengguna' : 'Registrasi Akun Pengguna' }}
                </h1>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Lengkapi informasi akun pengguna sesuai data yang tersedia.
                </p>
            </div>

            <a
                href="{{ route('superadmin.pengguna.index') }}"
                class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h12" />
                </svg>

                Kembali ke Daftar
            </a>
        </div>

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                <p class="mb-2 text-sm font-semibold">
                    Periksa kembali data yang dimasukkan.
                </p>

                <ul class="ml-5 list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ $isEdit
                ? route('superadmin.pengguna.update', $userEdit->id_user)
                : route('superadmin.pengguna.store') }}"
            class="space-y-5"
        >
            @csrf

            @if ($isEdit)
                @method('PUT')
            @endif

            <input type="hidden" name="role" value="Bidan">

            {{-- KARTU FORM --}}
            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">

                <div class="space-y-7 p-5 sm:p-7 lg:p-8">

                    {{-- INFORMASI DASAR --}}
                    <section class="space-y-5">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">
                                Informasi Pengguna
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Masukkan identitas dan informasi akun Bidan.
                            </p>
                        </div>

                        {{-- NAMA DAN USERNAME --}}
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div>
                                <label for="nama" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="nama"
                                    type="text"
                                    name="nama"
                                    value="{{ $namaLengkap }}"
                                    placeholder="Contoh: Siti Aminah, S.Tr.Keb"
                                    autocomplete="name"
                                    required
                                    class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] px-4 text-sm text-slate-800 outline-none transition placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Gunakan nama resmi beserta gelar profesi kebidanan.
                                </p>
                            </div>

                            <div>
                                <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Username <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    value="{{ $username }}"
                                    placeholder="Contoh: bidan.siti"
                                    autocomplete="username"
                                    required
                                    class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] px-4 text-sm text-slate-800 outline-none transition placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Gunakan huruf kecil tanpa spasi. Huruf, angka, titik, dan garis bawah.
                                </p>
                            </div>

                        </div>

                        {{-- NIP DAN NOMOR TELEPON --}}
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div>
                                <label for="nip" class="mb-2 block text-sm font-semibold text-slate-700">
                                    NIP Bidan
                                    <span class="font-normal text-slate-400">(Opsional)</span>
                                </label>

                                <input
                                    id="nip"
                                    type="text"
                                    name="nip"
                                    value="{{ $nip }}"
                                    placeholder="Masukkan NIP bidan"
                                    class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] px-4 text-sm text-slate-800 outline-none transition placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Isi jika bidan memiliki NIP.
                                </p>
                            </div>

                            <div>
                                <label for="no_telp" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Nomor Telepon
                                </label>

                                <input
                                    id="no_telp"
                                    type="tel"
                                    name="no_telp"
                                    value="{{ $noTelp }}"
                                    placeholder="Masukkan nomor telepon"
                                    autocomplete="tel"
                                    class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] px-4 text-sm text-slate-800 outline-none transition placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Nomor yang dapat dihubungi jika diperlukan.
                                </p>
                            </div>

                        </div>
                    </section>

                    {{-- KEAMANAN AKUN --}}
                    <section class="space-y-5 border-t border-slate-100 pt-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">
                                Keamanan Akun
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                {{ $isEdit
                                    ? 'Kosongkan kata sandi jika tidak ingin mengubahnya.'
                                    : 'Buat kata sandi untuk digunakan saat login.' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- PASSWORD --}}
                            <div>
                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                    <label for="password" class="text-sm font-semibold text-slate-700">
                                        Password
                                        @unless ($isEdit)
                                            <span class="text-red-500">*</span>
                                        @endunless
                                    </label>

                                    <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                        Min. 8 karakter
                                    </span>
                                </div>

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Masukkan password' }}"
                                    autocomplete="new-password"
                                    {{ $isEdit ? '' : 'required' }}
                                    minlength="8"
                                    class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] px-4 text-sm text-slate-800 outline-none transition placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Minimal 8 karakter. Gunakan kombinasi huruf dan angka.
                                </p>
                            </div>

                            {{-- KONFIRMASI PASSWORD --}}
                            <div>
                                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Konfirmasi Password
                                    @unless ($isEdit)
                                        <span class="text-red-500">*</span>
                                    @endunless
                                </label>

                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Masukkan kembali password"
                                    autocomplete="new-password"
                                    {{ $isEdit ? '' : 'required' }}
                                    minlength="8"
                                    class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] px-4 text-sm text-slate-800 outline-none transition placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Pastikan password sama persis.
                                </p>
                            </div>

                        </div>
                    </section>

                    {{-- WILAYAH TUGAS --}}
                    <section class="space-y-4 border-t border-slate-100 pt-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">
                                Wilayah Tugas
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Tentukan Posyandu tempat bidan bertugas.
                            </p>
                        </div>

                        <div>
                            <label for="id_posyandu" class="mb-2 block text-sm font-semibold text-slate-700">
                                Posyandu <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="id_posyandu"
                                name="id_posyandu"
                                required
                                class="h-12 w-full cursor-pointer rounded-xl border-0 bg-[#F2F3FF] px-4 text-sm text-slate-800 outline-none transition focus:ring-2 focus:ring-blue-200"
                            >
                                <option value="" disabled {{ $idPosyandu == '' ? 'selected' : '' }}>
                                    Pilih wilayah tugas
                                </option>

                                @foreach ($posyanduList as $item)
                                    <option
                                        value="{{ $item->id_posyandu }}"
                                        {{ (string) $idPosyandu === (string) $item->id_posyandu ? 'selected' : '' }}
                                    >
                                        {{ $item->nama_posyandu }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Wilayah tugas digunakan untuk menentukan area pelayanan bidan.
                            </p>
                        </div>
                    </section>

                    {{-- PEMBERITAHUAN SISTEM --}}
                    <div class="flex gap-3 rounded-xl border border-blue-100 bg-blue-50/70 p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M10.3 3.9l-7 12.1A2 2 0 005 19h14a2 2 0 001.7-3l-7-12.1a2 2 0 00-3.4 0z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-800">
                                Pemberitahuan Sistem
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Akun Bidan akan terdaftar pada sistem dan dapat ditugaskan sebagai
                                Bidan Pendamping pada Posyandu di wilayah kerja terkait di seluruh
                                Kabupaten Indramayu.
                            </p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500">
                        <span class="text-red-500">*</span>
                        Semua field bertanda bintang wajib diisi.
                    </p>
                </div>

                {{-- FOOTER FORM --}}
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-5 sm:flex-row sm:justify-end sm:px-7 lg:px-8">

                    <a
                        href="{{ route('superadmin.pengguna.index') }}"
                        class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-6 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>

                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Bidan' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
