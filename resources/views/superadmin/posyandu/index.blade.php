@extends('layouts.superadmin')

@section('title', 'Posyandu')

@section('content')
    <div class="space-y-5">
        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold leading-8 text-slate-800">
                Daftar Unit Posyandu
            </h1>

            <a href="{{ route('superadmin.posyandu.create') }}"
                class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-bold text-white transition-colors hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>Registrasi Unit</span>
            </a>
        </div>

        {{-- SEARCH & FILTER --}}
        <div class="rounded-2xl border border-white bg-white/60 p-2 shadow-sm backdrop-blur-md">
            <form action="{{ route('superadmin.posyandu.index') }}" method="GET" class="flex flex-col gap-2 lg:flex-row">
                {{-- SEARCH --}}
                <div class="relative min-w-0 flex-1">
                    <label for="search-posyandu" class="sr-only">
                        Cari nama unit Posyandu
                    </label>

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-4.35-4.35m2.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>

                    <input id="search-posyandu" type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama unit Posyandu..."
                        class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] py-3 pl-11 pr-4 text-sm text-slate-700 outline-none placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200">
                </div>

                {{-- FILTER WILAYAH --}}
                <div class="relative shrink-0">
                    <label for="filter-wilayah" class="sr-only">
                        Filter wilayah
                    </label>

                    <select id="filter-wilayah" name="wilayah"
                        class="h-12 w-full min-w-[180px] appearance-none rounded-xl border-0 bg-[#F2F3FF] py-3 pl-10 pr-9 text-sm font-medium text-[#434656] outline-none focus:ring-2 focus:ring-blue-200">
                        <option value="">Semua Wilayah</option>

                        @foreach ($wilayah as $item)
                            <option value="{{ $item }}" {{ request('wilayah') == $item ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>

                    {{-- ICON FILTER --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2
                                    h-4 w-4 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h9m4 0h3M4 17h3m4 0h9M13 4v6M7 14v6" />
                    </svg>

                    {{-- ICON DROPDOWN --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </div>

                {{-- BUTTON CARI --}}
                <button type="submit"
                    class="h-12 shrink-0 rounded-xl bg-blue-600 px-7 text-sm font-bold text-white transition-colors hover:bg-blue-700">
                    Cari
                </button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70">
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Informasi Unit
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Lokasi Wilayah
                            </th>
                            <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Alamat Lengkap
                            </th>
                            <th class="px-5 py-4 text-center text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($posyandu as $item)
                            <tr class="transition-colors hover:bg-blue-50/30">
                                {{-- INFORMASI UNIT --}}
                                <td class="px-5 py-4">
                                    <span class="block text-base font-bold leading-6 text-slate-800">
                                        {{ $item->nama_posyandu }}
                                    </span>
                                </td>

                                {{-- LOKASI --}}
                                <td class="px-5 py-4">
                                    <div class="space-y-1 text-xs">
                                        <div class="flex flex-wrap gap-x-2">
                                            <span class="font-bold text-slate-700">Kec:</span>
                                            <span class="font-medium text-slate-500">
                                                {{ $item->kecamatan ?? '-' }}
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap gap-x-2">
                                            <span class="font-bold text-slate-700">Desa:</span>
                                            <span class="font-medium text-slate-500">
                                                {{ $item->desa_kelurahan ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- ALAMAT --}}
                                <td class="px-5 py-4">
                                    <p class="max-w-[280px] text-sm leading-5 text-slate-500">
                                        {{ $item->alamat ?? '-' }}
                                    </p>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- DETAIL --}}
                                        <a href="{{ route('superadmin.posyandu.show', $item->id_posyandu) }}"
                                            title="Lihat Detail" aria-label="Lihat detail {{ $item->nama_posyandu }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition-colors hover:bg-blue-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                                <circle cx="12" cy="12" r="2.5" />
                                            </svg>
                                        </a>

                                        {{-- EDIT --}}
                                        <a href="{{ route('superadmin.posyandu.edit', $item->id_posyandu) }}" title="Edit"
                                            aria-label="Edit {{ $item->nama_posyandu }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition-colors hover:bg-amber-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652l-1.687 1.688m-2.652-2.652-8.15 8.15a4.5 4.5 0 0 0-1.128 1.902L6.75 17.25l2.711-.834a4.5 4.5 0 0 0 1.902-1.128l8.15-8.15m-2.652-2.652 2.652 2.652" />
                                            </svg>
                                        </a>

                                        {{-- HAPUS --}}
                                        <form action="{{ route('superadmin.posyandu.destroy', $item->id_posyandu) }}"
                                            method="POST" onsubmit="return confirm('Hapus unit {{ $item->nama_posyandu }}?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" title="Hapus" aria-label="Hapus {{ $item->nama_posyandu }}"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 transition-colors hover:bg-red-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                    aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 7h12m-9 0V4h6v3m-7 0 1 13h6l1-13M10 11v6m4-6v6" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 7.5h18M5.25 7.5v10.125A1.875 1.875 0 0 0 7.125 19.5h9.75a1.875 1.875 0 0 0 1.875-1.875V7.5M8.25 7.5V5.625A1.875 1.875 0 0 1 10.125 3.75h3.75a1.875 1.875 0 0 1 1.875 1.875V7.5" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-500">
                                            Belum ada data Posyandu.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if ($posyandu->hasPages())
            <div class="pt-1">
                {{ $posyandu->links() }}
            </div>
        @endif
    </div>
@endsection