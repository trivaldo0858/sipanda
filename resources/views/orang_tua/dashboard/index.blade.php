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
    @endphp

    <div class="space-y-7">




        {{-- Header Dashboard Orang Tua --}}
        <section class=" pl-5 pr-4">

            {{-- Sapaan --}}
            <h1 class="text-xl font-extrabold tracking-tight text-slate-800decoration-2 underline-offset-4">
                Selamat Datang, Bunda!
            </h1>

            {{-- Ringkasan nama dan usia anak --}}
            <p class="mt-5 text-sm font-semibold leading-5 text-slate-500">
                {{ \Illuminate\Support\Str::title($anak?->nama_anak ?? 'Data anak belum tersedia') }}

                @if ($usiaAnak)
                    , {{ $usiaAnak->y }} Tahun {{ $usiaAnak->m }} Bulan
                @endif
            </p>

        </section>




        {{-- Data Pemeriksaan Terakhir --}}
        <section id="perkembangan-anak" class="space-y-5">

            {{-- Judul section --}}
            <div class="flex items-center gap-3">
                <span aria-hidden="true"
                    style="display:block; flex:0 0 8px; width:8px; height:24px;
                                                                                                                           border-radius:9999px; background-color:#155DFC;
                                                                                                                           box-shadow:0 1px 2px #DBEAFE;">
                </span>

                <h1
                    style="margin:0; color:#1E293B; font-size:20px;
                                                                                                                           line-height:28px; font-weight:800; letter-spacing:-0.5px;">
                    Data pemeriksaan Terakhir
                </h1>
            </div>

            {{-- Kartu pemeriksaan --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3" style="padding-top:4px;">

                {{-- Berat badan --}}
                <article
                    style="display:flex; align-items:center; justify-content:space-between;
                                                                                                                           gap:16px; min-width:0; min-height:121px; padding:24px 28px;
                                                                                                                           background:#FFFFFF; border:1px solid #F8FAFC;
                                                                                                                           border-radius:40px;
                                                                                                                           box-shadow:0 8px 12px rgba(15,23,42,0.08);">

                    <div style="min-width:0; flex:1;">
                        <p
                            style="margin:0; color:#94A3B8; font-size:10px;
                                                                                                                                  font-weight:700; line-height:15px;
                                                                                                                                  letter-spacing:2px; text-transform:uppercase;">
                            Berat Badan
                        </p>

                        <p
                            style="margin:4px 0 0; color:#1E293B;
                                                                                                                                  font-size:24px; font-weight:800;
                                                                                                                                  line-height:36px; white-space:nowrap;">
                            @if ($pemeriksaan?->berat_badan !== null)
                                {{ number_format((float) $pemeriksaan->berat_badan, 2, '.', '') }}
                                <span style="font-size:24px; font-weight:800;">KG</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <div
                        style="display:flex; flex:0 0 64px; width:64px; height:64px;
                                                                                                                               align-items:center; justify-content:center;
                                                                                                                               border-radius:24px; background-color:#EFF6FF;
                                                                                                                               color:#155DFC;
                                                                                                                               box-shadow:0 1px 1px rgba(0,0,0,0.05);">


                        <svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" viewBox="0 0 24 24" fill="none"
                            stroke="#155DFC" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">

                            <!-- Badan timbangan -->
                            <rect x="3.5" y="2.5" width="17" height="19" rx="3" />

                            <!-- Panel indikator analog -->
                            <path d="M7 7.5
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
                <article
                    style="display:flex; align-items:center; justify-content:space-between;
                                                                                                                           gap:16px; min-width:0; min-height:121px; padding:24px 28px;
                                                                                                                           background:#FFFFFF; border:1px solid #F8FAFC;
                                                                                                                           border-radius:40px;
                                                                                                                           box-shadow:0 8px 12px rgba(15,23,42,0.08);">

                    <div style="min-width:0; flex:1;">
                        <p
                            style="margin:0; color:#94A3B8; font-size:10px;
                                                                                                                                  font-weight:700; line-height:15px;
                                                                                                                                  letter-spacing:2px; text-transform:uppercase;">
                            Tinggi Badan
                        </p>

                        <p
                            style="margin:4px 0 0; color:#1E293B;
                                                                                                                                  font-size:24px; font-weight:800;
                                                                                                                                  line-height:36px; white-space:nowrap;">
                            @if ($pemeriksaan?->tinggi_badan !== null)
                                {{ number_format((float) $pemeriksaan->tinggi_badan, 2, '.', '') }}
                                <span style="font-size:24px; font-weight:800;">CM</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <div
                        style="display:flex; flex:0 0 64px; width:64px; height:64px;
                                                                                                                               align-items:center; justify-content:center;
                                                                                                                               border-radius:24px; background-color:#EFF6FF;
                                                                                                                               color:#155DFC;
                                                                                                                               box-shadow:0 1px 1px rgba(0,0,0,0.05);">

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
                <article
                    style="display:flex; align-items:center; justify-content:space-between;
                                                                                                                           gap:16px; min-width:0; min-height:121px; padding:24px;
                                                                                                                           background:#FFFFFF; border:1px solid #F8FAFC;
                                                                                                                           border-radius:40px;
                                                                                                                           box-shadow:0 8px 12px rgba(15,23,42,0.08);">

                    <div style="min-width:0; flex:1;">
                        <p
                            style="margin:0; color:#94A3B8; font-size:10px;
                                                                                                                                  font-weight:700; line-height:15px;
                                                                                                                                  letter-spacing:2px; text-transform:uppercase;">
                            Lingkar Kepala
                        </p>

                        <p
                            style="margin:4px 0 0; color:#1E293B;
                                                                                                                                  font-size:24px; font-weight:800;
                                                                                                                                  line-height:36px; white-space:nowrap;">
                            @if ($pemeriksaan?->lingkar_kepala !== null)
                                {{ number_format((float) $pemeriksaan->lingkar_kepala, 2, '.', '') }}
                                <span style="font-size:24px; font-weight:800;">CM</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <div
                        style="display:flex; flex:0 0 64px; width:64px; height:64px;
                                                                                                                               align-items:center; justify-content:center;
                                                                                                                               border-radius:24px; background-color:#EFF6FF;
                                                                                                                               color:#155DFC;
                                                                                                                               box-shadow:0 1px 1px rgba(0,0,0,0.05);">



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
                    gap: 20px;
                    min-width: 0;
                }
            </style>
        @endpush

        {{-- Kalender dan jadwal terdekat --}}
        <div class="dashboard-schedule-grid">

            {{-- Kalender Kegiatan Posyandu --}}
            <section id="jadwal-posyandu" class="dashboard-content-section min-w-0">

                {{-- Judul --}}
                <div class="flex items-center gap-3">
                    <span aria-hidden="true"
                        style="display:block; width:8px; height:24px; flex-shrink:0;
                                                                                                       border-radius:9999px; background:#155DFC;">
                    </span>

                    <h2 class="text-xl font-extrabold tracking-tight text-slate-800">
                        Kalender Kegiatan Posyandu
                    </h2>
                </div>

                {{-- Panel kalender --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    {{-- Header bulan --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
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

                                                    $daftarJadwal = $jadwalTerdekat ?? collect();

                                                    $adalahTanggalJadwal = $daftarJadwal->contains(
                                                        function ($item) use ($tanggalKalender) {
                                                            return !empty($item->tgl_kegiatan)
                                                                && $tanggalKalender->isSameDay(
                                                                    \Carbon\Carbon::parse($item->tgl_kegiatan)
                                                                );
                                                        }
                                                    );
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
                                                                                                        border-t border-slate-100 px-5 py-3">

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
                <div class="flex items-center gap-3">
                    <span aria-hidden="true"
                        style="display:block; width:8px; height:24px; flex-shrink:0;
                                                                                                       border-radius:9999px; background:#155DFC;">
                    </span>

                    <h2 class="text-xl font-extrabold tracking-tight text-slate-800">
                        Jadwal Terdekat
                    </h2>
                </div>

                {{-- Konten jadwal --}}
                <div class="space-y-3">

                    @if ($jadwal)
                                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

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

                        <div class="min-h-[230px] rounded-2xl border border-dashed
                                                                                           border-slate-200 bg-slate-50 px-5 text-center"
                            style="box-sizing:border-box;
                                                                                           display:flex;
                                                                                           flex-direction:column;
                                                                                           align-items:center;
                                                                                           justify-content:flex-start;
                                                                                           gap:14px;
                                                                                           padding-top:24px;
                                                                                           padding-bottom:20px;">

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


        {{-- Bagian prediksi stunting --}}
        <section id="prediksi-stunting"
            class="scroll-mt-6 rounded-2xl border border-slate-200
                                                                                                                                                                            bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-700">
                        Pemantauan Risiko Stunting
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Informasi pemantauan pertumbuhan anak berdasarkan data pemeriksaan.
                    </p>
                </div>

                <span
                    class="rounded-full bg-[#EBF3FF] px-3 py-1.5
                                                                                                                                                                                 text-xs font-semibold text-[#155DFC]">
                    Hasil Pemantauan
                </span>
            </div>

            <p class="mt-4 text-sm text-slate-600">
                @if ($prediksiStunting)
                        {{ data_get($prediksiStunting, 'hasil_prediksi')
                    ?? data_get($prediksiStunting, 'kategori_risiko')
                    ?? 'Data prediksi tersedia, tetapi nama kolom hasil perlu disesuaikan.' }}
                @else
                    Hasil prediksi belum tersedia. Silakan menunggu pemeriksaan
                    dan pemrosesan data oleh sistem.
                @endif
            </p>
        </section>

    </div>

    {{-- Tombol chatbot mengambang --}}
    <div class="fixed bottom-6 right-6 z-50">
        <button type="button" id="chatbot-toggle" aria-label="Buka chatbot" aria-expanded="false"
            class="flex h-14 w-14 items-center justify-center
                                                                                                                                                                           rounded-full bg-[#155DFC] text-white
                                                                                                                                                                           shadow-lg shadow-blue-200 transition
                                                                                                                                                                           hover:bg-blue-700 hover:shadow-xl">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 10h8M8 14h5m-8 6 1.8-3.6A8 8 0 1119 17H9l-4 3z" />
            </svg>
        </button>

        {{-- Panel tampilan chatbot --}}
        <div id="chatbot-panel"
            class="absolute bottom-16 right-0 hidden w-80
                                                                                                                                                                        overflow-hidden rounded-2xl border border-slate-200
                                                                                                                                                                        bg-white shadow-xl">

            <div class="bg-[#155DFC] p-4 text-white">
                <p class="font-semibold">Chatbot Posyandu-In</p>
                <p class="mt-1 text-xs text-blue-100">
                    Asisten informasi tumbuh kembang anak
                </p>
            </div>

            <div class="p-4">
                <p class="text-sm leading-6 text-slate-600">
                    Halo, Bunda! Chatbot ini dapat digunakan untuk membantu
                    mencari informasi seputar pertumbuhan, pemeriksaan,
                    imunisasi, dan jadwal Posyandu.
                </p>

                <p class="mt-3 text-xs text-slate-400">
                    Fitur percakapan belum terhubung ke layanan chatbot.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('chatbot-toggle');
            const panel = document.getElementById('chatbot-panel');

            if (!toggle || !panel) return;

            toggle.addEventListener('click', function () {
                const akanDibuka = panel.classList.contains('hidden');

                panel.classList.toggle('hidden', !akanDibuka);
                toggle.setAttribute('aria-expanded', String(akanDibuka));
                toggle.setAttribute(
                    'aria-label',
                    akanDibuka ? 'Tutup chatbot' : 'Buka chatbot'
                );
            });
        });
    </script>
@endsection