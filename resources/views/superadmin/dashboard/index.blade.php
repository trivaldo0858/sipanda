@extends('layouts.superadmin')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="dashboard-wrapper">
        <div class="stats-grid">
            <div class="card-figma">
                <div class="icon-container" style="background:#EFF6FF; color:#2563EB;">
                    <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <p class="stat-label">Total Balita</p>
                <span class="stat-value">{{ number_format($stats['total_anak'] ?? 0) }}</span>
            </div>

            <div class="card-figma">
                <div class="icon-container" style="background:#FFF1F2; color:#E11D48;">
                    <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <p class="stat-label">Total Unit Posyandu</p>
                <span class="stat-value">{{ $stats['total_posyandu'] ?? 0 }}</span>
            </div>

            <div class="card-figma">
                <div class="icon-container" style="background:#F0FDF4; color:#16A34A;">
                    <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="stat-label">Total Staf/Bidan</p>
                <span class="stat-value">{{ $stats['total_staf'] ?? 0 }}</span>
            </div>
        </div>

        <div class="chart-container-card" id="chart-wrapper"
            data-chart="{{ json_encode($chart_data ?? [45, 52, 48, 70, 65, 80]) }}">
            <h3 style="font-size:18px; font-weight:900; margin-bottom:20px;">Statistik Pertumbuhan Anak</h3>
            <div style="height: 350px;">
                <canvas id="mainDashboardChart"></canvas>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('mainDashboardChart').getContext('2d');

            // Mengambil data dari atribut HTML agar VS Code tidak merah
            const chartWrapper = document.getElementById('chart-wrapper');
            const dataGizi = JSON.parse(chartWrapper.getAttribute('data-chart'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Balita Sehat',
                        data: dataGizi,
                        backgroundColor: '#2563EB',
                        borderRadius: 12,
                        barThickness: 45,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: '#F1F5F9' }, ticks: { color: '#94A3B8', font: { weight: '600' } } },
                        x: { grid: { display: false }, ticks: { color: '#64748B', font: { weight: '700' } } }
                    }
                }
            });
        });
    </script>
@endsection