@extends('layouts.superadmin')

@section('title', 'SIPANDA')

@section('content')
    <div class="space-y-8">
        {{-- 1. Statistik Utama (Stats Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <!-- Card Total Balita -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-card border border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Total Balita</p>
                    <h3 class="text-4xl font-extrabold text-slate-800">{{ $stats['total_anak'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-primary shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
            </div>

            <!-- Card Unit Posyandu -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-card border border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Unit Posyandu</p>
                    <h3 class="text-4xl font-extrabold text-slate-800">{{ $stats['total_posyandu'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-primary shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </div>
            </div>

            <!-- Card Total Bidan -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-card border border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Total Bidan</p>
                    <h3 class="text-4xl font-extrabold text-slate-800">{{ $stats['total_bidan'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-primary shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                    </svg>
                </div>
            </div>

        </div>

        {{-- Area Grafik Pertumbuhan (Satu Kolom Gabungan) --}}
        <div class="mt-8 bg-white p-10 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-50">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-2 h-6 bg-primary rounded-full shadow-sm shadow-blue-100"></span>
                        <h4 class="text-xl font-extrabold text-slate-800 tracking-tight">
                            Grafik Pertumbuhan Balita
                        </h4>
                    </div>
                </div>

                {{-- Dropdown Filter Modern dengan Ikon --}}
                <div class="relative group min-w-[220px]">
                    {{-- Elemen Select --}}
                    <select onchange="window.location.href='/superadmin?id_posyandu='+this.value"
                        class="w-full bg-[#EBF3FF] border border-blue-100 px-6 py-2.5 rounded-full text-xs font-bold text-[#0A63D8] shadow-sm outline-none appearance-none cursor-pointer hover:bg-blue-100 transition-all">
                        <option value="global" {{ $selectedPosyandu == 'global' ? 'selected' : '' }}>Grafik Global (Semua
                            Unit)</option>
                        @foreach($posyanduList as $p)
                            <option value="{{ $p->id_posyandu }}" {{ $selectedPosyandu == $p->id_posyandu ? 'selected' : '' }}>
                                {{ $p->nama_posyandu }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Ikon Dropdown Kustom --}}
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-[#0A63D8]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Canvas Grafik: Wadah ini akan selalu tampil --}}
            <div class="h-[400px] w-full relative">
                <canvas id="chartPertumbuhan"></canvas>

                {{-- Pesan opsional jika data benar-benar kosong --}}
                @if(empty($chartData['labels']))
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <p class="text-slate-300 font-medium text-sm text-center">
                            Belum ada data pemeriksaan.<br>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('chartPertumbuhan').getContext('2d');

                // Kita berikan data default kosong [] jika variabel dari PHP kosong
                const labels = @json($chartData['labels'] ?? []);
                const dataBB = @json($chartData['dataBB'] ?? []);
                const dataTB = @json($chartData['dataTB'] ?? []);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Tinggi Badan (Cm)',
                                data: dataTB,
                                borderColor: '#0A63D8',
                                backgroundColor: 'rgba(10, 99, 216, 0.05)',
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y1',
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: '#0A63D8'
                            },
                            {
                                label: 'Berat Badan (Kg)',
                                data: dataBB,
                                borderColor: '#d63384',
                                backgroundColor: 'rgba(214, 51, 132, 0.05)',
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y',
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: '#d63384'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: { display: true, text: 'Berat (Kg)', font: { weight: 'bold' } },
                                suggestedMax: 15, // Supaya kolom tetap terlihat meskipun data 0
                                beginAtZero: true
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: { display: true, text: 'Tinggi (Cm)', font: { weight: 'bold' } },
                                suggestedMax: 100, // Supaya kolom tetap terlihat meskipun data 0
                                grid: { drawOnChartArea: false },
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        @endpush
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false, // Penting agar tinggi tetap terjaga
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f8fafc' } },
                    x: { grid: { display: false } }
                }
            };

            // Render Grafik BB
            new Chart(document.getElementById('chartBB'), {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{ data: @json($chartData['dataBB']), borderColor: '#0A63D8', backgroundColor: 'rgba(10, 99, 216, 0.05)', fill: true, tension: 0.4 }]
                },
                options: commonOptions
            });

            // Render Grafik TB
            new Chart(document.getElementById('chartTB'), {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{ data: @json($chartData['dataTB']), borderColor: '#d63384', backgroundColor: 'rgba(214, 51, 132, 0.05)', fill: true, tension: 0.4 }]
                },
                options: commonOptions
            });
        </script>
    @endpush
@endsection