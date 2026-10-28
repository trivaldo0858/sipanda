@extends('layouts.posyandu')

@section('title', 'Dashboard Posyandu')

@section('content')
    <div class="space-y-8">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#155DFC]">
                    Posyandu-In
                </p>

                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-800">
                    Dashboard Posyandu
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Ringkasan pengelolaan data dan kegiatan Posyandu.
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                <p class="text-xs font-medium text-slate-400">
                    Unit Posyandu
                </p>
                <p class="mt-1 text-sm font-bold text-slate-700">
                    {{ $posyandu?->nama_posyandu ?? 'Unit Posyandu' }}
                </p>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">

            {{-- Total Anak --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Anak Terdaftar
                        </p>

                        <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-800">
                            {{ number_format($totalAnak) }}
                        </p>

                        <p class="mt-2 text-xs text-slate-400">
                            Data anak pada sistem
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EBF3FF] text-[#155DFC]">
                        <i class="fa-solid fa-child-reaching text-xl" aria-hidden="true"></i>
                    </div>
                </div>

                <a href="{{ route('posyandu.anak.index') }}"
                    class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#155DFC] hover:text-blue-700">
                    Lihat data anak
                    <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>
            </div>

            {{-- Total Pemeriksaan --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Pemeriksaan
                        </p>

                        <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-800">
                            {{ number_format($totalPemeriksaan) }}
                        </p>

                        <p class="mt-2 text-xs text-slate-400">
                            Catatan pemeriksaan tersimpan
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EBF3FF] text-[#155DFC]">
                        <i class="fa-solid fa-notes-medical text-xl" aria-hidden="true"></i>
                    </div>
                </div>

                <a href="{{ route('posyandu.pemeriksaan.index') }}"
                    class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#155DFC] hover:text-blue-700">
                    Input pemeriksaan
                    <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>
            </div>

            {{-- Total Jadwal --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Jadwal Posyandu
                        </p>

                        <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-800">
                            {{ number_format($totalJadwal) }}
                        </p>

                        <p class="mt-2 text-xs text-slate-400">
                            Jadwal yang tercatat pada sistem
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EBF3FF] text-[#155DFC]">
                        <i class="fa-regular fa-calendar-days text-xl" aria-hidden="true"></i>
                    </div>
                </div>

                <a href="{{ route('posyandu.jadwal.index') }}"
                    class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#155DFC] hover:text-blue-700">
                    Kelola jadwal
                    <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>
            </div>

        </div>

        {{-- Akses Cepat --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Akses Cepat
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Pilih aktivitas yang ingin dilakukan.
                </p>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                <a href="{{ route('posyandu.anak.index') }}"
                    class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-[#155DFC]/30 hover:bg-[#F8FAFF]">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EBF3FF] text-[#155DFC]">
                        <i class="fa-solid fa-users text-lg" aria-hidden="true"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800">
                            Kelola Data Anak
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Lihat dan daftarkan data anak.
                        </p>
                    </div>

                    <i class="fa-solid fa-chevron-right text-xs text-slate-400 transition group-hover:text-[#155DFC]"
                        aria-hidden="true"></i>
                </a>

                <a href="{{ route('posyandu.pemeriksaan.index') }}"
                    class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-[#155DFC]/30 hover:bg-[#F8FAFF]">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EBF3FF] text-[#155DFC]">
                        <i class="fa-solid fa-clipboard-check text-lg" aria-hidden="true"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800">
                            Input Pemeriksaan
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Catat hasil pengukuran anak.
                        </p>
                    </div>

                    <i class="fa-solid fa-chevron-right text-xs text-slate-400 transition group-hover:text-[#155DFC]"
                        aria-hidden="true"></i>
                </a>

                <a href="{{ route('posyandu.jadwal.index') }}"
                    class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-[#155DFC]/30 hover:bg-[#F8FAFF]">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EBF3FF] text-[#155DFC]">
                        <i class="fa-regular fa-calendar-days text-lg" aria-hidden="true"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800">
                            Jadwal Posyandu
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Periksa dan kelola jadwal kegiatan.
                        </p>
                    </div>

                    <i class="fa-solid fa-chevron-right text-xs text-slate-400 transition group-hover:text-[#155DFC]"
                        aria-hidden="true"></i>
                </a>

                <a href="{{ route('posyandu.laporan.index') }}"
                    class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-[#155DFC]/30 hover:bg-[#F8FAFF]">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EBF3FF] text-[#155DFC]">
                        <i class="fa-solid fa-chart-column text-lg" aria-hidden="true"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800">
                            Laporan Bulanan
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Lihat laporan kegiatan Posyandu.
                        </p>
                    </div>

                    <i class="fa-solid fa-chevron-right text-xs text-slate-400 transition group-hover:text-[#155DFC]"
                        aria-hidden="true"></i>
                </a>

            </div>
        </section>

    </div>
@endsection