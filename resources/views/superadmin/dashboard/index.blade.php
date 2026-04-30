{{-- resources/views/superadmin/dashboard/index.blade.php --}}
@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-8">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Dashboard Utama
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Ringkasan data global sistem SIPANDA
                </p>
            </div>

            <div class="text-sm text-slate-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

            {{-- Total Balita --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                        👶
                    </div>

                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-600">
                        Anak
                    </span>
                </div>

                <p class="text-sm text-slate-500 uppercase tracking-wide">
                    Total Balita
                </p>

                <h3 class="text-4xl font-bold text-slate-900 mt-2">
                    {{ number_format($stats['total_anak']) }}
                </h3>
            </div>

            {{-- Posyandu --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                        🏥
                    </div>

                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                        Aktif
                    </span>
                </div>

                <p class="text-sm text-slate-500 uppercase tracking-wide">
                    Total Posyandu
                </p>

                <h3 class="text-4xl font-bold text-slate-900 mt-2">
                    {{ number_format($stats['total_posyandu']) }}
                </h3>
            </div>

            {{-- Pemeriksaan --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        📋
                    </div>

                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-50 text-blue-600">
                        Bulan Ini
                    </span>
                </div>

                <p class="text-sm text-slate-500 uppercase tracking-wide">
                    Pemeriksaan
                </p>

                <h3 class="text-4xl font-bold text-slate-900 mt-2">
                    {{ number_format($stats['total_pemeriksaan']) }}
                </h3>
            </div>

            {{-- Imunisasi --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                        💉
                    </div>

                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-sky-50 text-sky-600">
                        Bulan Ini
                    </span>
                </div>

                <p class="text-sm text-slate-500 uppercase tracking-wide">
                    Imunisasi
                </p>

                <h3 class="text-4xl font-bold text-slate-900 mt-2">
                    {{ number_format($stats['total_imunisasi']) }}
                </h3>
            </div>
        </div>

        {{-- Grafik + Info --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Chart --}}
            <div class="xl:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">
                            Aktivitas 6 Bulan Terakhir
                        </h2>
                        <p class="text-sm text-slate-500">
                            Berdasarkan jumlah pemeriksaan bulanan
                        </p>
                    </div>
                </div>

                <div class="h-[360px]">
                    <canvas id="dashboardChart"></canvas>
                </div>
            </div>

            {{-- Pengguna --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-800 mb-6">
                    Ringkasan Pengguna
                </h2>

                <div class="space-y-4">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Total Pengguna</p>
                        <p class="text-3xl font-bold text-slate-900 mt-1">
                            {{ number_format($stats['total_pengguna']) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-blue-50 p-4">
                        <p class="text-sm text-blue-600">Bidan</p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ number_format($stats['total_bidan']) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-emerald-50 p-4">
                        <p class="text-sm text-emerald-600">Kader</p>
                        <p class="text-3xl font-bold text-emerald-700 mt-1">
                            {{ number_format($stats['total_kader']) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Posyandu --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-800">
                    Sebaran & Performa Posyandu
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Rekap unit aktif di seluruh wilayah
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="text-left px-6 py-4">Nama Posyandu</th>
                            <th class="text-left px-6 py-4">Wilayah</th>
                            <th class="text-center px-6 py-4">Kader</th>
                            <th class="text-center px-6 py-4">Bidan</th>
                            <th class="text-center px-6 py-4">Pemeriksaan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($posyanduList as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    {{ $item['nama_posyandu'] }}
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $item['wilayah'] }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    {{ $item['total_kader'] }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    {{ $item['total_bidan'] }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    {{ $item['pemeriksaan_bulan'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-400">
                                    Belum ada data posyandu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Chart --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('dashboardChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        data: [
                            {{ $stats['total_pemeriksaan'] }},
                            {{ $stats['total_pemeriksaan'] }},
                            {{ $stats['total_pemeriksaan'] }},
                            {{ $stats['total_pemeriksaan'] }},
                            {{ $stats['total_pemeriksaan'] }},
                            {{ $stats['total_pemeriksaan'] }}
                        ],
                        borderRadius: 12,
                        barThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 5 }
                        }
                    }
                }
            });
        });
    </script>
@endsection