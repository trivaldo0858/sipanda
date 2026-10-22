@extends('layouts.orang_tua')

@section('title', 'Dashboard Orang Tua | Posyandu-In')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('orang-tua.dashboard') }}" class="flex shrink-0 items-center gap-3"
                    aria-label="Dashboard Posyandu-In">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#155DFC] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                            <rect x="3" y="3" width="18" height="18" rx="5" />
                        </svg>
                    </div>

                    <div>
                        <span class="block text-lg font-extrabold tracking-tight text-[#155DFC] sm:text-xl">
                            Posyandu-In
                        </span>
                        <span class="hidden text-xs text-slate-500 sm:block">
                            Informasi Kesehatan Anak
                        </span>
                    </div>
                </a>

                <form action="{{ route('orang-tua.logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-200 sm:px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 17l5-5-5-5M15 12H3m9-8h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <section>
            <p class="text-sm font-semibold text-[#155DFC]">
                Dashboard Orang Tua
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
                Informasi Kesehatan Anak
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Pantau informasi pertumbuhan dan jadwal kegiatan Posyandu anak Anda
                melalui satu halaman.
            </p>
        </section>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div role="status"
                class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Profil anak --}}
        <section id="profil-anak" class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-800">
                    Profil Anak
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi dasar anak yang terdaftar di Posyandu-In.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-2xl font-bold text-[#155DFC]">
                        {{ strtoupper(substr($anak->nama_anak, 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="text-xl font-bold text-slate-800">
                            {{ $anak->nama_anak }}
                        </h3>

                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                            <span>{{ $anak->jenis_kelamin_label }}</span>
                            <span class="text-slate-300">•</span>
                            <span>{{ $anak->umur_format }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 sm:min-w-48">
                        <p class="text-xs font-medium text-slate-500">
                            Tanggal Lahir
                        </p>
                        <p class="mt-1 text-sm font-bold text-slate-800">
                            {{ $anak->tgl_lahir
        ? \Illuminate\Support\Carbon::parse($anak->tgl_lahir)->format('d M Y')
        : '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Nama Ibu
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $anak->nama_ibu ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Posyandu Terdaftar
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $anak->posyandu->nama_posyandu ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Pemeriksaan terakhir --}}
        <section id="pemeriksaan" class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800 sm:text-lg">
                        Pemeriksaan Terakhir
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan hasil pengukuran pertumbuhan anak.
                    </p>
                </div>

                <span
                    class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-[#155DFC]">
                    Data Pemeriksaan
                </span>
            </div>

            @if ($pemeriksaanTerakhir)
                <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <p class="text-xs font-medium text-slate-500">
                        Tanggal Pemeriksaan
                    </p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ $pemeriksaanTerakhir->tgl_periksa
                ? \Illuminate\Support\Carbon::parse($pemeriksaanTerakhir->tgl_periksa)->format('d M Y')
                : '-' }}
                    </p>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex items-center gap-2">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-[#155DFC]">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-6 6 6 6-6M5 7h14" />
                                </svg>
                            </div>
                            <p class="text-sm text-slate-500">Berat Badan</p>
                        </div>

                        <p class="mt-3 text-xl font-bold text-slate-800">
                            @if ($pemeriksaanTerakhir->berat_badan !== null)
                                {{ $pemeriksaanTerakhir->berat_badan }}
                                <span class="text-sm font-medium text-slate-500">kg</span>
                            @else
                                <span class="text-sm font-medium text-slate-400">Belum tersedia</span>
                            @endif
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex items-center gap-2">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v18M8 7l4-4 4 4M8 17l4 4 4-4M5 9h3m-3 6h3m8-6h3m-3 6h3" />
                                </svg>
                            </div>
                            <p class="text-sm text-slate-500">Tinggi/Panjang Badan</p>
                        </div>

                        <p class="mt-3 text-xl font-bold text-slate-800">
                            @if ($pemeriksaanTerakhir->tinggi_badan !== null)
                                {{ $pemeriksaanTerakhir->tinggi_badan }}
                                <span class="text-sm font-medium text-slate-500">cm</span>
                            @else
                                <span class="text-sm font-medium text-slate-400">Belum tersedia</span>
                            @endif
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-100 p-4 sm:col-span-2 lg:col-span-1">
                        <div class="flex items-center gap-2">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" />
                                    <circle cx="12" cy="12" r="4" />
                                    <path stroke-linecap="round" d="M12 3v2m9 7h-2M12 21v-2M3 12h2" />
                                </svg>
                            </div>
                            <p class="text-sm text-slate-500">Lingkar Kepala</p>
                        </div>

                        <p class="mt-3 text-xl font-bold text-slate-800">
                            @if ($pemeriksaanTerakhir->lingkar_kepala !== null)
                                {{ $pemeriksaanTerakhir->lingkar_kepala }}
                                <span class="text-sm font-medium text-slate-500">cm</span>
                            @else
                                <span class="text-sm font-medium text-slate-400">Belum tersedia</span>
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="mt-5 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 3v3m8-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13H4V6a1 1 0 0 1 1-1Z" />
                        </svg>
                    </div>

                    <p class="mt-3 text-sm font-semibold text-slate-700">
                        Belum ada data pemeriksaan
                    </p>
                    <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-slate-500">
                        Informasi pengukuran akan muncul di sini setelah pemeriksaan anak tercatat.
                    </p>
                </div>
            @endif
        </section>

        {{-- Jadwal Posyandu --}}
        <section id="jadwal-posyandu" class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#155DFC]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M3 10h18" />
                    </svg>
                </div>

                <div>
                    <h2 class="text-base font-bold text-slate-800 sm:text-lg">
                        Jadwal Posyandu Terdekat
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Informasi kegiatan di Posyandu tempat anak terdaftar.
                    </p>
                </div>
            </div>

            @if ($jadwalTerdekat)
                <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50/50 p-4 sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div
                            class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-white text-[#155DFC] shadow-sm">
                            <span class="text-xl font-extrabold leading-none">
                                {{ \Illuminate\Support\Carbon::parse($jadwalTerdekat->tgl_kegiatan)->format('d') }}
                            </span>
                            <span class="mt-1 text-xs font-semibold uppercase">
                                {{ \Illuminate\Support\Carbon::parse($jadwalTerdekat->tgl_kegiatan)->translatedFormat('M') }}
                            </span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#155DFC]">
                                Jadwal Kegiatan
                            </p>

                            <h3 class="mt-1 text-base font-bold text-slate-800">
                                {{ $jadwalTerdekat->agenda ?: 'Kegiatan Posyandu' }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                {{ \Illuminate\Support\Carbon::parse($jadwalTerdekat->tgl_kegiatan)->translatedFormat('l, d F Y') }}
                            </p>

                            <div class="mt-3 flex items-start gap-2 text-sm text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                                    <circle cx="12" cy="10" r="2.5" />
                                </svg>
                                <span>{{ $jadwalTerdekat->lokasi ?: 'Lokasi belum dicantumkan' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-5 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-7 text-center">
                    <p class="text-sm font-semibold text-slate-700">
                        Belum ada jadwal mendatang
                    </p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Informasi kegiatan berikutnya akan ditampilkan setelah jadwal tersedia.
                    </p>
                </div>
            @endif
        </section>

        {{-- Prediksi risiko stunting --}}
        <section id="prediksi-stunting" class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3 4 7v5c0 5 3.5 7.5 8 9 4.5-1.5 8-4 8-9V7l-8-4Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                    </svg>
                </div>

                <div>
                    <h2 class="text-base font-bold text-slate-800 sm:text-lg">
                        Prediksi Risiko Stunting
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Informasi pendukung untuk pemantauan pertumbuhan anak.
                    </p>
                </div>
            </div>

            @if ($prediksiStunting)
                <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                    <p class="text-sm font-semibold text-slate-800">
                        Hasil prediksi tersedia
                    </p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        {{ $prediksiStunting }}
                    </p>
                </div>
            @else
                <div class="mt-5 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6">
                    <p class="text-sm font-semibold text-slate-700">
                        Hasil prediksi belum tersedia
                    </p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Informasi ini akan ditampilkan setelah fitur prediksi risiko
                        terintegrasi dan hasilnya tersedia.
                    </p>
                </div>
            @endif

            <p class="mt-4 text-xs leading-5 text-slate-400">
                Prediksi merupakan alat bantu pemantauan, bukan diagnosis medis.
                Konsultasikan hasil pengukuran dan kekhawatiran mengenai pertumbuhan
                anak kepada tenaga kesehatan.
            </p>
        </section>

        {{-- Footer --}}
        <footer class="pb-2 pt-1 text-center text-xs text-slate-400">
            Posyandu-In &copy; {{ date('Y') }}. Informasi kesehatan anak dalam satu tempat.
        </footer>
    </div>
@endsection