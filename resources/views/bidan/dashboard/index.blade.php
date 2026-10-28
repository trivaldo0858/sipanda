@extends('layouts.bidan')

@section('title', 'Dashboard Bidan')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">
                    Dashboard Bidan
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan data dan aktivitas Posyandu.
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-2">
                <p class="text-xs text-slate-500">Bidan</p>
                <p class="font-semibold text-slate-800">
                    {{ $bidan->nama_bidan }}
                </p>
            </div>
        </div>

        {{-- Informasi Posyandu --}}
        <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                Posyandu tempat bertugas
            </p>

            <h2 class="mt-1 text-lg font-bold text-slate-800">
                {{ $bidan->posyandu->nama_posyandu ?? 'Posyandu belum ditentukan' }}
            </h2>
        </div>

        {{-- Kartu Statistik --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total Anak
                    </p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-800">
                        {{ number_format($stats['total_anak']) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Anak terdaftar di Posyandu
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="7" r="4" />
                        <path d="M2 21v-2a7 7 0 0 1 14 0v2" />
                        <path d="M16 3.5a4 4 0 0 1 0 7.5" />
                        <path d="M19 14a5 5 0 0 1 3 4.5V21" />
                    </svg>
                </div>
            </div>

            <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total Pemeriksaan
                    </p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-800">
                        {{ number_format($stats['total_pemeriksaan']) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Catatan pemeriksaan Posyandu
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="3" width="16" height="18" rx="2" />
                        <path d="M8 8h8M8 12h8M8 16h5" />
                    </svg>
                </div>
            </div>

        </div>

        {{-- Pemeriksaan Terbaru --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">
                    Pemeriksaan Terbaru
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Lima catatan pemeriksaan terakhir di Posyandu.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Nama Anak</th>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold">Berat Badan</th>
                            <th class="px-5 py-3 font-semibold">Tinggi Badan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pemeriksaanTerbaru as $pemeriksaan)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-5 py-4 font-medium text-slate-700">
                                                {{ $pemeriksaan->anak->nama_anak ?? '-' }}
                                            </td>

                                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                                {{ $pemeriksaan->tgl_periksa
                            ? \Carbon\Carbon::parse($pemeriksaan->tgl_periksa)->format('d/m/Y')
                            : '-' }}
                                            </td>

                                            <td class="px-5 py-4 text-slate-600">
                                                {{ $pemeriksaan->berat_badan !== null
                            ? $pemeriksaan->berat_badan . ' kg'
                            : '-' }}
                                            </td>

                                            <td class="px-5 py-4 text-slate-600">
                                                {{ $pemeriksaan->tinggi_badan !== null
                            ? $pemeriksaan->tinggi_badan . ' cm'
                            : '-' }}
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">
                                    Belum ada data pemeriksaan di Posyandu ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection