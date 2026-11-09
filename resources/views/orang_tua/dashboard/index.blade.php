@extends('layouts.orang_tua')

@section('title', 'Dashboard')

@section('content')
    @php
        $tanggalSekarang = now();
        $tanggalLahir = $anak?->tgl_lahir
            ? \Carbon\Carbon::parse($anak->tgl_lahir)
            : null;

        $usiaAnak = $tanggalLahir
            ? $tanggalLahir->diff($tanggalSekarang)
            : null;

        $jadwal = ($jadwalTerdekat ?? collect())->first();
        $pemeriksaan = $pemeriksaanTerakhir ?? null;

        $awalBulan = $tanggalSekarang->copy()->startOfMonth();
        $akhirBulan = $tanggalSekarang->copy()->endOfMonth();
        $jumlahHari = $akhirBulan->day;
        $hariPertama = $awalBulan->dayOfWeekIso; // Senin = 1

        $daftarJadwalPosyandu = $jadwalPosyanduBulanIni ?? collect();
        $daftarJadwalImunisasi = $jadwalImunisasiBulanIni ?? collect();
    @endphp

    <div class="space-y-7 pb-20">
        <section class="dashboard-welcome px-1 sm:px-2">
            <h1>
                Selamat Datang, Bunda!
            </h1>

            <p>
                {{ \Illuminate\Support\Str::title($anak?->nama_anak ?? 'Data anak belum tersedia') }}

                @if ($usiaAnak)
                    , {{ $usiaAnak->y }} Tahun {{ $usiaAnak->m }} Bulan
                @endif
            </p>
        </section>
        {{-- Data Pemeriksaan Terakhir --}}
        <section id="perkembangan-anak" class="scroll-mt-6 space-y-4">

            {{-- Judul section --}}
            <div class="dashboard-section-heading">
                <span class="dashboard-section-bar" aria-hidden="true"></span>

                <h2>Data Pemeriksaan Terakhir</h2>
            </div>

            {{-- Kartu pemeriksaan --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Berat badan --}}
                <article class="dashboard-metric-card">

                    <div class="min-w-0 flex-1">
                        <p class="dashboard-metric-label">
                            Berat Badan
                        </p>

                        <p class="dashboard-metric-value">
                            @if ($pemeriksaan?->berat_badan !== null)
                                {{ number_format((float) $pemeriksaan->berat_badan, 2, '.', '') }}
                                <span class="dashboard-metric-unit">KG</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <div class="dashboard-metric-icon">


                        <svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" viewBox="0 0 24 24" fill="none"
                            stroke="#155DFC" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">

                            <!-- Badan timbangan -->
                            <rect x="3.5" y="2.5" width="17" height="19" rx="3" />

                            <!-- Panel indikator analog -->
                            <path
                                d="M7 7.5
                                                                                                                                                     C7 5.6 9.2 4.7 12 4.7
                                                                                                                                                     C14.8 4.7 17 5.6 17 7.5
                                                                                                                                                     L15.8 10
                                                                                                                                                     C13.3 10.8 10.7 10.8 8.2 10
                                                                                                                                                     Z" />

                            <!-- Jarum penunjuk -->
                            <path d="M12 9.7 L10.2 6.8" />

                        </svg>

                    </div>
                </article>

                {{-- Tinggi badan --}}
                <article class="dashboard-metric-card">

                    <div class="min-w-0 flex-1">
                        <p class="dashboard-metric-label">
                            Tinggi Badan
                        </p>

                        <p class="dashboard-metric-value">
                            @if ($pemeriksaan?->tinggi_badan !== null)
                                {{ number_format((float) $pemeriksaan->tinggi_badan, 2, '.', '') }}
                                <span class="dashboard-metric-unit">CM</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <div class="dashboard-metric-icon">

                        <svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" viewBox="0 0 24 24" fill="none"
                            stroke="#155DFC" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                            aria-label="Ikon pengukur tinggi badan" role="img">
                            <path d="M8 2.5h8v19H8z" />
                            <path d="M8 6h5" />
                            <path d="M8 9h3" />
                            <path d="M8 12h5" />
                            <path d="M8 15h3" />
                            <path d="M8 18h5" />
                        </svg>
                    </div>
                </article>

                {{-- Lingkar kepala --}}
                <article class="dashboard-metric-card">

                    <div class="min-w-0 flex-1">
                        <p class="dashboard-metric-label">
                            Lingkar Kepala
                        </p>

                        <p class="dashboard-metric-value">
                            @if ($pemeriksaan?->lingkar_kepala !== null)
                                {{ number_format((float) $pemeriksaan->lingkar_kepala, 2, '.', '') }}
                                <span class="dashboard-metric-unit">CM</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <div class="dashboard-metric-icon">



                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 48 48" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M14.698 29.3103C13.3292 27.7399 11.0369 24.3692 11.0004 20.046C9.37497 19.8203 7.96011 19.5407 6.90689 19.1989C6.3859 19.0299 5.83306 18.8081 5.37048 18.4979C4.99757 18.2477 4.12207 17.5607 4.12207 16.3433C4.12207 15.126 4.99757 14.4389 5.37048 14.1888C5.83306 13.8785 6.3859 13.6568 6.90689 13.4877C7.9764 13.1407 9.41884 12.8577 11.076 12.6302C11.3012 12.5993 11.5318 12.5693 11.7677 12.54C11.1667 13.3346 10.6049 14.1069 10.2221 14.7813C9.28649 14.9362 8.48727 15.1072 7.84965 15.2907C7.8171 15.3001 7.78497 15.3095 7.75327 15.319C6.70615 15.6306 6.12207 15.9776 6.12207 16.3433C6.12207 16.7091 6.70615 17.0561 7.75327 17.3677C7.78497 17.3772 7.8171 17.3866 7.84965 17.3959C8.70174 17.6412 9.84241 17.864 11.2114 18.0561C11.5177 16.6423 12.1849 15.0602 13.3906 13.6681C15.2076 11.5703 18.1646 10 22.6704 10C24.9378 10 26.6467 10.2886 28.0399 10.8294C29.4444 11.3746 30.4647 12.1516 31.3788 13.0293C31.4485 13.0962 31.5201 13.1638 31.5929 13.2325C32.1852 13.7916 32.8549 14.4237 33.1923 15.3546C33.4972 16.1957 33.8824 17.2303 34.3005 18.3398C36.7656 18.0989 38.7655 17.7742 40.0793 17.3959C40.1119 17.3866 40.1441 17.377 40.1757 17.3677C41.2228 17.0561 41.8069 16.7091 41.8069 16.3433C41.8069 15.9776 41.2228 15.6306 40.1757 15.319C39.5093 15.1266 38.8102 14.9726 38 14.8311V17.5L34 14L38 10V12.8026C39.1771 12.9963 40.2074 13.2235 41.0221 13.4877C41.5431 13.6568 42.0959 13.8785 42.5585 14.1888C42.9314 14.4389 43.8069 15.126 43.8069 16.3433C43.8069 17.5607 42.9314 18.2477 42.5585 18.4979C42.0959 18.8081 41.5431 19.0299 41.0221 19.1989C39.9526 19.546 38.5101 19.829 36.8529 20.0564C36.2809 20.135 35.6733 20.2081 35.0342 20.2755C35.6697 21.9443 36.3181 23.6286 36.8371 24.9715C37.5146 26.7244 36.4363 28.5964 34.7018 28.9848V31.0186C34.7018 32.6754 33.3587 34.0186 31.7018 34.0186H28.7165V38H26.7165V32.0186H31.7018C32.2541 32.0186 32.7018 31.5709 32.7018 31.0186V27.0578H34.0354C34.7401 27.0578 35.2256 26.3499 34.9716 25.6925C34.399 24.2114 33.6665 22.3075 32.9663 20.4645C30.2948 20.6742 27.2219 20.7923 23.9645 20.7923C19.8785 20.7923 16.0831 20.6066 13.0058 20.2871C13.1321 24.0642 15.296 27.0269 16.4302 28.2459L16.698 28.5337V38H14.698V29.3103Z"
                                fill="#155DFC" />
                        </svg>


                    </div>
                </article>

            </div>
        </section>

        {{-- Styling khusus kalender dan jadwal --}}
        @push('styles')
            <style>
                .dashboard-schedule-grid {
                    display: grid;
                    grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
                    gap: 24px;
                    align-items: start;
                }

                .dashboard-calendar-weekdays,
                .dashboard-calendar-days {
                    display: grid;
                    grid-template-columns: repeat(7, minmax(0, 1fr));
                }

                .dashboard-calendar-day {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 0;
                    height: 40px;
                }

                .dashboard-calendar-number {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    border-radius: 9999px;
                    font-size: 13px;
                    transition: background-color .2s ease;
                }

                @media (max-width: 1023px) {
                    .dashboard-schedule-grid {
                        grid-template-columns: minmax(0, 1fr);
                    }
                }

                @media (max-width: 480px) {
                    .dashboard-calendar-day {
                        height: 36px;
                    }

                    .dashboard-calendar-number {
                        width: 29px;
                        height: 29px;
                        font-size: 12px;
                    }
                }


                /* Pastikan ikon pada empty state tidak membesar */
                .dashboard-empty-state-icon svg {
                    display: block !important;
                    width: 28px !important;
                    height: 28px !important;
                    min-width: 28px !important;
                    min-height: 28px !important;
                    max-width: 28px !important;
                    max-height: 28px !important;
                    flex: 0 0 28px !important;
                }

                /* Jarak judul ke panel, konsisten dengan ringkasan pemeriksaan */
                .dashboard-content-section {
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                    min-width: 0;
                }

                /* Jarak isi section yang memakai heading dengan subjudul */
                .dashboard-section-content {
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                    min-width: 0;
                    margin-top: 16px;
                }

                /* =========================================
                                   CHATBOT FLOATING BUTTON
                                ========================================= */

                .dashboard-chatbot-widget {
                    position: fixed;
                    right: max(24px, env(safe-area-inset-right));
                    bottom: max(24px, env(safe-area-inset-bottom));
                    z-index: 60;
                }

                .dashboard-chatbot-button {
                    position: relative;
                    display: flex;
                    width: 56px;
                    height: 56px;
                    align-items: center;
                    justify-content: center;
                    border: none;
                    border-radius: 50%;
                    background: #155DFC;
                    color: #FFFFFF;
                    cursor: pointer;
                    box-shadow: 0 4px 12px rgba(21, 93, 252, 0.20);
                    transition: background .2s ease, box-shadow .2s ease;
                }

                .dashboard-chatbot-button:hover {
                    background: #104BCB;
                    box-shadow: 0 6px 14px rgba(21, 93, 252, 0.24);
                }

                .dashboard-chatbot-button:focus-visible,
                .dashboard-chatbot-close:focus-visible {
                    outline: 3px solid #93C5FD;
                    outline-offset: 3px;
                }

                /* Indikator pada tombol chatbot dihapus karena layanan belum aktif. */

                /* =========================================
                                   CHATBOT PANEL
                                ========================================= */

                .dashboard-chatbot-panel {
                    position: absolute;
                    right: 0;
                    bottom: 68px;
                    display: none;
                    flex-direction: column;
                    width: min(360px, calc(100vw - 32px));
                    max-height: min(520px, calc(100dvh - 112px));
                    overflow: hidden;
                    border: 1px solid #E2E8F0;
                    border-radius: 18px;
                    background: #FFFFFF;
                    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14);
                    animation: chatbot-appear .2s ease-out;
                }

                .dashboard-chatbot-panel:not(.hidden) {
                    display: flex;
                }

                .dashboard-chatbot-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    padding: 16px;
                    background: #155DFC;
                    color: #FFFFFF;
                }

                .dashboard-chatbot-close {
                    display: flex;
                    width: 34px;
                    height: 34px;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                    border: none;
                    border-radius: 10px;
                    background: rgba(255, 255, 255, 0.15);
                    color: #FFFFFF;
                    cursor: pointer;
                }

                .dashboard-chatbot-close:hover {
                    background: rgba(255, 255, 255, 0.25);
                }

                .dashboard-chatbot-body {
                    flex: 1;
                    overflow-y: auto;
                    overscroll-behavior: contain;
                    -webkit-overflow-scrolling: touch;
                    padding: 16px;
                }

                .dashboard-chatbot-message {
                    padding: 14px;
                    border: 1px solid #DBEAFE;
                    border-radius: 14px;
                    background: #F8FAFF;
                }

                .dashboard-chatbot-footer {
                    padding: 12px 16px;
                    border-top: 1px solid #E2E8F0;
                    background: #FFFFFF;
                }

                @keyframes chatbot-appear {
                    from {
                        opacity: 0;
                        transform: translateY(8px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @media (max-width: 480px) {
                    .dashboard-chatbot-widget {
                        right: max(16px, env(safe-area-inset-right));
                        bottom: max(16px, env(safe-area-inset-bottom));
                    }

                    .dashboard-chatbot-button {
                        width: 54px;
                        height: 54px;
                    }

                    .dashboard-chatbot-panel {
                        bottom: 66px;
                        width: calc(100vw - 32px);
                        max-height: calc(100dvh - 96px);
                        border-radius: 16px;
                    }
                }

                @media (prefers-reduced-motion: reduce) {
                    .dashboard-chatbot-panel {
                        animation: none;
                    }

                    .dashboard-chatbot-button {
                        transition: none;
                    }
                }

                /* =========================================
                                   WELCOME SECTION
                                ========================================= */

                .dashboard-welcome h1 {
                    margin: 0;
                    color: #142D59;
                    font-size: clamp(24px, 2.4vw, 32px);
                    font-weight: 800;
                    line-height: 1.2;
                    letter-spacing: -0.8px;
                }

                .dashboard-welcome p {
                    margin-top: 8px;
                    color: #334B73;
                    font-size: clamp(16px, 1.3vw, 18px);
                    font-weight: 500;
                    line-height: 1.5;
                }

                /* =========================================
                                   SECTION HEADING
                                ========================================= */

                .dashboard-section-heading {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    min-width: 0;
                }

                .dashboard-section-bar {
                    display: block;
                    width: 8px;
                    height: 24px;
                    flex: 0 0 8px;
                    border-radius: 999px;
                    background: #155DFC;
                    box-shadow: 0 1px 3px rgba(21, 93, 252, 0.15);
                }

                .dashboard-section-heading > div {
                    min-width: 0;
                }

                .dashboard-section-heading h2 {
                    margin: 0;
                    color: #1E293B;
                    font-size: 20px;
                    font-weight: 800;
                    line-height: 1.3;
                    letter-spacing: -0.5px;
                }

                .dashboard-section-heading p {
                    margin-top: 4px;
                    color: #64748B;
                    font-size: 13px;
                    line-height: 20px;
                }

                /* =========================================
                                   METRIC CARDS
                                ========================================= */

                .dashboard-metric-card {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    min-width: 0;
                    min-height: 112px;
                    padding: 20px;
                    background: #FFFFFF;
                    border: 1px solid #E8EEF8;
                    border-radius: 20px;
                    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
                }

                .dashboard-metric-label {
                    margin: 0;
                    color: #64748B;
                    font-size: 11px;
                    font-weight: 700;
                    line-height: 16px;
                    letter-spacing: 1.2px;
                    text-transform: uppercase;
                }

                .dashboard-metric-value {
                    margin: 6px 0 0;
                    color: #1E293B;
                    font-size: 28px;
                    font-weight: 800;
                    line-height: 1.2;
                    white-space: nowrap;
                }

                .dashboard-metric-unit {
                    margin-left: 2px;
                    color: #64748B;
                    font-size: 14px;
                    font-weight: 700;
                    line-height: 1;
                    vertical-align: baseline;
                }

                .dashboard-metric-icon {
                    display: flex;
                    flex: 0 0 56px;
                    width: 56px;
                    height: 56px;
                    align-items: center;
                    justify-content: center;
                    border-radius: 16px;
                    background-color: #EFF6FF;
                    color: #155DFC;
                    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
                }

                @media (max-width: 640px) {
                    .dashboard-metric-card {
                        min-height: 100px;
                        padding: 16px;
                        border-radius: 16px;
                    }

                    .dashboard-metric-value {
                        font-size: 24px;
                    }

                    .dashboard-metric-icon {
                        flex: 0 0 48px;
                        width: 48px;
                        height: 48px;
                    }
                }

                /* =========================================
                       BOTTOM DASHBOARD GRID
                    ========================================= */

                .dashboard-bottom-grid {
                    display: grid;
                    grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
                    gap: 20px;
                    align-items: stretch;
                }

                .dashboard-bottom-card {
                    min-width: 0;
                    padding: 20px;
                    border: 1px solid #E8EEF8;
                    border-radius: 26px;
                    background: #FFFFFF;
                    box-shadow: 0 5px 16px rgba(15, 23, 42, 0.055);
                }

                @media (max-width: 1100px) {
                    .dashboard-bottom-grid {
                        grid-template-columns: minmax(0, 1fr);
                    }
                }

                @media (max-width: 640px) {
                    .dashboard-bottom-card {
                        padding: 16px;
                        border-radius: 20px;
                    }

                    .dashboard-section-heading h2 {
                        font-size: 18px;
                        line-height: 25px;
                    }
                }
            </style>

        @endpush

        {{-- Kalender dan jadwal terdekat --}}
        <div class="dashboard-schedule-grid">

            {{-- Kalender Kegiatan Posyandu --}}
            <section id="jadwal-posyandu" class="dashboard-content-section min-w-0">

                {{-- Judul --}}
                <div class="dashboard-section-heading">
                    <span class="dashboard-section-bar" aria-hidden="true"></span>

                    <h2>Kalender Kegiatan Posyandu</h2>
                </div>

                {{-- Panel kalender --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    {{-- Header bulan --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-4">
                        <div>
                            <p class="text-sm font-bold text-slate-700">
                                Jadwal Posyandu & Imunisasi
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Pantau tanggal kegiatan anak
                            </p>
                        </div>

                        <span
                            class="rounded-xl bg-[#EBF3FF] px-3 py-2
                                                                                                                                                                 text-sm font-bold text-[#155DFC]">
                            {{ $tanggalSekarang->translatedFormat('F Y') }}
                        </span>
                    </div>

                    {{-- Kalender --}}
                    <div class="mx-4 mb-4 overflow-hidden rounded-xl border border-slate-100">

                        {{-- Nama hari: selalu tujuh kolom --}}
                        <div
                            class="dashboard-calendar-weekdays bg-slate-50 px-1 py-3
                                                                                                                                                               text-center text-xs font-bold text-slate-500">

                            @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $hari)
                                <div class="min-w-0">
                                    {{ $hari }}
                                </div>
                            @endforeach
                        </div>

                        {{-- Tanggal --}}
                        <div class="dashboard-calendar-days gap-y-1 p-2">

                            {{-- Sel kosong sebelum tanggal 1 --}}
                            @for ($i = 1; $i < $hariPertama; $i++)
                                <div class="dashboard-calendar-day"></div>
                            @endfor

                            @for ($tanggal = 1; $tanggal <= $jumlahHari; $tanggal++)
                                                @php
                                                    $tanggalKalender = $awalBulan->copy()->day($tanggal);

                                                    $adalahHariIni = $tanggalKalender->isToday();

                                                    $adaJadwalPosyandu = $daftarJadwalPosyandu->contains(
                                                        function ($item) use ($tanggalKalender) {
                                                            return !empty($item->tgl_kegiatan)
                                                                && $tanggalKalender->isSameDay(
                                                                    \Carbon\Carbon::parse($item->tgl_kegiatan)
                                                                );
                                                        }
                                                    );

                                                    $adaJadwalImunisasi = $daftarJadwalImunisasi->contains(
                                                        function ($item) use ($tanggalKalender) {
                                                            return !empty($item->tanggal_jadwal)
                                                                && $tanggalKalender->isSameDay($item->tanggal_jadwal);
                                                        }
                                                    );

                                                    $adalahTanggalJadwal = $adaJadwalPosyandu || $adaJadwalImunisasi;
                                                @endphp

                                                <div class="dashboard-calendar-day">
                                                    <span title="{{ $tanggalKalender->translatedFormat('d F Y') }}" class="dashboard-calendar-number
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $adalahTanggalJadwal
                                ? 'bg-[#155DFC] font-bold text-white'
                                : ($adalahHariIni
                                    ? 'border-2 border-[#155DFC] font-bold text-[#155DFC]'
                                    : 'text-slate-600 hover:bg-slate-100') }}">

                                                        {{ $tanggal }}
                                                    </span>
                                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Keterangan warna --}}
                    <div
                        class="flex flex-wrap items-center gap-x-5 gap-y-2
                                                                                                                                                            border-t border-slate-100 px-4 py-3">

                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full border-2 border-[#155DFC]"></span>
                            <span class="text-xs text-slate-500">Hari ini</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#155DFC]"></span>
                            <span class="text-xs text-slate-500">Jadwal kegiatan</span>
                        </div>

                    </div>
                </div>
            </section>

            {{-- Jadwal Terdekat --}}
            <section id="jadwal-terdekat" class="dashboard-content-section min-w-0">

                {{-- Judul --}}
                <div class="dashboard-section-heading">
                    <span class="dashboard-section-bar" aria-hidden="true"></span>

                    <h2>Jadwal Terdekat</h2>
                </div>

                {{-- Konten jadwal --}}
                <div class="space-y-3">

                    @if ($jadwal)
                                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                                    <div class="flex items-start gap-4">

                                        {{-- Ikon jadwal --}}
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                rounded-2xl bg-[#EFF6FF] text-[#155DFC]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <rect x="3" y="5" width="18" height="16" rx="2" />
                                                <path d="M16 3v4M8 3v4M3 10h18" />
                                                <path d="m9 15 2 2 4-4" />
                                            </svg>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold uppercase tracking-wider text-[#155DFC]">
                                                Kegiatan Mendatang
                                            </p>

                                            <h3 class="mt-2 break-words text-base font-bold text-slate-800">
                                                {{ $jadwal->agenda ?? 'Jadwal Posyandu Mendatang' }}
                                            </h3>

                                            <div class="mt-3 flex items-start gap-2 text-sm text-slate-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <rect x="3" y="5" width="18" height="16" rx="2" />
                                                    <path d="M16 3v4M8 3v4M3 10h18" />
                                                </svg>

                                                <span>
                                                    {{ $jadwal->tgl_kegiatan
                        ? \Carbon\Carbon::parse($jadwal->tgl_kegiatan)
                            ->translatedFormat('d F Y')
                        : 'Tanggal belum tersedia' }}
                                                </span>
                                            </div>

                                            @if (!empty($jadwal->lokasi))
                                                <div class="mt-2 flex items-start gap-2 text-sm text-slate-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                                                        <circle cx="12" cy="10" r="2.5" />
                                                    </svg>

                                                    <span class="break-words">
                                                        {{ $jadwal->lokasi }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </article>

                    @else
                        {{-- Empty state: jadwal belum tersedia --}}

                        <div class="min-h-[210px] rounded-2xl border border-dashed
                                                                                                                                                                                                   border-slate-200 bg-slate-50 px-4 text-center"
                            style="box-sizing:border-box;
                                                                                                                                                                                                   display:flex;
                                                                                                                                                                                                   flex-direction:column;
                                                                                                                                                                                                   align-items:center;
                                                                                                                                                                                                   justify-content:flex-start;
                                                                                                                                                                                                   gap:12px;
                                                                                                                                                                                                   padding-top:20px;
                                                                                                                                                                                                   padding-bottom:16px;">

                            {{-- Ikon kalender --}}
                            <div
                                style="display:flex; align-items:center; justify-content:center;
                                                                                                                                                                                                                               width:52px; height:52px; flex-shrink:0;
                                                                                                                                                                                                                               border-radius:16px; background:#EFF6FF;">

                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
                                    stroke="#155DFC" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                                    style="display:block; flex-shrink:0;" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2.5" />
                                    <path d="M16 3v4M8 3v4M3 10h18" />
                                    <path d="M8 14h.01M12 14h.01M16 14h.01" />
                                </svg>
                            </div>

                            {{-- Judul --}}
                            <h3 class="text-sm font-bold text-slate-700" style="margin:0;">
                                Belum Ada Jadwal
                            </h3>

                            {{-- Deskripsi --}}
                            <p class="max-w-[260px] text-sm leading-6 text-slate-500" style="margin:0;">
                                Jadwal Posyandu terdekat belum tersedia.
                                Informasi kegiatan akan ditampilkan di sini
                                setelah jadwal tersedia.
                            </p>
                        </div>

                    @endif


                </div>
            </section>
        </div>

        {{-- Pemantauan Pertumbuhan Anak --}}
        <section id="pemantauan-pertumbuhan" class="scroll-mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">



            @php
                $tbU = data_get($hasilAnalisisPertumbuhan, 'indikator.TB_U', []);
                $bbU = data_get($hasilAnalisisPertumbuhan, 'indikator.BB_U', []);
                $bbPbTb = data_get($hasilAnalisisPertumbuhan, 'indikator.BB_PB_TB', []);
                $imtU = data_get($hasilAnalisisPertumbuhan, 'indikator.IMT_U', []);
                $lkU = data_get($hasilAnalisisPertumbuhan, 'indikator.LK_U', []);

                $ringkasan = [];

                // ==========================================
                // 1. PERTUMBUHAN TINGGI BADAN
                // ==========================================

                if (!data_get($tbU, 'tersedia', false)) {
                    $ringkasan[] = [
                        'judul' => 'Pertumbuhan tinggi badan',
                        'status' => 'Belum tersedia',
                        'deskripsi' => 'Data tinggi badan belum tersedia.',
                    ];
                } else {
                    $tbPerluPerhatian =
                        data_get($tbU, 'perlu_tinjau_ulang', false) ||
                        data_get($tbU, 'status_stunting') === true ||
                        data_get($tbU, 'kategori_berdasarkan_z_score') === 'Tinggi';

                    $ringkasan[] = [
                        'judul' => 'Pertumbuhan tinggi badan',
                        'status' => $tbPerluPerhatian
                            ? 'Perlu perhatian'
                            : 'Dalam rentang acuan',
                        'deskripsi' => $tbPerluPerhatian
                            ? 'Pertumbuhan tinggi badan perlu diperhatikan.'
                            : 'Tinggi badan sesuai rentang acuan usia.',
                    ];
                }

                // ==========================================
                // 2. KESESUAIAN BERAT BADAN
                // ==========================================

                $dataBeratLengkap =
                    data_get($bbU, 'tersedia', false) &&
                    data_get($bbPbTb, 'tersedia', false) &&
                    data_get($imtU, 'tersedia', false);

                if (!$dataBeratLengkap) {
                    $ringkasan[] = [
                        'judul' => 'Kesesuaian berat badan',
                        'status' => 'Belum tersedia',
                        'deskripsi' => 'Data berat dan tinggi badan belum lengkap.',
                    ];
                } else {
                    $bbPerluPerhatian =
                        data_get($bbU, 'perlu_tinjau_ulang', false) ||
                        data_get($bbPbTb, 'perlu_tinjau_ulang', false) ||
                        data_get($imtU, 'perlu_tinjau_ulang', false) ||

                        data_get($bbU, 'kategori_berdasarkan_z_score')
                        !== 'Berat badan normal' ||

                        data_get($bbPbTb, 'kategori_berdasarkan_z_score')
                        !== 'Gizi baik' ||

                        data_get($imtU, 'kategori_berdasarkan_z_score')
                        !== 'Gizi baik';

                    $ringkasan[] = [
                        'judul' => 'Kesesuaian berat badan',
                        'status' => $bbPerluPerhatian
                            ? 'Perlu perhatian'
                            : 'Dalam rentang acuan',
                        'deskripsi' => $bbPerluPerhatian
                            ? 'Kesesuaian berat badan perlu diperhatikan.'
                            : 'Berat badan sesuai rentang acuan usia dan tinggi badan.',
                    ];
                }

                // ==========================================
                // 3. LINGKAR KEPALA
                // ==========================================

                if (!data_get($lkU, 'tersedia', false)) {
                    $ringkasan[] = [
                        'judul' => 'Lingkar kepala',
                        'status' => 'Belum tersedia',
                        'deskripsi' => 'Data lingkar kepala belum tersedia.',
                    ];
                } else {
                    $lkPerluPerhatian =
                        data_get($lkU, 'perlu_tinjau_ulang', false) ||
                        data_get($lkU, 'interpretasi_berdasarkan_z_score')
                        !== 'Dalam rentang rujukan';

                    $ringkasan[] = [
                        'judul' => 'Lingkar kepala',
                        'status' => $lkPerluPerhatian
                            ? 'Perlu perhatian'
                            : 'Dalam rentang acuan',
                        'deskripsi' => $lkPerluPerhatian
                            ? 'Lingkar kepala perlu diperhatikan.'
                            : 'Lingkar kepala sesuai rentang acuan usia.',
                    ];
                }

                // ==========================================
                // STATUS KESELURUHAN
                // ==========================================

                $adaPerhatian = collect($ringkasan)->contains(
                    fn($item) => $item['status'] === 'Perlu perhatian'
                );

                $adaDataBelumTersedia = collect($ringkasan)->contains(
                    fn($item) => $item['status'] === 'Belum tersedia'
                );

                $statusKeseluruhan = $adaPerhatian
                    ? 'Ada hasil yang perlu diperhatikan'
                    : ($adaDataBelumTersedia
                        ? 'Pemantauan belum lengkap'
                        : 'Hasil pemantauan dalam rentang acuan');

                // ==========================================
                // REKOMENDASI TINDAK LANJUT
                // ==========================================

                if ($adaPerhatian) {
                    $judulRekomendasi = 'Saran untuk Bunda';

                    $isiRekomendasi = 'Diskusikan hasil ini dengan kader atau bidan Posyandu.';
                } elseif ($adaDataBelumTersedia) {
                    $judulRekomendasi = 'Lengkapi pemantauan anak';

                    $isiRekomendasi = 'Lengkapi data pemeriksaan bersama kader atau bidan.';
                } else {
                    $judulRekomendasi = 'Pertahankan pola pemantauan anak';

                    $isiRekomendasi = 'Lanjutkan pemantauan rutin dan pola makan bergizi seimbang.';
                }
            @endphp

            <div class="dashboard-section-heading">
                <span class="dashboard-section-bar" aria-hidden="true"></span>

                <div>
                    <h2>Pemantauan Pertumbuhan Anak</h2>
                    <p>Ringkasan hasil pemeriksaan pertumbuhan anak.</p>
                </div>
            </div>

            <div class="dashboard-section-content">
                {{-- Status keseluruhan --}}
                @if ($adaPerhatian)
                    <div class="flex items-start gap-2.5 rounded-xl border border-amber-100 bg-amber-50 p-4">

                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v5m0 3h.01" />
                        </svg>

                        <div>
                            <h3 class="text-sm font-bold text-amber-800">
                                {{ $statusKeseluruhan }}
                            </h3>

                            <p class="mt-0.5 text-sm leading-5 text-amber-700">
                                Beberapa hasil pemeriksaan perlu diperhatikan.
                            </p>
                        </div>
                    </div>

                @elseif ($adaDataBelumTersedia)
                    <div class="flex items-start gap-2.5 rounded-xl border border-slate-200 bg-slate-50 p-4">

                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-slate-500" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 11v5m0-8h.01" />
                        </svg>

                        <div>
                            <h3 class="text-sm font-bold text-slate-700">
                                {{ $statusKeseluruhan }}
                            </h3>

                            <p class="mt-0.5 text-sm leading-5 text-slate-500">
                                Beberapa data pemeriksaan belum lengkap.
                            </p>
                        </div>
                    </div>

                @else
                    <div class="flex items-start gap-2.5 rounded-xl border border-green-100 bg-green-50 p-4">

                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-green-600" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9" />
                            <path d="m8 12 2.5 2.5L16 9" />
                        </svg>

                        <div>
                            <h3 class="text-sm font-bold text-green-800">
                                {{ $statusKeseluruhan }}
                            </h3>

                            <p class="mt-0.5 text-sm leading-5 text-green-700">
                                Hasil pemeriksaan berada dalam rentang acuan.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Tiga ringkasan pertumbuhan --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

                    @foreach ($ringkasan as $item)

                        @php
                            $warna = match ($item['status']) {
                                'Perlu perhatian' => 'bg-amber-50 text-amber-700',
                                'Dalam rentang acuan' => 'bg-green-50 text-green-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp

                        <article class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-sm font-bold text-slate-800">
                                    {{ $item['judul'] }}
                                </h3>

                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $warna }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm leading-5 text-slate-500">
                                {{ $item['deskripsi'] }}
                            </p>
                        </article>

                    @endforeach

                </div>

                {{-- Rekomendasi --}}
                <div class="rounded-xl bg-[#F8FAFC] p-4">

                    <div class="flex items-start gap-2.5">

                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-[#155DFC]"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3v3m0 12v3M3 12h3m12 0h3" />
                            <circle cx="12" cy="12" r="7" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>

                        <div>
                            <h3 class="text-sm font-bold text-slate-700">
                                {{ $judulRekomendasi }}
                            </h3>

                            <p class="mt-0.5 text-sm leading-5 text-slate-500">
                                {{ $isiRekomendasi }}
                            </p>
                        </div>

                    </div>

                </div>

                    {{-- Tanggal pemeriksaan --}}
                    <div class="space-y-1">
                        @if ($pemeriksaanTerakhir?->tgl_periksa)
                            <p class="text-xs text-slate-400">
                                Pemeriksaan terakhir:
                                {{ \Carbon\Carbon::parse($pemeriksaanTerakhir->tgl_periksa)
                    ->translatedFormat('d F Y') }}
                            </p>
                        @endif

                        <p class="text-xs leading-5 text-slate-400">
                            Informasi ini merupakan hasil pemantauan berdasarkan data
                            pemeriksaan dan bukan diagnosis medis.
                        </p>
                    </div>
            </div>
        </section>


        {{-- Prediksi Risiko Stunting --}}
        <section id="prediksi-stunting" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            {{-- Judul section --}}
            <div class="dashboard-section-heading">
                <span class="dashboard-section-bar" aria-hidden="true"></span>

                <div>
                    <h2>Prediksi Risiko Stunting</h2>
                    <p>
                        Perkiraan kondisi pertumbuhan anak dalam 3 bulan mendatang.
                    </p>
                </div>
            </div>

            {{-- Kontrak hasil prediksi belum tersedia dari controller. --}}
            <div class="dashboard-section-content">
                <div class="rounded-xl bg-slate-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-200 text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="M12 8v4m0 4h.01" />
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                Belum tersedia
                            </span>

                            <h3 class="mt-2 text-sm font-bold text-slate-700">
                                Hasil prediksi belum tersedia
                            </h3>

                            <p class="mt-1.5 text-sm leading-5 text-slate-500">
                                Prediksi akan muncul setelah layanan prediksi
                                terintegrasi dan data pemeriksaan memenuhi
                                kebutuhan analisis.
                            </p>
                        </div>
                    </div>
                </div>

                <p class="text-xs leading-5 text-slate-400">
                    Prediksi bukan diagnosis medis.
                </p>
            </div>
        </section>
        {{-- Chatbot Posyandu-In --}}
        <div class="dashboard-chatbot-widget">

            {{-- Tombol mengambang --}}
            <button type="button" id="chatbot-toggle" class="dashboard-chatbot-button" aria-label="Buka chatbot"
                aria-expanded="false" aria-controls="chatbot-panel" title="Asisten Posyandu-In">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-[26px] w-[26px]" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path
                        d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8z" />
                    <path d="M8 11h8M8 14h5" />
                </svg>
            </button>

            {{-- Panel chatbot --}}
            <section id="chatbot-panel" class="dashboard-chatbot-panel hidden" role="dialog" aria-labelledby="chatbot-title"
                aria-modal="false">

                {{-- Header --}}
                <div class="dashboard-chatbot-header">

                    <div class="flex min-w-0 items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                        rounded-xl bg-white/15">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                                <circle cx="12" cy="12" r="7" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <h2 id="chatbot-title" class="text-sm font-bold">
                                Asisten Posyandu-In
                            </h2>

                            <p class="mt-0.5 text-xs text-blue-100">
                                Informasi kesehatan anak
                            </p>
                        </div>

                    </div>

                    {{-- Tombol tutup --}}
                    <button type="button" id="chatbot-close" class="dashboard-chatbot-close" aria-label="Tutup chatbot">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <path d="m18 6-12 12M6 6l12 12" />
                        </svg>
                    </button>

                </div>

                {{-- Isi percakapan --}}
                <div class="dashboard-chatbot-body">

                    <div class="dashboard-chatbot-message">

                        <div class="flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center
                                             rounded-lg bg-blue-100 text-[#155DFC]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                                    <circle cx="12" cy="12" r="7" />
                                </svg>
                            </span>

                            <p class="text-xs font-bold text-slate-700">
                                Asisten Posyandu-In
                            </p>
                        </div>

                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Halo, Bunda! Saya siap membantu memberikan informasi
                            seputar pertumbuhan anak, imunisasi, gizi, dan kegiatan
                            Posyandu.
                        </p>

                    </div>

                    <p class="mt-4 text-xs leading-5 text-slate-400">
                        Layanan percakapan sedang dalam tahap pengembangan.
                        Informasi ini belum diproses oleh layanan AI.
                    </p>

                </div>

                {{-- Footer --}}
                <div class="dashboard-chatbot-footer">
                    <p class="text-center text-xs text-slate-400">
                        Posyandu-In · Asisten Informasi Posyandu
                    </p>
                </div>

            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('chatbot-toggle');
            const panel = document.getElementById('chatbot-panel');
            const close = document.getElementById('chatbot-close');

            if (!toggle || !panel || !close) return;

            function bukaChatbot() {
                panel.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', 'Tutup chatbot');
                close.focus();
            }

            function tutupChatbot() {
                panel.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Buka chatbot');
                toggle.focus();
            }

            toggle.addEventListener('click', function () {
                const sedangTerbuka =
                    !panel.classList.contains('hidden');

                if (sedangTerbuka) {
                    tutupChatbot();
                } else {
                    bukaChatbot();
                }
            });

            close.addEventListener('click', tutupChatbot);

            document.addEventListener('keydown', function (event) {
                if (
                    event.key === 'Escape' &&
                    !panel.classList.contains('hidden')
                ) {
                    tutupChatbot();
                }
            });

            document.addEventListener('click', function (event) {
                const widget = document.querySelector(
                    '.dashboard-chatbot-widget'
                );

                if (
                    !widget.contains(event.target) &&
                    !panel.classList.contains('hidden')
                ) {
                    panel.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.setAttribute('aria-label', 'Buka chatbot');
                }
            });
        });
        </script>

@endsection
