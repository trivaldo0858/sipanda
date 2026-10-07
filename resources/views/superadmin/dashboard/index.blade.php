@extends('layouts.superadmin')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

@section('title', 'Posyandu-In | Dashboard')

@section('content')
    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div
                class="bg-white p-7 rounded-[2.5rem] shadow-[0_8px_24px_rgba(15,23,42,0.08)] border border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Total Balita</p>
                    <h3 class="text-3xl font-extrabold text-slate-800">{{ $stats['total_anak'] }}</h3>
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

            <div
                class="bg-white p-7 rounded-[2.5rem] shadow-[0_8px_24px_rgba(15,23,42,0.08)] border border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Unit Posyandu</p>
                    <h3 class="text-3xl font-extrabold text-slate-800">{{ $stats['total_posyandu'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-primary shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-[2.5rem] shadow-[0_8px_24px_rgba(15,23,42,0.08)] border border-slate-50 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Total Bidan</p>
                    <h3 class="text-3xl font-extrabold text-slate-800">{{ $stats['total_bidan'] }}</h3>
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

        <div class="mt-6 bg-white p-6 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-50">

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">

                <div>
                    <div class="flex items-center gap-3 mb-2">

                        <span class="w-2 h-6 bg-primary rounded-full shadow-sm shadow-blue-100"></span>

                        <h4 class="text-xl font-extrabold text-slate-800 tracking-tight">
                            Peta Sebaran Posyandu
                        </h4>

                    </div>
                </div>

            </div>


            <div class="w-full relative">

                <div id="map" class="w-full h-[500px] rounded-3xl overflow-hidden">
                </div>

            </div>

        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('chartPertumbuhan').getContext('2d');

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


    <style>
        .custom-marker {
            background: transparent;
            border: none;
        }

        .marker-wrapper {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .marker-pin {
            width: 32px;
            height: 32px;
            background: #155DFC;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.20);
            color: white;
            font-size: 19px;
            font-weight: bold;
        }

        .marker-pin span {
            line-height: 1;
        }

        .leaflet-marker-icon.marker-active {
            filter: drop-shadow(0 0 8px rgba(21, 93, 252, 0.45));
        }

        .posyandu-label {
            background: white !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
            color: #334155 !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08) !important;
            white-space: nowrap;
        }

        .posyandu-label::before {
            display: none !important;
        }

        .custom-marker {
            background: transparent !important;
            border: none !important;
        }

        .marker-circle {
            width: 34px;
            height: 34px;
            background: #155DFC;
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.20);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .marker-circle svg {
            width: 18px;
            height: 18px;
            stroke: white;
        }

        .custom-marker.marker-active .marker-circle {
            box-shadow:
                0 0 0 8px rgba(21, 93, 252, 0.18),
                0 3px 10px rgba(15, 23, 42, 0.20);
            transform: scale(1.05);
        }

        .posyandu-popup {
            min-width: 220px;
        }

        .popup-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .popup-info {
            font-size: 13px;
            line-height: 1.6;
            color: #64748b;
            margin-bottom: 12px;
        }

        .popup-detail-button {
            display: block;
            text-align: center;
            background: #155DFC;
            color: white !important;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none !important;
            font-size: 13px;
            font-weight: 600;
        }

        .popup-detail-button:hover {
            background: #0f4bd8;
        }

        /* ========================================
                                                                           POPUP POSYANDU
                                                                        ======================================== */

        .leaflet-popup-content-wrapper {
            padding: 0 !important;
            border-radius: 16px !important;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14) !important;
        }

        .leaflet-popup-content {
            margin: 0 !important;
            width: 300px !important;
        }

        .leaflet-popup-tip {
            box-shadow: none !important;
        }

        /* Container */
        .posyandu-popup {
            width: 300px;
            padding: 16px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            color: #1e293b;
        }

        /* ========================================
                                                                           HEADER
                                                                        ======================================== */

        .popup-title {
            display: flex;
            align-items: center;
            gap: 7px;

            padding-right: 20px;

            font-size: 15px;
            line-height: 20px;
            font-weight: 700;
            color: #0f172a;
        }

        .popup-dot {
            width: 8px;
            height: 8px;

            flex: 0 0 auto;

            background: #155DFC;
            border-radius: 50%;
        }

        .popup-location {
            margin-top: 3px;
            margin-left: 15px;

            font-size: 11px;
            line-height: 16px;

            color: #64748b;
        }

        /* ========================================
                                                                           ALAMAT
                                                                        ======================================== */

        .popup-address {
            margin-top: 13px;
            padding: 10px 11px;

            background: #f5f3ff;
            border-radius: 8px;
        }

        .popup-label {
            display: flex;
            align-items: center;
            gap: 5px;

            margin-bottom: 4px;

            font-size: 8px;
            line-height: 12px;
            font-weight: 700;
            letter-spacing: 0.35px;

            color: #64748b;
        }

        .popup-label svg {
            color: #64748b;
            flex-shrink: 0;
        }

        .popup-address-text {
            font-size: 11px;
            line-height: 16px;
            color: #334155;
        }

        /* ========================================
                                                                           BALITA
                                                                        ======================================== */

        .popup-balita {
            display: flex;
            align-items: center;
            gap: 10px;

            margin-top: 12px;
            padding: 10px 13px;

            background: #ffffff;

            border: 1px solid #dbe5ff;
            border-radius: 8px;
        }

        .popup-icon {
            width: 32px;
            height: 32px;

            flex: 0 0 auto;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #eff4ff;
            border-radius: 7px;

            color: #155DFC;
        }

        .popup-icon svg {
            display: block;
        }

        .popup-small-label {
            margin-bottom: 1px;

            font-size: 10px;
            line-height: 14px;

            color: #64748b;
        }

        .popup-balita-number {
            font-size: 12px;
            line-height: 17px;
            font-weight: 700;

            color: #155DFC;
        }

        /* ========================================
                                                                           BIDAN
                                                                        ======================================== */

        .popup-bidan {
            display: flex;
            align-items: center;
            gap: 10px;

            margin-top: 10px;
            padding: 10px 11px;
            background: #ffffff;
            border: 1px solid #dbe5ff;
            border-radius: 8px;
        }

        .popup-bidan-name {
            font-size: 11px;
            line-height: 16px;
            font-weight: 600;

            color: #155DFC;

        }

        /* ========================================
                                                                           BUTTON
                                                                        ======================================== */

        .popup-detail-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 36px;
            margin-top: 12px;
            padding: 8px 12px;
            box-sizing: border-box;
            background: #155DFC;
            color: #ffffff !important;
            border-radius: 8px;
            font-size: 11px;
            line-height: 17px;
            font-weight: 600;
            text-decoration: none !important;
            transition: background 0.2s ease;
        }

        .popup-detail-button:hover {
            background: #0f4bd8;
        }

        /* ========================================
                                                                           CLOSE BUTTON
                                                                        ======================================== */

        .leaflet-popup-close-button {
            top: 10px !important;
            right: 10px !important;

            width: 24px !important;
            height: 24px !important;

            color: #64748b !important;

            font-size: 20px !important;
            font-weight: 400 !important;

            line-height: 24px !important;
        }
    </style>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const posyanduData = @json($posyanduMap);

        const map = L.map('map').setView([-6.3276, 108.3247], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const posyanduIcon = L.divIcon({
            className: 'custom-marker',
            html: `
                                                                                                                                                                                            <div class="marker-circle">
                                                                                                                                                                                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                                                                                                                                                                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                                                                                                                                                                                    <circle cx="12" cy="10" r="2.5"/>
                                                                                                                                                                                                </svg>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        `,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20]
        });
        posyanduData.forEach(posyandu => {

            if (posyandu.latitude === null || posyandu.longitude === null) {
                return;
            }

            const marker = L.marker(
                [
                    parseFloat(posyandu.latitude),
                    parseFloat(posyandu.longitude)
                ],
                {
                    icon: posyanduIcon
                }
            ).addTo(map);

            marker.bindTooltip(posyandu.nama_posyandu, {
                permanent: true,
                direction: 'bottom',
                offset: [0, 8],
                className: 'posyandu-label'
            });

            marker.bindPopup(`
                                                                        <div class="posyandu-popup">

                                                                            <div class="popup-title">
                                                                                <span class="popup-dot"></span>
                                                                                ${posyandu.nama_posyandu}
                                                                            </div>

                                                                            <div class="popup-location">
                                                                                ${posyandu.desa_kelurahan}, Kec. ${posyandu.kecamatan}
                                                                            </div>

                                                                            <div class="popup-address">

                                                                                <div class="popup-label">
                                                                                    <svg
                                                                                        width="12"
                                                                                        height="12"
                                                                                        viewBox="0 0 24 24"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                    >
                                                                                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                                                                        <circle cx="12" cy="10" r="2.5"></circle>
                                                                                    </svg>

                                                                                    <span>ALAMAT POSYANDU</span>
                                                                                </div>

                                                                                <div class="popup-address-text">
                                                                                    ${posyandu.alamat}
                                                                                </div>

                                                                            </div>

                                                                            <div class="popup-balita">

                                                                                <div class="popup-icon">
                                                                                    <svg
                                                                                        width="18"
                                                                                        height="18"
                                                                                        viewBox="0 0 24 24"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                    >
                                                                                        <circle cx="12" cy="8" r="3"></circle>
                                                                                        <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6"></path>
                                                                                    </svg>
                                                                                </div>

                                                                                <div>
                                                                                    <div class="popup-small-label">
                                                                                        Balita Terdaftar
                                                                                    </div>

                                                                                    <div class="popup-balita-number">
                                                                                        ${posyandu.total_balita} Balita
                                                                                    </div>
                                                                                </div>

                                                                            </div>

                                                                            <div class="popup-bidan">

                                                                                <div class="popup-icon">
                                                                                    <svg
                                                                                        width="18"
                                                                                        height="18"
                                                                                        viewBox="0 0 24 24"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                    >
                                                                                        <circle cx="12" cy="8" r="3"></circle>
                                                                                        <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6"></path>
                                                                                    </svg>
                                                                                </div>

                                                                                <div>
                                                                                    <div class="popup-small-label">
                                                                                        Bidan Pendamping
                                                                                    </div>

                                                                                    <div class="popup-bidan-name">
                                                                                        ${posyandu.bidan?.[0]?.nama_bidan ?? 'Belum ditentukan'}
                                                                                    </div>
                                                                                </div>

                                                                            </div>

                                                                            <a
                                                                                href="/superadmin/posyandu/${posyandu.id_posyandu}"
                                                                                class="popup-detail-button"
                                                                            >
                                                                                Lihat detail Posyandu
                                                                            </a>

                                                                        </div>
                                                                    `, {
                closeButton: true,
                closeOnClick: false,
                autoClose: true,
                autoPan: true
            });

        });

        marker.bindTooltip('Posyandu Melati', {
            permanent: true,
            direction: 'bottom',
            offset: [0, 8],
            className: 'posyandu-label'
        });

    </script>
@endsection