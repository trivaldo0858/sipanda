@extends('layouts.superadmin')

@section('title', 'Detail Posyandu')

@section('content')

<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm">
        <a
            href="{{ route('superadmin.dashboard') }}"
            class="text-slate-400 hover:text-blue-600"
        >
            Dashboard
        </a>

        <span class="text-slate-300">/</span>

        <a
            href="{{ route('superadmin.dashboard') }}"
            class="text-slate-400 hover:text-blue-600"
        >
            Peta Sebaran Posyandu
        </a>

        <span class="text-slate-300">/</span>

        <span class="font-medium text-slate-700">
            Detail Posyandu
        </span>
    </div>


    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Informasi Posyandu
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Detail informasi dan lokasi Posyandu.
            </p>
        </div>

        <a
            href="{{ route('superadmin.dashboard') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
        >
            <svg
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M19 12H5"></path>
                <path d="M12 19l-7-7 7-7"></path>
            </svg>

            Kembali ke Peta
        </a>

    </div>


    {{-- Nama Posyandu --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="flex items-start gap-3">

            <div class="mt-1 h-3 w-3 flex-shrink-0 rounded-full bg-blue-600"></div>

            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    {{ $posyandu->nama_posyandu }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Desa {{ $posyandu->desa_kelurahan }},
                    Kecamatan {{ $posyandu->kecamatan }},
                    Kabupaten {{ $posyandu->kabupaten_kota ?? 'Indramayu' }}
                </p>
            </div>

        </div>

    </div>


    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        {{-- Total Balita --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center gap-4">

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                    <svg
                        width="22"
                        height="22"
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
                    <p class="text-sm text-slate-500">
                        Total Balita
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800">
                        {{ $posyandu->anak_count ?? $posyandu->total_balita }}
                    </p>

                    <p class="text-xs text-slate-400">
                        Balita terdaftar
                    </p>
                </div>

            </div>

        </div>


        {{-- Wilayah --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center gap-4">

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">

                    <svg
                        width="22"
                        height="22"
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

                </div>

                <div>
                    <p class="text-sm text-slate-500">
                        Wilayah
                    </p>

                    <p class="mt-1 text-lg font-bold text-slate-800">
                        {{ $posyandu->desa_kelurahan }}
                    </p>

                    <p class="text-xs text-slate-400">
                        Kecamatan {{ $posyandu->kecamatan }}
                    </p>
                </div>

            </div>

        </div>

    </div>


    {{-- Informasi Posyandu --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">
            <h2 class="text-lg font-bold text-slate-800">
                Informasi Posyandu
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Informasi umum mengenai Posyandu.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-x-8 gap-y-6 p-6 md:grid-cols-2">

            {{-- Nama --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Nama Posyandu
                </p>

                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ $posyandu->nama_posyandu }}
                </p>
            </div>


            {{-- Desa --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Desa / Kelurahan
                </p>

                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ $posyandu->desa_kelurahan }}
                </p>
            </div>


            {{-- Kecamatan --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Kecamatan
                </p>

                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ $posyandu->kecamatan }}
                </p>
            </div>


            {{-- Kabupaten --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Kabupaten
                </p>

                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ $posyandu->kabupaten_kota ?? 'Indramayu' }}
                </p>
            </div>


            {{-- Alamat --}}
            <div class="md:col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Alamat Lengkap
                </p>

                <p class="mt-1 text-sm font-semibold leading-6 text-slate-700">
                    {{ $posyandu->alamat }}
                </p>
            </div>


            {{-- Bidan --}}
            <div class="md:col-span-2">

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Bidan Pendamping
                </p>

                <div class="mt-2 flex items-center gap-3">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-blue-600">

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

                    <span class="text-sm font-semibold text-slate-700">
                        {{ $posyandu->bidan->first()?->nama_bidan ?? 'Belum ditentukan' }}
                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- Lokasi Posyandu --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <h2 class="text-lg font-bold text-slate-800">
                Lokasi Posyandu
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Lokasi Posyandu berdasarkan koordinat GIS.
            </p>

        </div>


        <div class="p-6">

            {{-- Map --}}
            <div
                id="detail-map"
                class="h-[420px] w-full overflow-hidden rounded-2xl"
            ></div>


            {{-- Koordinat --}}
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Latitude
                    </p>

                    <p class="mt-1 font-mono text-sm font-semibold text-slate-700">
                        {{ $posyandu->latitude ?? '-' }}
                    </p>

                </div>


                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Longitude
                    </p>

                    <p class="mt-1 font-mono text-sm font-semibold text-slate-700">
                        {{ $posyandu->longitude ?? '-' }}
                    </p>

                </div>

            </div>


            {{-- Alamat lokasi --}}
            <div class="mt-4 flex items-start gap-3 rounded-xl bg-slate-50 p-4">

                <svg
                    class="mt-0.5 flex-shrink-0 text-blue-600"
                    width="18"
                    height="18"
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

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Alamat Lokasi
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-700">
                        {{ $posyandu->alamat }}
                    </p>
                </div>

            </div>

        </div>

    </div>


    {{-- Action --}}
    <div class="flex flex-col-reverse gap-3 pb-6 sm:flex-row sm:justify-end">

        <a
            href="{{ route('superadmin.posyandu.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Kembali ke Daftar
        </a>

        <a
            href="{{ route('superadmin.posyandu.edit', $posyandu->id_posyandu) }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
        >
            <svg
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
            </svg>

            Edit Posyandu
        </a>

    </div>

</div>


{{-- Leaflet --}}
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const latitude = {{ $posyandu->latitude ?? 'null' }};
    const longitude = {{ $posyandu->longitude ?? 'null' }};

    if (latitude === null || longitude === null) {
        document.getElementById('detail-map').innerHTML = `
            <div class="flex h-full items-center justify-center bg-slate-50">
                <p class="text-sm text-slate-400">
                    Koordinat lokasi belum tersedia.
                </p>
            </div>
        `;

        return;
    }


    const map = L.map('detail-map').setView(
        [latitude, longitude],
        16
    );


    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


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
                <svg
                    width="19"
                    height="19"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                    <circle cx="12" cy="10" r="2.5"></circle>
                </svg>
            </div>
        `,
        iconSize: [38, 38],
        iconAnchor: [19, 19]
    });


    L.marker(
        [latitude, longitude],
        {
            icon: markerIcon
        }
    )
    .addTo(map)
    .bindPopup(`
        <strong>{{ $posyandu->nama_posyandu }}</strong><br>
        {{ $posyandu->alamat }}
    `)
    .openPopup();

});

</script>

@endsection