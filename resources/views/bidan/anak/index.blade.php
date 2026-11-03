
@extends('layouts.bidan')

@section('content')
<div class="space-y-6">

    {{-- Header halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Balita</h1>
            <p class="text-sm text-gray-500 mt-1">
                Daftar balita yang terdaftar di wilayah Posyandu Anda.
            </p>
        </div>
    </div>

    {{-- Informasi jumlah data --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Total Balita</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">
            {{ $anak->total() }}
        </p>
    </div>

    {{-- Pencarian --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <form action="{{ route('bidan.anak.index') }}" method="GET"
              class="flex flex-col sm:flex-row gap-3">

            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari nama atau NIK balita..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none"
                >
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"
                >
                    Cari
                </button>

                @if(request('q'))
                    <a
                        href="{{ route('bidan.anak.index') }}"
                        class="px-5 py-3 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Data Balita --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Daftar Balita</h2>
            <p class="text-sm text-gray-500 mt-1">
                Data balita berdasarkan wilayah tugas Anda.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-4 font-semibold">No.</th>
                        <th class="px-5 py-4 font-semibold">NIK Anak</th>
                        <th class="px-5 py-4 font-semibold">Nama Balita</th>
                        <th class="px-5 py-4 font-semibold">Tanggal Lahir</th>
                        <th class="px-5 py-4 font-semibold">Umur</th>
                        <th class="px-5 py-4 font-semibold">Jenis Kelamin</th>
                        <th class="px-5 py-4 font-semibold">Nama Ayah</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($anak as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 text-gray-500">
                                {{ $anak->firstItem() + $index }}
                            </td>

                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap">
                                {{ $item->nik_anak }}
                            </td>

                            <td class="px-5 py-4 font-semibold whitespace-nowrap">
    <a href="{{ route('bidan.anak.show', $item->nik_anak) }}"
       class="text-gray-800 hover:text-blue-600 transition-colors">
        {{ $item->nama_anak }}
    </a>
</td>

                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                {{ $item->tgl_lahir->format('d-m-Y') }}
                            </td>

                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                {{ $item->umur_format }}
                            </td>

                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                {{ $item->jenis_kelamin_label }}
                            </td>

                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                {{ $item->nama_ayah ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center">
                                <div class="text-gray-400 mb-2">
                                    <svg class="w-10 h-10 mx-auto" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4m5-4h6"/>
                                    </svg>
                                </div>

                                <p class="font-medium text-gray-600">
                                    Data balita tidak ditemukan
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    @if(request('q'))
                                        Coba gunakan kata kunci pencarian lainnya.
                                    @else
                                        Belum ada balita yang terdaftar di Posyandu Anda.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($anak->hasPages())
            <div class="px-5 py-4 border-t border-gray-200">
                {{ $anak->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
