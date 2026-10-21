@extends('layouts.superadmin')

@section('title', 'Pengguna')

@section('content')
    <div class="space-y-5">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-8 text-slate-800">
                    Manajemen Pengguna
                </h1>

            </div>

            <a href="{{ route('superadmin.pengguna.create') }}" class="inline-flex h-11 shrink-0 items-center justify-center gap-2
                          rounded-xl bg-[#155DFC] px-5 text-sm font-semibold text-white
                          transition hover:bg-blue-700
                          focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                </svg>

                <span>Registrasi Pengguna</span>
            </a>
        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50
                                px-4 py-3 text-sm text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50
                                px-4 py-3 text-sm text-red-700" role="alert">
                {{ session('error') }}
            </div>
        @endif


{{-- SEARCH & FILTER --}}
<div class="rounded-2xl border border-white bg-white/60 p-2 shadow-sm backdrop-blur-md">
    <form
        action="{{ route('superadmin.pengguna.index') }}"
        method="GET"
        class="flex flex-col gap-2 lg:flex-row"
    >
        {{-- SEARCH --}}
        <div class="relative min-w-0 flex-1">
            <label for="search-pengguna" class="sr-only">
                Cari nama bidan atau username
            </label>

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m21 21-4.35-4.35m2.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                />
            </svg>

            <input
                id="search-pengguna"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama bidan atau username..."
                class="h-12 w-full rounded-xl border-0 bg-[#F2F3FF] py-3 pl-11 pr-4 text-sm text-slate-700 outline-none placeholder:text-[#737687] focus:ring-2 focus:ring-blue-200"
            >
        </div>

        {{-- FILTER WILAYAH --}}
        <div class="relative shrink-0">
            <label for="filter-posyandu-pengguna" class="sr-only">
                Filter wilayah Posyandu
            </label>

            <select
                id="filter-posyandu-pengguna"
                name="id_posyandu"
                class="h-12 w-full min-w-[180px] appearance-none rounded-xl border-0 bg-[#F2F3FF] py-3 pl-10 pr-9 text-sm font-medium text-[#434656] outline-none focus:ring-2 focus:ring-blue-200"
            >
                <option value="">Semua Wilayah</option>

                @foreach ($posyanduList as $item)
                    <option
                        value="{{ $item->id_posyandu }}"
                        {{ request('id_posyandu') == $item->id_posyandu ? 'selected' : '' }}
                    >
                        {{ $item->nama_posyandu }}
                    </option>
                @endforeach
            </select>

            {{-- ICON FILTER --}}
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 7h9m4 0h3M4 17h3m4 0h9M13 4v6M7 14v6"
                />
            </svg>

            {{-- ICON DROPDOWN --}}
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m6 9 6 6 6-6"
                />
            </svg>
        </div>

        {{-- BUTTON CARI --}}
        <button
            type="submit"
            class="h-12 shrink-0 rounded-xl bg-blue-600 px-7 text-sm font-bold text-white transition-colors hover:bg-blue-700"
        >
            Cari
        </button>
    </form>
</div>


        {{-- TABEL PENGGUNA --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    {{-- TABLE HEADER --}}
                    <thead>
                        <tr class="bg-slate-50/70">
                            <th class="whitespace-nowrap px-5 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Informasi Bidan
                            </th>

                            <th class="whitespace-nowrap px-5 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Posyandu Pendamping
                            </th>

                            <th class="px-5 py-4 text-center text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    {{-- TABLE BODY --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pengguna as $item)
                            @if($item->role === 'Bidan')
                                @php
                                    $nama = $item->bidan->nama_bidan ?? $item->username;

                                    $inisial = collect(explode(' ', trim($nama)))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
                                        ->implode('');
                                @endphp

                                <tr class="transition-colors hover:bg-blue-50/40">

                                    {{-- INFORMASI BIDAN --}}
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0
                                                                    items-center justify-center
                                                                    rounded-full bg-indigo-50
                                                                    text-sm font-semibold text-indigo-700">
                                                {{ $inisial ?: 'B' }}
                                            </div>

                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold leading-5 text-slate-800">
                                                    {{ $nama }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    @ {{ $item->username }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- POSYANDU PENDAMPING --}}
                                    <td class="px-5 py-4 sm:px-6">
                                        @if($item->posyandu)
                                            <p class="text-sm font-semibold leading-5 text-slate-800">
                                                {{ $item->posyandu->nama_posyandu }}
                                            </p>

                                            @if($item->posyandu->desa_kelurahan ?? false)
                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $item->posyandu->desa_kelurahan }}
                                                </p>
                                            @elseif($item->posyandu->kecamatan ?? false)
                                                <p class="mt-1 text-xs text-slate-500">
                                                    Kec. {{ $item->posyandu->kecamatan }}
                                                </p>
                                            @endif
                                        @else
                                            <span class="text-sm italic text-slate-400">
                                                Belum ditentukan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center justify-center gap-2">

                                            {{-- LIHAT DETAIL --}}
                                            <a href="{{ route('superadmin.pengguna.show', $item->id_user) }}" title="Lihat Detail"
                                                aria-label="Lihat detail {{ $nama }}" class="flex h-9 w-9 items-center justify-center
                                                                  rounded-lg bg-blue-50 text-blue-600
                                                                  transition hover:bg-blue-100
                                                                  focus:outline-none focus:ring-2 focus:ring-blue-200">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                                    <circle cx="12" cy="12" r="2.5" />
                                                </svg>
                                            </a>

                                            {{-- EDIT --}}
                                            <a href="{{ route('superadmin.pengguna.edit', $item->id_user) }}" title="Edit"
                                                aria-label="Edit akun {{ $nama }}" class="flex h-9 w-9 items-center justify-center
                                                                  rounded-lg bg-amber-50 text-amber-600
                                                                  transition hover:bg-amber-100
                                                                  focus:outline-none focus:ring-2 focus:ring-amber-200">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652l-1.687 1.688m-2.652-2.652-8.15 8.15a4.5 4.5 0 0 0-1.128 1.902L6.75 17.25l2.711-.834a4.5 4.5 0 0 0 1.902-1.128l8.15-8.15m-2.652-2.652 2.652 2.652" />
                                                </svg>
                                            </a>

                                            {{-- HAPUS --}}
                                            <form method="POST" action="{{ route('superadmin.pengguna.destroy', $item->id_user) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus akun bidan {{ $nama }}?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Hapus" aria-label="Hapus akun {{ $nama }}" class="flex h-9 w-9 items-center justify-center
                                                                           rounded-lg bg-red-50 text-red-600
                                                                           transition hover:bg-red-100
                                                                           focus:outline-none focus:ring-2 focus:ring-red-200">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
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
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-14 text-center sm:px-6">
                                    <div class="flex flex-col items-center">
                                        <div class="mb-3 flex h-12 w-12 items-center
                                                            justify-center rounded-full
                                                            bg-slate-50 text-slate-300">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm6-1a4 4 0 0 1 0 8m2 3h2v-2a4 4 0 0 0-3-3.87" />
                                            </svg>
                                        </div>

                                        <p class="text-sm font-medium text-slate-500">
                                            Belum ada data pengguna.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if(method_exists($pengguna, 'links'))
                @if($pengguna->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                        {{ $pengguna->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
@endsection