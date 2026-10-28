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



        {{-- Data Pemeriksaan Terakhir --}}
        <section id="perkembangan-anak" class="space-y-5">

            {{-- Judul section --}}
            <div class="flex items-center gap-3">
                <span aria-hidden="true" style="display:block; flex:0 0 8px; width:8px; height:24px;
                               border-radius:9999px; background-color:#155DFC;
                               box-shadow:0 1px 2px #DBEAFE;">
                </span>

                <h1 style="margin:0; color:#1E293B; font-size:20px;
                               line-height:28px; font-weight:800; letter-spacing:-0.5px;">
                    Data pemeriksaan Terakhir
                </h1>
            </div>

            {{-- Kartu pemeriksaan --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3" style="padding-top:4px; padding-bottom:40px;">

                {{-- Berat badan --}}
                <article style="display:flex; align-items:center; justify-content:space-between;
                               gap:16px; min-width:0; min-height:121px; padding:24px 28px;
                               background:#FFFFFF; border:1px solid #F8FAFC;
                               border-radius:40px;
                               box-shadow:0 8px 12px rgba(15,23,42,0.08);">

                    <div style="min-width:0; flex:1;">
                        <p style="margin:0; color:#94A3B8; font-size:10px;
                                      font-weight:700; line-height:15px;
                                      letter-spacing:2px; text-transform:uppercase;">
                            Berat Badan
                        </p>

                        <p style="margin:4px 0 0; color:#1E293B;
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

                    <div style="display:flex; flex:0 0 64px; width:64px; height:64px;
                                   align-items:center; justify-content:center;
                                   border-radius:24px; background-color:#EFF6FF;
                                   color:#155DFC;
                                   box-shadow:0 1px 1px rgba(0,0,0,0.05);">

                        <svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" viewBox="0 0 24 24" fill="none"
                            stroke="#155DFC" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"
                            aria-label="Ikon timbangan berat badan" role="img">
                            <path d="M8 3.5h8a3 3 0 0 1 3 3v1h1
                                         a1.5 1.5 0 0 1 1.49 1.69l-1.25 10.5
                                         A2.6 2.6 0 0 1 17.66 22H6.34
                                         a2.6 2.6 0 0 1-2.58-2.31L2.51 9.19
                                         A1.5 1.5 0 0 1 4 7.5h1v-1a3 3 0 0 1 3-3Z" />
                            <path d="M8 7.5a4 4 0 0 1 8 0" />
                            <path d="M10 12.5h4" />
                            <path d="M9 17h6" />
                        </svg>
                    </div>
                </article>

                {{-- Tinggi badan --}}
                <article style="display:flex; align-items:center; justify-content:space-between;
                               gap:16px; min-width:0; min-height:121px; padding:24px 28px;
                               background:#FFFFFF; border:1px solid #F8FAFC;
                               border-radius:40px;
                               box-shadow:0 8px 12px rgba(15,23,42,0.08);">

                    <div style="min-width:0; flex:1;">
                        <p style="margin:0; color:#94A3B8; font-size:10px;
                                      font-weight:700; line-height:15px;
                                      letter-spacing:2px; text-transform:uppercase;">
                            Tinggi Badan
                        </p>

                        <p style="margin:4px 0 0; color:#1E293B;
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

                    <div style="display:flex; flex:0 0 64px; width:64px; height:64px;
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
                <article style="display:flex; align-items:center; justify-content:space-between;
                               gap:16px; min-width:0; min-height:121px; padding:24px;
                               background:#FFFFFF; border:1px solid #F8FAFC;
                               border-radius:40px;
                               box-shadow:0 8px 12px rgba(15,23,42,0.08);">

                    <div style="min-width:0; flex:1;">
                        <p style="margin:0; color:#94A3B8; font-size:10px;
                                      font-weight:700; line-height:15px;
                                      letter-spacing:2px; text-transform:uppercase;">
                            Lingkar Kepala
                        </p>

                        <p style="margin:4px 0 0; color:#1E293B;
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

                    <div style="display:flex; flex:0 0 64px; width:64px; height:64px;
                                   align-items:center; justify-content:center;
                                   border-radius:24px; background-color:#EFF6FF;
                                   color:#155DFC;
                                   box-shadow:0 1px 1px rgba(0,0,0,0.05);">

                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24" fill="none"
                            stroke="#155DFC" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"
                            aria-label="Ikon lingkar kepala" role="img">
                            <path d="M4 9.5c1.5-2.7 4.5-4 8-4s6.5 1.3 8 4" />
                            <path d="M6 9.5v3.2a6 6 0 0 0 12 0V9.5" />
                            <path d="M3 9.5h18" />
                            <path d="M12 18.7v2" />
                        </svg>
                    </div>
                </article>

            </div>
        </section>

        {{-- Jadwal dan notifikasi --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-5">

            {{-- Kalender --}}
            <section id="jadwal-posyandu" class="rounded-2xl border border-slate-200 bg-white p-5
                                                                                    shadow-sm xl:col-span-3">

                <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-700">
                        Jadwal Posyandu & Imunisasi
                    </h2>

                    <span
                        class="rounded-lg bg-[#EBF3FF] px-3 py-1.5
                                                                                         text-xs font-semibold text-[#155DFC]">
                        {{ $tanggalSekarang->translatedFormat('F Y') }}
                    </span>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <div
                        class="grid grid-cols-7 bg-slate-50 py-3 text-center
                                                                                        text-xs font-semibold text-slate-500">
                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $hari)
                            <div>{{ $hari }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 gap-y-1 p-2">
                        @for ($i = 1; $i < $hariPertama; $i++)
                            <div class="flex h-10 items-center justify-center"></div>
                        @endfor

                        @for ($tanggal = 1; $tanggal <= $jumlahHari; $tanggal++)
                                        @php
                                            $tanggalKalender = $awalBulan->copy()->day($tanggal);
                                            $adalahHariIni = $tanggalKalender->isToday();

                                            $adalahTanggalJadwal = $jadwalTerdekat
                                                ->contains(function ($item) use ($tanggalKalender) {
                                                    return $item->tgl_kegiatan
                                                        && $tanggalKalender->isSameDay(
                                                            \Carbon\Carbon::parse($item->tgl_kegiatan)
                                                        );
                                                });
                                        @endphp

                                        <div class="flex h-10 items-center justify-center">
                                            <span class="flex h-8 w-8 items-center justify-center
                                                                                                                                                                                                                                                                                                                                            rounded-full text-sm
                                                                                                                                                                                                                                                                                                                                            {{ $adalahTanggalJadwal
                            ? 'bg-[#155DFC] font-bold text-white'
                            : ($adalahHariIni
                                ? 'border border-[#155DFC] font-semibold text-[#155DFC]'
                                : 'text-slate-600') }}">
                                                {{ $tanggal }}
                                            </span>
                                        </div>
                        @endfor
                    </div>
                </div>

                @if ($jadwal)
                        <div class="mt-4 rounded-xl bg-[#F6F9FF] p-4">
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $jadwal->agenda ?? 'Jadwal Posyandu Mendatang' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $jadwal->tgl_kegiatan
                    ? \Carbon\Carbon::parse($jadwal->tgl_kegiatan)->translatedFormat('d F Y')
                    : 'Tanggal belum tersedia' }}
                            </p>

                            @if (!empty($jadwal->lokasi))
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $jadwal->lokasi }}
                                </p>
                            @endif
                        </div>
                @else
                    <p class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-500">
                        Belum ada jadwal Posyandu terdekat.
                    </p>
                @endif
            </section>

            {{-- Notifikasi --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5
                                                                                    shadow-sm xl:col-span-2">
                <h2 class="mb-5 text-base font-bold text-slate-700">
                    Notifikasi Terbaru
                </h2>

                @if ($jadwal)
                        <div
                            class="flex items-start gap-3 rounded-xl border
                                                                                                                                                                                                                border-slate-100 p-4">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center
                                                                                                                                                                                                                    justify-center rounded-full bg-[#EBF3FF]
                                                                                                                                                                                                                    text-[#155DFC]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1M9 13h1m4 0h1M10 21v-4h4v4" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $jadwal->agenda ?? 'Jadwal Posyandu Mendatang' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $jadwal->tgl_kegiatan
                    ? \Carbon\Carbon::parse($jadwal->tgl_kegiatan)->translatedFormat('d F Y')
                    : 'Tanggal belum tersedia' }}
                                </p>
                            </div>
                        </div>
                @else
                    <div class="rounded-xl bg-slate-50 p-5 text-center">
                        <p class="text-sm text-slate-500">
                            Belum ada notifikasi terbaru.
                        </p>
                    </div>
                @endif

                {{-- Pintasan hasil prediksi --}}
                <a href="#prediksi-stunting"
                    class="mt-4 flex items-center justify-between rounded-xl
                                                                                  border border-slate-100 p-4 transition
                                                                                  hover:border-blue-200 hover:bg-[#F6F9FF]">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">
                            Pemantauan Risiko Stunting
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Lihat informasi hasil prediksi anak
                        </p>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#155DFC]" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                    </svg>
                </a>
            </section>
        </div>

        {{-- Bagian prediksi stunting --}}
        <section id="prediksi-stunting" class="scroll-mt-6 rounded-2xl border border-slate-200
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

                <span class="rounded-full bg-[#EBF3FF] px-3 py-1.5
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
        <button type="button" id="chatbot-toggle" aria-label="Buka chatbot" aria-expanded="false" class="flex h-14 w-14 items-center justify-center
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
        <div id="chatbot-panel" class="absolute bottom-16 right-0 hidden w-80
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