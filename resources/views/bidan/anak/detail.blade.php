
@extends('layouts.bidan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Detail Balita
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Informasi lengkap dan riwayat pemeriksaan balita.
            </p>
        </div>

        <a href="{{ route('bidan.anak.index') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Informasi Balita --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

        <div class="p-5 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">
                Informasi Balita
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Identitas balita yang terdaftar di Posyandu Anda.
            </p>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <div>
                <p class="text-sm text-gray-500">NIK Anak</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $anak->nik_anak }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Nama Balita</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $anak->nama_anak }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Tanggal Lahir</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $anak->tgl_lahir->format('d-m-Y') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Umur</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $anak->umur_format }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Jenis Kelamin</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $anak->jenis_kelamin_label }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Nama Ayah</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $anak->nama_ayah ?: '-' }}
                </p>
            </div>

        </div>
    </div>

    {{-- Ringkasan Pemeriksaan --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">
            Total Pemeriksaan
        </p>
        <p class="text-2xl font-bold text-gray-800 mt-1">
            {{ $riwayat->total() }}
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Jumlah catatan pemeriksaan balita.
        </p>
    </div>

    {{-- Riwayat Pemeriksaan --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

        <div class="p-5 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">
                Riwayat Pemeriksaan
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Catatan pertumbuhan balita berdasarkan pemeriksaan Posyandu.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-4 font-semibold">No.</th>
                        <th class="px-5 py-4 font-semibold">Tanggal</th>
                        <th class="px-5 py-4 font-semibold">Berat Badan</th>
                        <th class="px-5 py-4 font-semibold">Tinggi Badan</th>
                        <th class="px-5 py-4 font-semibold">Lingkar Kepala</th>
                        <th class="px-5 py-4 font-semibold">Keluhan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($riwayat as $index => $item)
                        <tr class="hover:bg-gray-50">

                            <td class="px-5 py-4 text-gray-500">
                                {{ $riwayat->firstItem() + $index }}
                            </td>

                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap">
                                {{ $item->tgl_periksa->format('d-m-Y') }}
                            </td>

                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap">
                                {{ $item->berat_badan !== null ? $item->berat_badan . ' kg' : '-' }}
                            </td>

                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap">
                                {{ $item->tinggi_badan !== null ? $item->tinggi_badan . ' cm' : '-' }}
                            </td>

                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap">
                                {{ $item->lingkar_kepala !== null ? $item->lingkar_kepala . ' cm' : '-' }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ $item->keluhan ?: '-' }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center">
                                <p class="font-medium text-gray-600">
                                    Belum ada riwayat pemeriksaan
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Data pemeriksaan balita belum tersedia.
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($riwayat->hasPages())
            <div class="px-5 py-4 border-t border-gray-200">
                {{ $riwayat->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
