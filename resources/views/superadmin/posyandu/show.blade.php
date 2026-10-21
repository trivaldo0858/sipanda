@extends('layouts.superadmin')

@section('title', 'Detail Posyandu')

@section('content')
    @php
        /*
         * Prioritaskan parameter ?from=map atau ?from=list.
         * Jika parameter tidak ada, gunakan halaman sebelumnya sebagai petunjuk.
         */
        $previousPath = parse_url(url()->previous(), PHP_URL_PATH) ?? '';
        $fromMap = request()->query('from') === 'map'
            || (request()->query('from') !== 'list'
                && str_contains($previousPath, '/superadmin/dashboard'));

        $backUrl = $fromMap
            ? route('superadmin.dashboard')
            : route('superadmin.posyandu.index');

        $backLabel = $fromMap
            ? 'Kembali ke Peta'
            : 'Kembali ke Daftar';
    @endphp

    <div class="space-y-5">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Informasi Posyandu</h1>
                <p class="mt-1 text-sm text-slate-500">Detail informasi dan lokasi Posyandu.</p>
            </div>

            <a href="{{ $backUrl }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5"></path>
                    <path d="m12 19-7-7 7-7"></path>
                </svg>
                {{ $backLabel }}
            </a>
        </div>

        {{-- Identitas Posyandu --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-start gap-3">
                <span class="mt-1.5 h-3 w-3 shrink-0 rounded-full bg-[#155DFC]"></span>
                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-slate-800 sm:text-xl">{{ $posyandu->nama_posyandu }}</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Desa {{ $posyandu->desa_kelurahan }},
                        Kecamatan {{ $posyandu->kecamatan }},
                        Kabupaten {{ $posyandu->kabupaten_kota ?? 'Indramayu' }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#155DFC]">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="8" r="3"></circle>
                            <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Total Anak</p>
                        <p class="mt-1 text-2xl font-bold text-slate-800">
                            {{ $posyandu->anak_count ?? $posyandu->total_balita ?? 0 }}</p>
                        <p class="text-xs text-slate-400">Anak terdaftar</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#155DFC]">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="2.5"></circle>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Wilayah</p>
                        <p class="mt-1 text-lg font-bold text-slate-800">{{ $posyandu->desa_kelurahan }}</p>
                        <p class="text-xs text-slate-400">Kecamatan {{ $posyandu->kecamatan }}</p>
                    </div>
                </div>
            </section>
        </div>

        {{-- Informasi Posyandu --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-800 sm:text-lg">Informasi Posyandu</h2>
                <p class="mt-1 text-sm text-slate-500">Informasi umum mengenai Posyandu.</p>
            </div>

            <div class="grid grid-cols-1 gap-x-8 gap-y-5 p-5 sm:grid-cols-2 sm:p-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nama Posyandu</p>
                    <p class="mt-1.5 text-sm font-semibold text-slate-700">{{ $posyandu->nama_posyandu }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Desa / Kelurahan</p>
                    <p class="mt-1.5 text-sm font-semibold text-slate-700">{{ $posyandu->desa_kelurahan }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kecamatan</p>
                    <p class="mt-1.5 text-sm font-semibold text-slate-700">{{ $posyandu->kecamatan }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kabupaten</p>
                    <p class="mt-1.5 text-sm font-semibold text-slate-700">{{ $posyandu->kabupaten_kota ?? 'Indramayu' }}
                    </p>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat Lengkap</p>
                    <p class="mt-1.5 text-sm leading-6 text-slate-700">{{ $posyandu->alamat ?: '-' }}</p>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Bidan Pendamping</p>
                    <div class="mt-2 flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-[#155DFC]">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="8" r="3"></circle>
                                <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6"></path>
                            </svg>
                        </div>
                        <span
                            class="text-sm font-semibold text-slate-700">{{ $posyandu->bidan->first()?->nama_bidan ?? 'Belum ditentukan' }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Akun Akses Posyandu --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-800 sm:text-lg">Akun Akses Posyandu</h2>
                <p class="mt-1 text-sm text-slate-500">Informasi akun yang digunakan untuk mengakses sistem sebagai Kader
                    Posyandu.</p>
            </div>

            <div class="p-5 sm:p-6">
                @if ($akunAkses)
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#155DFC]">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M5 21v-2a7 7 0 0 1 14 0v2"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Username Login</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $akunAkses->username }}</p>
                                <p class="mt-1 text-xs text-slate-500">Akun terhubung dengan Posyandu ini</p>
                            </div>
                        </div>

                        <span
                            class="inline-flex w-fit items-center gap-2 rounded-full bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            Akun Terdaftar
                        </span>
                    </div>
                @else
                    <div class="flex items-start gap-3 rounded-xl bg-amber-50 p-4">
                        <svg class="mt-0.5 shrink-0 text-amber-600" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 8v4"></path>
                            <path d="M12 16h.01"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Akun akses belum ditemukan</p>
                            <p class="mt-1 text-sm leading-5 text-amber-700">Belum ada akun dengan role Posyandu yang terhubung
                                ke unit ini.</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Lokasi Posyandu --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-800 sm:text-lg">Lokasi Posyandu</h2>
                <p class="mt-1 text-sm text-slate-500">Lokasi Posyandu berdasarkan koordinat GIS.</p>
            </div>

            <div class="p-5 sm:p-6">
                <div id="detail-map" class="h-[320px] w-full overflow-hidden rounded-xl bg-slate-50 sm:h-[420px]"></div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Latitude</p>
                        <p class="mt-1.5 break-all font-mono text-sm font-semibold text-slate-700">
                            {{ $posyandu->latitude ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Longitude</p>
                        <p class="mt-1.5 break-all font-mono text-sm font-semibold text-slate-700">
                            {{ $posyandu->longitude ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-4 flex items-start gap-3 rounded-xl bg-slate-50 p-4">
                    <svg class="mt-0.5 shrink-0 text-[#155DFC]" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                        <circle cx="12" cy="10" r="2.5"></circle>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat Lokasi</p>
                        <p class="mt-1.5 text-sm leading-6 text-slate-700">{{ $posyandu->alamat ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="flex flex-col-reverse gap-3 pb-5 sm:flex-row sm:justify-end">

            <a href="{{ route('superadmin.posyandu.edit', $posyandu->id_posyandu) }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                </svg>
                Edit Posyandu
            </a>
        </div>
    </div>

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapElement = document.getElementById('detail-map');
            const latitude = @json($posyandu->latitude);
            const longitude = @json($posyandu->longitude);

            if (latitude === null || longitude === null || latitude === '' || longitude === '') {
                mapElement.innerHTML = `
                    <div class="flex h-full items-center justify-center bg-slate-50 px-4 text-center">
                        <p class="text-sm text-slate-400">Koordinat lokasi belum tersedia.</p>
                    </div>
                `;
                return;
            }

            const lat = Number(latitude);
            const lng = Number(longitude);

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                mapElement.innerHTML = `
                    <div class="flex h-full items-center justify-center bg-slate-50 px-4 text-center">
                        <p class="text-sm text-slate-400">Koordinat lokasi tidak valid.</p>
                    </div>
                `;
                return;
            }

            const map = L.map('detail-map').setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const markerIcon = L.divIcon({
                className: 'detail-marker',
                html: `
                    <div style="
                        width: 38px;
                        height: 38px;
                        background: #155DFC;
                        border: 3px solid white;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 0 0 8px rgba(21, 93, 252, 0.15);
                    ">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none"
                            stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="2.5"></circle>
                        </svg>
                    </div>
                `,
                iconSize: [38, 38],
                iconAnchor: [19, 19]
            });

            const popupName = @json($posyandu->nama_posyandu);
            const popupAddress = @json($posyandu->alamat);

            L.marker([lat, lng], { icon: markerIcon })
                .addTo(map)
                .bindPopup(
                    `<strong>${escapeHtml(popupName ?? '')}</strong><br>${escapeHtml(popupAddress ?? '')}`
                )
                .openPopup();

            function escapeHtml(value) {
                return String(value).replace(/[&<>"']/g, function (character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    }[character];
                });
            }
        });
    </script>
@endsection