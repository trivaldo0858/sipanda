```blade
@extends('layouts.auth')

@section('title', 'Dashboard Orang Tua | Posyandu-In')

@section('content')
    <div class="min-h-screen bg-[#F4F6FB] font-sans text-slate-800">

        {{-- Header --}}
        <header class="border-b border-slate-100 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8">
                <a href="{{ route('orang-tua.dashboard') }}" class="text-xl font-extrabold tracking-tight text-[#155DFC]">
                    Posyandu-In
                </a>

                <form action="{{ route('orang-tua.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Konten --}}
        <main class="mx-auto max-w-7xl space-y-6 px-5 py-8 sm:px-8">

            {{-- Sapaan --}}
            <section>
                <p class="text-sm font-semibold text-[#155DFC]">
                    Dashboard Orang Tua
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
                    Informasi Kesehatan Anak
                </h1>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Pantau informasi pertumbuhan dan kegiatan Posyandu anak Anda.
                </p>
            </section>

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Profil Anak --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-2xl font-bold text-[#155DFC]">
                        {{ strtoupper(substr($anak->nama_anak, 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-500">Profil Anak</p>

                        <h2 class="mt-1 text-xl font-bold text-slate-800">
                            {{ $anak->nama_anak }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $anak->jenis_kelamin_label }}
                            <span class="mx-1">•</span>
                            {{ $anak->umur_format }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-blue-50 px-4 py-3 sm:min-w-48">
                        <p class="text-xs font-medium text-slate-500">
                            Tanggal Lahir
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $anak->tgl_lahir->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Nama Ibu</p>
                        <p class="mt-1 font-semibold text-slate-700">
                            {{ $anak->nama_ibu ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-500">Posyandu</p>
                        <p class="mt-1 font-semibold text-slate-700">
                            {{ $anak->posyandu->nama_posyandu ?? '-' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Pemeriksaan Terakhir --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">
                            Pemeriksaan Terakhir
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Ringkasan pemeriksaan pertumbuhan anak.
                        </p>
                    </div>

                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-[#155DFC]">
                        Data Pemeriksaan
                    </span>
                </div>

                @if ($pemeriksaanTerakhir)
                        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Tanggal Pemeriksaan</p>
                                <p class="mt-2 font-bold text-slate-800">
                                    {{ $pemeriksaanTerakhir->tgl_periksa
                    ? \Illuminate\Support\Carbon::parse($pemeriksaanTerakhir->tgl_periksa)->format('d M Y')
                    : '-' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Berat Badan</p>
                                <p class="mt-2 font-bold text-slate-800">
                                    {{ $pemeriksaanTerakhir->bb !== null
                    ? $pemeriksaanTerakhir->bb . ' kg'
                    : '-' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Tinggi/Panjang Badan</p>
                                <p class="mt-2 font-bold text-slate-800">
                                    {{ $pemeriksaanTerakhir->tb !== null
                    ? $pemeriksaanTerakhir->tb . ' cm'
                    : '-' }}
                                </p>
                            </div>
                        </div>
                @else
                    <div class="mt-5 rounded-xl bg-slate-50 px-4 py-8 text-center">
                        <p class="font-semibold text-slate-700">
                            Belum ada data pemeriksaan
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            Data akan ditampilkan setelah pemeriksaan anak tercatat.
                        </p>
                    </div>
                @endif
            </section>

            {{-- Jadwal Posyandu --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800">
                    Jadwal Posyandu Terdekat
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi kegiatan Posyandu untuk anak Anda.
                </p>

                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-6 text-sm text-slate-500">
                    Informasi jadwal akan ditampilkan setelah integrasi dengan data jadwal selesai.
                </div>
            </section>

            {{-- Prediksi Stunting --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800">
                    Hasil Prediksi Risiko Stunting
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi pendukung untuk memantau pertumbuhan anak.
                </p>

                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-6 text-sm text-slate-500">
                    Hasil prediksi akan ditampilkan setelah integrasi dengan fitur prediksi selesai.
                </div>

                <p class="mt-4 text-xs leading-5 text-slate-400">
                    Informasi prediksi merupakan alat bantu pemantauan, bukan diagnosis medis.
                    Konsultasikan hasil pengukuran dan kekhawatiran pertumbuhan anak kepada tenaga kesehatan.
                </p>
            </section>

        </main>
    </div>
@endsection