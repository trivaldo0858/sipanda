@extends('layouts.superadmin')

@section('title', 'Dashboard Super Admin')

@section('content')
    <div class="space-y-8">
        {{-- 1. Statistik Utama (Stats Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl3 border border-line shadow-card">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Total Balita</p>
                <h2 class="text-3xl font-bold text-slate-800 mt-2">{{ $stats['total_anak'] }}</h2>
            </div>
            <div class="bg-white p-6 rounded-xl3 border border-line shadow-card">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Unit Posyandu</p>
                <h2 class="text-3xl font-bold text-slate-800 mt-2">{{ $stats['total_posyandu'] }}</h2>
            </div>
            <div class="bg-white p-6 rounded-xl3 border border-line shadow-card">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Total Bidan</p>
                <h2 class="text-3xl font-bold text-slate-800 mt-2">{{ $stats['total_bidan'] }}</h2>
            </div>
        </div>

        {{-- 2. Area Grafik Pertumbuhan --}}
        <div class="bg-white p-8 rounded-xl3 border border-line shadow-card">
            <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-primary rounded-full"></span>
                Grafik Rata-rata Pertumbuhan Balita Global
            </h3>
            <div class="h-[400px] w-full">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Memanggil Chart.js via CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const ctx = document.getElementById('growthChart').getContext('2d');

            // Mengonversi data dari Laravel (PHP) ke JavaScript
            const labels = @json($chartData['labels']);
            const dataValues = @json($chartData['data']);

            new Chart(ctx, {
                type: 'line',
                data: {
                    // Logika: Jika data kosong, tampilkan label "Belum Ada Data"
                    labels: labels.length > 0 ? labels : ['Belum Ada Data'],
                    datasets: [{
                        label: 'Rata-rata Berat Badan (kg)',
                        data: dataValues.length > 0 ? dataValues : [0],
                        borderColor: '#0A63D8',
                        backgroundColor: 'rgba(10, 99, 216, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Berat (kg)' }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection