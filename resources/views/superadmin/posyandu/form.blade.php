@extends('layouts.superadmin')

@section('title', isset($posyandu) ? 'Edit Unit Posyandu' : 'Registrasi Unit Posyandu')

@section('content')
    @php
        $isEdit = isset($posyandu);
        $namaPosyandu = old('nama_posyandu', $posyandu->nama_posyandu ?? '');
        $alamat = old('alamat', $posyandu->alamat ?? '');
        $kecamatan = old('kecamatan', $posyandu->kecamatan ?? '');
        $desa = old('desa_kelurahan', $posyandu->desa_kelurahan ?? '');
        $latitude = old('latitude', $posyandu->latitude ?? '');
        $longitude = old('longitude', $posyandu->longitude ?? '');
        $username = old('username', $posyandu->penggunaAkses->username ?? '');
    @endphp

    <div class="mx-auto max-w-[1400px] space-y-5 pb-8">
        {{-- Header halaman --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-8 tracking-tight text-slate-800">
                    {{ $isEdit ? 'Edit Unit Posyandu' : 'Registrasi Unit Posyandu' }}
                </h1>
                <p class="mt-1 text-sm leading-6 text-slate-500">
                    {{ $isEdit
                        ? 'Perbarui informasi unit layanan dan akun akses Posyandu.'
                        : 'Tambahkan data Posyandu dan akun akses untuk Posyandu.' }}
                </p>
            </div>

            <a
                href="{{ route('superadmin.posyandu.index') }}"
                class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        {{-- Pesan validasi --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700" role="alert">
                <p class="mb-2 text-sm font-semibold">Data belum berhasil disimpan.</p>
                <ul class="ml-5 list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ $isEdit
                ? route('superadmin.posyandu.update', $posyandu->id_posyandu)
                : route('superadmin.posyandu.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-12">
                {{-- Kartu informasi Posyandu --}}
                <section class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6 xl:col-span-7">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#155DFC]">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 13v.01M15 16v.01M15 19v.01" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold tracking-tight text-slate-900">Informasi Posyandu</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Data administratif unit Posyandu di wilayah kerja Puskesmas.
                            </p>
                        </div>
                    </div>

                    {{-- Nama Posyandu --}}
                    <div class="space-y-2">
                        <label for="nama_posyandu" class="text-sm font-semibold text-slate-800">
                            Nama Posyandu <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4" />
                            </svg>
                            <input
                                id="nama_posyandu"
                                type="text"
                                name="nama_posyandu"
                                value="{{ $namaPosyandu }}"
                                placeholder="Masukkan nama Posyandu (contoh: Posyandu Mawar 1)"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50"
                            >
                        </div>
                    </div>

                    {{-- Alamat lengkap --}}
                    <div class="space-y-2">
                        <label for="alamat" class="text-sm font-semibold text-slate-800">
                            Alamat Lengkap <span class="text-red-600">*</span>
                        </label>
                        <textarea
                            id="alamat"
                            name="alamat"
                            rows="3"
                            required
                            placeholder="Masukkan alamat lengkap Posyandu (contoh: Jl. Ki Buyut No. 15, RT 02/RW 01)"
                            class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-5 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50"
                        >{{ $alamat }}</textarea>
                    </div>

                    {{-- Kabupaten dan bidan pendamping --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="kabupaten" class="text-sm font-semibold text-slate-800">Kabupaten / Kota</label>
                            <input
                                id="kabupaten"
                                type="text"
                                value="Indramayu"
                                readonly
                                class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-500 outline-none"
                            >
                            <p class="text-xs leading-5 text-slate-500">
                                Kabupaten telah ditetapkan untuk sistem Posyandu-In.
                            </p>
                        </div>

                        @if (isset($bidanList))
                            <div class="space-y-2">
                                <label for="id_bidan" class="text-sm font-semibold text-slate-800">Bidan Pendamping</label>
                                <select
                                    id="id_bidan"
                                    name="id_bidan"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50"
                                >
                                    <option value="">Pilih Bidan Pendamping</option>
                                    @foreach ($bidanList as $bidan)
                                        <option
                                            value="{{ $bidan->id_bidan }}"
                                            {{ (string) old('id_bidan', $posyandu->id_bidan ?? '') === (string) $bidan->id_bidan ? 'selected' : '' }}
                                        >
                                            {{ $bidan->nama_bidan }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs leading-5 text-slate-500">
                                    Pilih bidan yang mendampingi unit Posyandu ini.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Kartu lokasi Posyandu --}}
                <section class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6 xl:col-span-5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#155DFC]">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1116 0z" />
                                <circle cx="12" cy="10" r="2.5" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold tracking-tight text-slate-900">Lokasi Posyandu</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Tentukan wilayah administratif dan titik lokasi unit.
                            </p>
                        </div>
                    </div>

                    {{-- Kecamatan --}}
                    <div class="space-y-2">
                        <label for="kecamatan" class="text-sm font-semibold text-slate-800">
                            Kecamatan <span class="text-red-600">*</span>
                        </label>
                        <select
                            id="kecamatan"
                            name="kecamatan"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50"
                        >
                            <option value="" disabled {{ $kecamatan === '' ? 'selected' : '' }}>Pilih Kecamatan</option>
                        </select>
                    </div>

                    {{-- Desa / kelurahan --}}
                    <div class="space-y-2">
                        <label for="desa" class="text-sm font-semibold text-slate-800">
                            Desa / Kelurahan <span class="text-red-600">*</span>
                        </label>
                        <select
                            id="desa"
                            name="desa_kelurahan"
                            required
                            {{ $kecamatan === '' ? 'disabled' : '' }}
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <option value="" disabled {{ $desa === '' ? 'selected' : '' }}>
                                {{ $kecamatan === '' ? 'Pilih Kecamatan Dahulu' : 'Pilih Desa / Kelurahan' }}
                            </option>
                        </select>
                    </div>

                    {{-- Koordinat --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="latitude" class="text-sm font-semibold text-slate-800">Latitude</label>
                            <input
                                id="latitude"
                                type="number"
                                step="any"
                                name="latitude"
                                value="{{ $latitude }}"
                                placeholder="-6.40123779"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50"
                            >
                        </div>
                        <div class="space-y-2">
                            <label for="longitude" class="text-sm font-semibold text-slate-800">Longitude</label>
                            <input
                                id="longitude"
                                type="number"
                                step="any"
                                name="longitude"
                                value="{{ $longitude }}"
                                placeholder="108.27668902"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50"
                            >
                        </div>
                    </div>

                    {{-- Peta lokasi --}}
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-800">Titik Lokasi Posyandu</label>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Klik peta atau geser marker biru untuk menentukan lokasi.
                            </p>
                        </div>
                        <div id="posyandu-map" class="w-full overflow-hidden rounded-xl border border-slate-200"></div>
                        <p id="map-status" role="status" class="text-xs leading-5 text-slate-500">
                            Pilih titik lokasi pada peta.
                        </p>
                    </div>
                </section>

                {{-- Kartu akun akses --}}
                <section class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6 xl:col-span-12">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#155DFC]">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <rect x="4" y="10" width="16" height="11" rx="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V7a4 4 0 118 0v3m-4 5v2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold tracking-tight text-slate-900">Akun Akses Posyandu</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Akun ini digunakan untuk login ke sistem oleh pengelola Posyandu.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {{-- Username --}}
                        <div class="space-y-2">
                            <label for="username" class="text-sm font-semibold text-slate-800">
                                Username
                                @unless ($isEdit)
                                    <span class="text-red-600">*</span>
                                @endunless
                            </label>
                            <input
                                id="username"
                                type="text"
                                name="username"
                                value="{{ $username }}"
                                {{ $isEdit ? 'readonly' : 'required' }}
                                autocomplete="username"
                                placeholder="Contoh: posyandu_melati"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50 read-only:cursor-not-allowed read-only:bg-slate-100"
                            >
                            <p class="text-xs leading-5 text-slate-500">
                                Gunakan username yang mudah dikenali dan belum terdaftar.
                            </p>
                        </div>

                        {{-- Password --}}
                        <div class="space-y-2">
                            <label for="password" class="text-sm font-semibold text-slate-800">
                                {{ $isEdit ? 'Password Baru' : 'Password' }}
                                @unless ($isEdit)
                                    <span class="text-red-600">*</span>
                                @endunless
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                {{ $isEdit ? '' : 'required' }}
                                minlength="6"
                                autocomplete="new-password"
                                placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-50"
                            >
                            <p class="text-xs leading-5 text-slate-500">
                                {{ $isEdit ? 'Isi hanya jika ingin mengganti password.' : 'Gunakan password minimal 6 karakter.' }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Informasi dan tombol aksi --}}
            <div class="flex flex-col gap-4 rounded-2xl border border-blue-100 bg-blue-50/70 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <p class="text-sm leading-6 text-slate-600">
                    <span class="font-semibold text-slate-800">Periksa kembali data.</span>
                    Pastikan nama, alamat, wilayah, dan titik lokasi Posyandu sudah benar.
                </p>

                <div class="flex shrink-0 flex-col-reverse gap-2 sm:flex-row">
                    <a
                        href="{{ route('superadmin.posyandu.index') }}"
                        class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 active:scale-[0.99]"
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
                        {{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Unit' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        #posyandu-map {
            display: block;
            width: 100%;
            height: 300px;
            min-height: 300px;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: #e5e7eb;
            z-index: 0;
        }

        #posyandu-map .leaflet-tile,
        #posyandu-map img {
            max-width: none !important;
            max-height: none !important;
        }

        #posyandu-map .leaflet-tile {
            padding: 0 !important;
            border: 0 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Daftar kecamatan dan desa di Kabupaten Indramayu.
            const dataWilayah = {
                "Anjatan": ["Anjatan", "Anjatan Baru", "Anjatan Utara", "Bugis", "Bugis Tua", "Cilandak", "Cilandak Lor", "Kedungwungu", "Kopyah", "Lempuyang", "Mangunjaya", "Salamdarma", "Wanguk"],
                "Arahan": ["Arahan Kidul", "Arahan Lor", "Cidempet", "Linggajati", "Pranggong", "Sukadadi", "Sukasari", "Tawangsari"],
                "Balongan": ["Balongan", "Gelarmendala", "Majakerta", "Rawadalem", "Sudimampir", "Sudimampir Lor", "Sukareja", "Sukaurip", "Tegalsembrada", "Tegalurung"],
                "Bangodua": ["Bangodua", "Beduyut", "Karanggetas", "Malangsari", "Mulyasari", "Rancasari", "Tegalgirang", "Wanasari"],
                "Bongas": ["Bongas", "Cipaat", "Cipedang", "Kertajaya", "Kertamulya", "Margamulya", "Plawangan", "Sidamulya"],
                "Cantigi": ["Cangkring", "Cantigi Kulon", "Cantigi Wetan", "Cemara", "Lamarantarung", "Panyingkiran Kidul", "Panyingkiran Lor"],
                "Cikedung": ["Amis", "Cikedung", "Cikedung Lor", "Jambak", "Jatisura", "Loyang", "Mundakjaya"],
                "Gabuswetan": ["Babakanjaya", "Drunten Kulon", "Drunten Wetan", "Gabuskulon", "Gabuswetan", "Kedokangabus", "Kedungdawa", "Rancahan", "Rancamulya", "Sekarmulya"],
                "Gantar": ["Baleraja", "Bantarwaru", "Gantar", "Mekarjaya", "Mekarwaru", "Sanca", "Situraja"],
                "Haurgeulis": ["Cipancuh", "Haurgeulis", "Haurkolot", "Karangtumaritis", "Kertanegara", "Mekarjati", "Sidadadi", "Sukajati", "Sumbermulya", "Wanakaya"],
                "Indramayu": ["Dukuh", "Karangsong", "Pabeanudik", "Pekandangan", "Pekandangan Jaya", "Plumbon", "Singajaya", "Singaraja", "Tambak", "Telukagung", "Bojongsari", "Karanganyar", "Karangmalang", "Kepandean", "Lemahabang", "Lemahmekar", "Margadadi", "Paoman"],
                "Jatibarang": ["Bulak", "Bulak Lor", "Jatibarang", "Jatibarang Baru", "Jatisawit", "Jatisawit Lor", "Kalimati", "Kebulen", "Krasak", "Lobener", "Lobener Lor", "Malang Semirang", "Pawidean", "Pilangsari", "Sukalila"],
                "Juntinyuat": ["Dadap", "Juntikebon", "Juntinyuat", "Juntiweden", "Limbangan", "Pondoh", "Sambimaya", "Segeran", "Segeran Kidul", "Tinumpuk"],
                "Kandanghaur": ["Bulak", "Curug", "Eretan Kulon", "Eretan Wetan", "Ilir", "Karanganyar", "Karangmulya", "Kertawinangun", "Pareangirang", "Pranti", "Soge", "Wirakanan", "Wirapanjunan"],
                "Karangampel": ["Benda", "Dukuh Jeruk", "Dukuh Tengah", "Mundu", "Kaplongan Lor", "Karangampel Kidul", "Karangampel", "Pringgacala", "Sendang", "Tanjungpura", "Tanjungsari"],
                "Kedokan Bunder": ["Cangkingan", "Jayalaksana", "Jayawinangun", "Kaplongan", "Kedokan Agung", "Kedokan Bunder", "Kedokan Bunder Wetan"],
                "Kertasemaya": ["Jambe", "Jengkok", "Kertasmaya", "Kliwed", "Lemahayu", "Manguntara", "Sukawera", "Tenajar", "Tenajar Kidul", "Tenajar Lor", "Tulungagung"],
                "Krangkeng": ["Dukuh Jati", "Kalianyar", "Kapringan", "Kedungwungu", "Krangkeng", "Luwunggesik", "Purwajaya", "Singakerta", "Srengseng", "Tanjakan", "Tegalmulya"],
                "Kroya": ["Jayamulya", "Kroya", "Sukamelang", "Sukaslamet", "Sumberjaya", "Sumbon", "Tanjungkerta", "Temiyang", "Temiyangsari"],
                "Lelea": ["Cempeh", "Langgengsari", "Lelea", "Nunuk", "Pangauban", "Tamansari", "Telagasari", "Tempel", "Tempelkulon", "Tugu", "Tunggulpayung"],
                "Lohbener": ["Bojongslawi", "Kiajaran Kulon", "Kiajaran Wetan", "Lanjan", "Langut", "Larangan", "Legok", "Lohbener", "Pamayahan", "Rambatan Kulon", "Sindangkerta", "Waru"],
                "Losarang": ["Cemara Kulon", "Jangga", "Jumbleng", "Krimun", "Losarang", "Muntur", "Pangkalan", "Pegagan", "Puntang", "Rajaiyang", "Santing", "Ranjeng"],
                "Pasekan": ["Brondong", "Karanganyar", "Pasekan", "Pabean Ilir", "Pagirikan", "Totoran"],
                "Patrol": ["Arjasari", "Bugel", "Limpas", "Mekarsari", "Patrol", "Patrol Baru", "Patrol Lor", "Sukahaji"],
                "Sindang": ["Babadan", "Dermayu", "Kenanga", "Panyindangan Kulon", "Panyindangan Wetan", "Penganjang", "Rambatan Wetan", "Sindang", "Terusan", "Wanantara"],
                "Sliyeg": ["Gadingan", "Longok", "Majasari", "Majasih", "Mekargading", "Sleman", "Sleman Lor", "Sliyeg", "Sliyeg Lor", "Sudikampiran", "Tambi", "Tambi Lor", "Tugu", "Tuguk Kidul"],
                "Sukagumiwang": ["Bondan", "Cadangpinggan", "Cibeber", "Gedangan", "Gunungsari", "Sukagumiwang", "Tersana"],
                "Sukra": ["Bogor", "Karanglayung", "Sukra", "Sukra Wetan", "Sumuradem", "Sumuradem Timur", "Tegal Taman", "Ujunggebang"],
                "Terisi": ["Cibereng", "Cikawung", "Jatimulya", "Jatimunggul", "Karangasem", "Kendayakan", "Maanggungan", "Plosokerep", "Rajasinga"],
                "Tukdana": ["Bodas", "Cangko", "Gadel", "Karangkerta", "Kerticala", "Lajer", "Mekarsari", "Pagedangan", "Rancajawat", "Sukadana", "Sukamulya", "Sukaperna", "Tukdana"],
                "Widasari": ["Bangkaloa Ilir", "Bunder", "Kalensari", "Kasmaran", "Kongsijaya", "Leuwigede", "Ujungaris", "Ujungjaya", "Ujung pendok Jaya", "Widasari"]
            };

            const kecamatanSelect = document.getElementById('kecamatan');
            const desaSelect = document.getElementById('desa');

            // Ambil pilihan sebelumnya dari Laravel.
            const oldKecamatan = @json(old('kecamatan', $posyandu->kecamatan ?? ''));
            const oldDesa = @json(old('desa_kelurahan', $posyandu->desa_kelurahan ?? ''));

            // Elemen untuk peta dan informasi alamat.
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const alamatInput = document.querySelector('[name="alamat"]');
            const mapStatus = document.getElementById('map-status');

            const defaultLocation = [-6.327, 108.32];
            const savedLat = parseFloat(latitudeInput.value);
            const savedLng = parseFloat(longitudeInput.value);

            const initialLocation =
                Number.isFinite(savedLat) && Number.isFinite(savedLng)
                    ? [savedLat, savedLng]
                    : defaultLocation;

            const map = L.map('posyandu-map').setView(initialLocation, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            let marker = null;
            let lookupTimer = null;
            let lookupController = null;

            function setMarker(lat, lng, reverseLookup = true) {
                latitudeInput.value = Number(lat).toFixed(8);
                longitudeInput.value = Number(lng).toFixed(8);

                if (!marker) {
                    marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map);

                    marker.on('dragend', function (event) {
                        const position = event.target.getLatLng();
                        setMarker(position.lat, position.lng);
                    });
                } else {
                    marker.setLatLng([lat, lng]);
                }

                if (reverseLookup) {
                    clearTimeout(lookupTimer);
                    lookupTimer = setTimeout(() => {
                        reverseGeocode(lat, lng);
                    }, 800);
                }
            }

            map.on('click', function (event) {
                setMarker(event.latlng.lat, event.latlng.lng);
            });

            async function reverseGeocode(lat, lng) {
                if (lookupController) {
                    lookupController.abort();
                }

                lookupController = new AbortController();
                mapStatus.textContent = 'Mencari alamat berdasarkan titik lokasi...';

                try {
                    const url = new URL('https://nominatim.openstreetmap.org/reverse');

                    url.search = new URLSearchParams({
                        format: 'jsonv2',
                        lat: lat,
                        lon: lng,
                        zoom: '18',
                        addressdetails: '1',
                        'accept-language': 'id'
                    });

                    const response = await fetch(url, {
                        signal: lookupController.signal,
                        headers: {
                            Accept: 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Layanan pencarian alamat gagal.');
                    }

                    const result = await response.json();

                    if (!result.address) {
                        throw new Error('Alamat tidak ditemukan.');
                    }

                    const address = result.address;
                    const namaKecamatan =
                        address.county ||
                        address.city_district ||
                        address.state_district ||
                        '';

                    const kecamatanCocok = Object.keys(dataWilayah).find(
                        kecamatan => kecamatan.toLowerCase() === namaKecamatan.toLowerCase()
                    );

                    if (kecamatanCocok) {
                        kecamatanSelect.value = kecamatanCocok;

                        const namaDesa =
                            address.village ||
                            address.suburb ||
                            address.neighbourhood ||
                            address.town ||
                            address.city ||
                            '';

                        updateDesa(kecamatanCocok);

                        const desaCocok = [...desaSelect.options].find(
                            option => option.value.toLowerCase() === namaDesa.toLowerCase()
                        );

                        if (desaCocok) {
                            desaSelect.value = desaCocok.value;
                        }
                    }

                    if (result.display_name) {
                        alamatInput.value = result.display_name;
                    }

                    mapStatus.textContent =
                        'Alamat ditemukan. Periksa kembali kecamatan, desa, dan alamat sebelum menyimpan.';
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    console.error(error);
                    mapStatus.textContent =
                        'Alamat otomatis tidak tersedia. Silakan isi alamat secara manual.';
                }
            }

            // Tampilkan marker jika form edit memiliki koordinat tersimpan.
            if (Number.isFinite(savedLat) && Number.isFinite(savedLng)) {
                setMarker(savedLat, savedLng, false);
            }

            function fixMapSize() {
                map.invalidateSize(true);
            }

            window.addEventListener('load', fixMapSize);
            window.addEventListener('resize', fixMapSize);

            setTimeout(fixMapSize, 300);
            setTimeout(fixMapSize, 1000);

            // Isi pilihan kecamatan.
            function populateKecamatan() {
                kecamatanSelect.innerHTML =
                    '<option value="" disabled selected>Pilih Kecamatan</option>';

                Object.keys(dataWilayah).sort().forEach(kecamatan => {
                    const option = document.createElement('option');
                    option.value = kecamatan;
                    option.textContent = kecamatan;

                    if (kecamatan === oldKecamatan) {
                        option.selected = true;
                    }

                    kecamatanSelect.appendChild(option);
                });

                if (oldKecamatan) {
                    updateDesa(oldKecamatan, oldDesa);
                }
            }

            // Isi pilihan desa sesuai kecamatan yang dipilih.
            function updateDesa(kecamatan, selectedDesa = '') {
                const desaList = dataWilayah[kecamatan];

                desaSelect.innerHTML =
                    '<option value="" disabled selected>Pilih Desa / Kelurahan</option>';

                if (desaList) {
                    desaSelect.disabled = false;

                    [...desaList].sort().forEach(desa => {
                        const option = document.createElement('option');
                        option.value = desa;
                        option.textContent = desa;

                        if (desa === selectedDesa) {
                            option.selected = true;
                        }

                        desaSelect.appendChild(option);
                    });
                } else {
                    desaSelect.disabled = true;
                }
            }

            populateKecamatan();

            kecamatanSelect.addEventListener('change', function () {
                updateDesa(this.value);
            });
        });
    </script>
@endpush
