@extends('layouts.superadmin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-wrapper">
    <div class="stats-grid">
        <div class="card-figma">
            <div class="icon-container" style="background:#EFF6FF; color:#2563EB;">
                <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <p class="stat-label">Total Balita</p>
            <span class="stat-value">{{ number_format($total_balita ?? 0) }}</span>
        </div>

        <div class="card-figma">
            <div class="icon-container" style="background:#FFF1F2; color:#E11D48;">
                <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
            </div>
            <p class="stat-label">Total Wilayah</p>
            <span class="stat-value">{{ $total_wilayah ?? 0 }}</span>
        </div>

        <div class="card-figma">
            <div class="icon-container" style="background:#F0FDF4; color:#16A34A;">
                <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
            </div>
            <p class="stat-label">Total Imunisasi</p>
            <span class="stat-value">{{ $total_imunisasi ?? 0 }}</span>
        </div>

        <div class="card-figma">
            <div class="icon-container" style="background:#FFFBEB; color:#D97706;">
                <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </div>
            <p class="stat-label">Rata-rata Berat</p>
            <span class="stat-value">{{ $tren_berat ?? 0 }} <small style="font-size:14px; color:#94A3B8;">kg</small></span>
        </div>
    </div>

    <div class="chart-container-card" id="chart-wrapper" data-chart="{{ json_encode($chart_data ?? [45, 52, 48, 70, 65, 80]) }}">
        <h3 style="font-size:18px; font-weight:900; margin-bottom:20px;">Statistik Pertumbuhan Anak</h3>
        <div style="height: 350px;">
            <canvas id="mainDashboardChart"></canvas>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                    y: { grid: { color: '#F1F5F9' }, ticks: { color: '#94A3B8', font: {weight:'600'} } },
                    x: { grid: { display: false }, ticks: { color: '#64748B', font: {weight:'700'} } }
                }
            }
        });
    });
</script>
@endsection