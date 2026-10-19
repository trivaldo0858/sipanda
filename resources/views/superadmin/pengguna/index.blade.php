@extends('layouts.superadmin')

@section('title', 'Pengguna')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-[30px] leading-[38px] font-bold text-slate-800">
                    Manajemen Pengguna
                </h1>

                <p class="mt-2 text-[16px] leading-6 text-slate-500">
                    Kelola akun bidan yang terdaftar dalam sistem Posyandu-In.
                </p>
            </div>

            <a href="{{ route('superadmin.pengguna.create') }}" class="flex h-[60px] shrink-0 items-center justify-center gap-4 rounded-full
                       bg-blue-600 px-8 text-[16px] font-bold text-white
                       transition-all hover:bg-blue-700">
                <span class="text-[24px] leading-none">+</span>
                <span>Registrasi Pengguna</span>
            </a>
        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50
                            px-5 py-4 text-sm text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50
                            px-5 py-4 text-sm text-red-700" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- SEARCH & FILTER --}}
        <div class="rounded-[32px] border border-white bg-white/60
                    p-3 shadow-sm backdrop-blur-md">

            <form action="{{ route('superadmin.pengguna.index') }}" method="GET" class="flex flex-col gap-3 lg:flex-row">

                {{-- SEARCH --}}
                <div class="relative min-w-0 flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-5 top-1/2 h-5 w-5
                                -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16 16 4 4" />
                    </svg>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama bidan atau username..." aria-label="Cari nama bidan atau username" class="h-[56px] w-full rounded-full border-0
                                  bg-[#F2F3FF] pl-14 pr-6 text-[16px]
                                  text-slate-700 placeholder:text-[#737687]
                                  outline-none focus:ring-2 focus:ring-blue-200">
                </div>

                {{-- FILTER WILAYAH --}}
                <div class="relative lg:w-[180px] lg:shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2
                                h-4 w-4 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h9m4 0h3M4 17h3m4 0h9M13 4v6M7 14v6" />
                    </svg>

                    <select name="id_posyandu" aria-label="Filter wilayah Posyandu" class="h-[56px] w-full appearance-none rounded-full
                                   border-0 bg-[#F2F3FF] pl-10 pr-9
                                   text-[12px] font-medium text-[#434656]
                                   outline-none focus:ring-2 focus:ring-blue-200">
                        <option value="">Semua Wilayah</option>

                        @foreach($posyanduList as $item)
                            <option value="{{ $item->id_posyandu }}" {{ request('id_posyandu') == $item->id_posyandu ? 'selected' : '' }}>
                                {{ $item->nama_posyandu }}
                            </option>
                        @endforeach
                    </select>

                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute right-4 top-1/2
                                h-4 w-4 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </div>

                {{-- BUTTON CARI --}}
                <button type="submit" class="h-[56px] rounded-full bg-blue-600 px-10
                               text-[16px] font-bold text-white
                               transition-all hover:bg-blue-700
                               lg:shrink-0">
                    Cari
                </button>
            </form>
        </div>

        {{-- TABEL PENGGUNA --}}
        <div class="overflow-hidden rounded-[40px] border border-slate-50
                    bg-white shadow-sm">

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">

                    {{-- TABLE HEADER --}}
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-6 text-[11px] font-black
                                       uppercase tracking-[1.65px] text-slate-700">
                                Informasi Bidan
                            </th>

                            <th class="px-8 py-6 text-[11px] font-black
                                       uppercase tracking-[1.65px] text-slate-700">
                                Posyandu Pendamping
                            </th>

                            <th class="px-8 py-6 text-center text-[11px] font-black
                                       uppercase tracking-[1.65px] text-slate-700">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    {{-- TABLE BODY --}}
                    <tbody class="divide-y divide-slate-50">

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

                                <tr class="transition-colors hover:bg-blue-50/30">

                                    {{-- INFORMASI BIDAN --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-14 w-14 shrink-0
                                                                items-center justify-center
                                                                rounded-full bg-indigo-100
                                                                text-xl font-semibold text-slate-800">
                                                {{ $inisial ?: 'B' }}
                                            </div>

                                            <div>
                                                <p class="text-[18px] leading-7
                                                                  font-bold text-slate-800">
                                                    {{ $nama }}
                                                </p>

                                                <p class="mt-0.5 text-[14px] text-slate-500">
                                                    @ {{ $item->username }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- POSYANDU PENDAMPING --}}
                                    <td class="px-8 py-5">
                                        @if($item->posyandu)
                                            <p class="text-[18px] leading-7
                                                                  font-bold text-slate-800">
                                                {{ $item->posyandu->nama_posyandu }}
                                            </p>

                                            @if($item->posyandu->desa_kelurahan ?? false)
                                                <p class="mt-0.5 text-[14px] text-slate-500">
                                                    • {{ $item->posyandu->desa_kelurahan }}
                                                </p>
                                            @elseif($item->posyandu->kecamatan ?? false)
                                                <p class="mt-0.5 text-[14px] text-slate-500">
                                                    • Kec. {{ $item->posyandu->kecamatan }}
                                                </p>
                                            @endif
                                        @else
                                            <span class="text-sm italic text-slate-400">
                                                Belum ditentukan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    
{{-- AKSI --}}
<td class="px-8 py-5">
    <div class="flex items-center justify-center gap-2">

        {{-- 1. LIHAT DETAIL --}}
        <a href="{{ route('superadmin.pengguna.show', $item->id_user) }}"
           title="Lihat Detail"
           aria-label="Lihat detail {{ $nama }}"
           class="flex h-10 w-10 items-center justify-center
                  rounded-xl bg-blue-50 text-blue-600
                  transition-all hover:bg-blue-100
                  focus:outline-none focus:ring-2 focus:ring-blue-200">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2"
                 aria-hidden="true">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12
                         18 18.75 12 18.75 2.25 12 2.25 12Z"/>
                <circle cx="12" cy="12" r="2.5"/>
            </svg>
        </a>

        {{-- 2. EDIT --}}
        <a href="{{ route('superadmin.pengguna.edit', $item->id_user) }}"
           title="Edit"
           aria-label="Edit akun {{ $nama }}"
           class="flex h-10 w-10 items-center justify-center
                  rounded-xl bg-amber-50 text-amber-600
                  transition-all hover:bg-amber-100
                  focus:outline-none focus:ring-2 focus:ring-amber-200">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2"
                 aria-hidden="true">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1
                         2.652 2.652l-1.687 1.688m-2.652-2.652-8.15
                         8.15a4.5 4.5 0 0 0-1.128 1.902L6.75 17.25
                         l2.711-.834a4.5 4.5 0 0 0 1.902-1.128
                         l8.15-8.15m-2.652-2.652 2.652 2.652"/>
            </svg>
        </a>

        {{-- 3. HAPUS --}}
        <form method="POST"
              action="{{ route('superadmin.pengguna.destroy', $item->id_user) }}"
              onsubmit="return confirm('Yakin ingin menghapus akun bidan {{ $nama }}?')">

            @csrf
            @method('DELETE')

            <button type="submit"
                    title="Hapus"
                    aria-label="Hapus akun {{ $nama }}"
                    class="flex h-10 w-10 items-center justify-center
                           rounded-xl bg-red-50 text-red-600
                           transition-all hover:bg-red-100
                           focus:outline-none focus:ring-2 focus:ring-red-200">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2"
                     aria-hidden="true">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6 7h12m-9 0V4h6v3m-7 0 1 13h6l1-13
                             M10 11v6m4-6v6"/>
                </svg>
            </button>
        </form>

    </div>
</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="mb-4 flex h-16 w-16 items-center
                                                        justify-center rounded-full
                                                        bg-slate-50 text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm6-1a4 4 0 0 1 0 8m2 3h2v-2a4 4 0 0 0-3-3.87" />
                                            </svg>
                                        </div>

                                        <p class="font-semibold text-slate-500">
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
                    <div class="border-t border-slate-100 px-8 py-5">
                        {{ $pengguna->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
    
@endsection