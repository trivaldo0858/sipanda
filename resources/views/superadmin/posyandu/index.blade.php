@extends('layouts.superadmin')

@section('title', 'Posyandu-In | Posyandu')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            <div>
                <h1 class="text-[30px] leading-[38px] font-bold text-slate-800">
                    Daftar Unit Posyandu
                </h1>

                <p class="mt-2 text-[16px] leading-6 text-slate-500">
                    Kelola data Posyandu dan akun akses Posyandu di seluruh wilayah Kabupaten Indramayu.
                </p>
            </div>

            <a href="{{ route('superadmin.posyandu.create') }}" class="shrink-0 h-[60px] px-8 bg-blue-600 hover:bg-blue-700
                                       text-white rounded-full
                                       flex items-center justify-center gap-4
                                       font-bold text-[16px]
                                       transition-all">
                <span class="text-[24px] leading-none">+</span>
                <span>Registrasi Unit Baru</span>
            </a>

        </div>


        {{-- SEARCH & FILTER --}}
        <div class="bg-white/60 backdrop-blur-md
                border border-white
                rounded-[32px]
                shadow-sm
                p-3">

            <form action="{{ route('superadmin.posyandu.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">

                {{-- SEARCH --}}
                <div class="relative flex-1">

                    {{-- Icon Search --}}
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-4.35-4.35m2.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />

                        </svg>
                    </div>

                    {{-- Input Search --}}
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama unit Posyandu..." class="w-full h-[56px]
                           pl-14 pr-6
                           bg-[#F2F3FF]
                           border-0
                           rounded-full
                           text-[16px]
                           text-slate-700
                           placeholder:text-[#737687]
                           focus:ring-2
                           focus:ring-blue-200
                           outline-none">

                </div>


                {{-- FILTER WILAYAH --}}
                <div class="relative">

                    <select name="wilayah" class="h-[56px]
                           min-w-[180px]
                           appearance-none
                           pl-11 pr-10
                           bg-[#F2F3FF]
                           border-0
                           rounded-full
                           text-[12px]
                           font-medium
                           text-[#434656]
                           focus:ring-2
                           focus:ring-blue-200
                           outline-none">

                        <option value="">
                            Semua Wilayah
                        </option>

                        @foreach($wilayah as $item)

                            <option value="{{ $item }}" {{ request('wilayah') == $item ? 'selected' : '' }}>

                                {{ $item }}

                            </option>

                        @endforeach

                    </select>


                    {{-- Icon Filter --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 top-1/2 -translate-y-1/2
                           w-4 h-4 text-slate-500
                           pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 8h12M10 12h4" />

                    </svg>


                    {{-- Icon Dropdown --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-4 top-1/2 -translate-y-1/2
                           w-4 h-4 text-slate-500
                           pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />

                    </svg>

                </div>


                {{-- BUTTON CARI --}}
                <button type="submit" class="h-[56px]
                       px-10
                       bg-blue-600
                       hover:bg-blue-700
                       text-white
                       rounded-full
                       font-bold
                       text-[16px]
                       transition-all">

                    Cari

                </button>

            </form>

        </div>

        {{-- TABLE --}}
        <div class="bg-white
                                    border border-slate-50
                                    rounded-[40px]
                                    shadow-sm
                                    overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full border-collapse">

                    {{-- TABLE HEADER --}}
                    <thead>

                        <tr class="bg-slate-50/50">

                            <th class="px-8 py-6
                                                       text-left
                                                       text-[11px]
                                                       font-black
                                                       tracking-[1.65px]
                                                       uppercase
                                                       text-slate-700">
                                Informasi Unit
                            </th>

                            <th class="px-8 py-6
                                                       text-left
                                                       text-[11px]
                                                       font-black
                                                       tracking-[1.65px]
                                                       uppercase
                                                       text-slate-700">
                                Lokasi Wilayah
                            </th>

                            <th class="px-8 py-6
                                                       text-left
                                                       text-[11px]
                                                       font-black
                                                       tracking-[1.65px]
                                                       uppercase
                                                       text-slate-700">
                                Alamat Lengkap
                            </th>

                            <th class="px-8 py-6
                                                       text-center
                                                       text-[11px]
                                                       font-black
                                                       tracking-[1.65px]
                                                       uppercase
                                                       text-slate-700">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    {{-- TABLE BODY --}}
                    <tbody class="divide-y divide-slate-50">

                        @forelse($posyandu as $item)

                            <tr class="hover:bg-blue-50/30 transition-colors">

                                {{-- INFORMASI UNIT --}}
                                <td class="px-8 py-6">

                                    <span class="block
                                                                                     text-[18px]
                                                                                     leading-7
                                                                                     font-bold
                                                                                     text-slate-800">
                                        {{ $item->nama_posyandu }}
                                    </span>

                                </td>


                                {{-- LOKASI --}}
                                <td class="px-8 py-6">

                                    <div class="space-y-1">

                                        <div class="flex gap-2">

                                            <span class="text-[12px] font-bold text-slate-700">
                                                Kec:
                                            </span>

                                            <span class="text-[12px] font-medium text-slate-500">
                                                {{ $item->kecamatan ?? '-' }}
                                            </span>

                                        </div>

                                        <div class="flex gap-2">

                                            <span class="text-[12px] font-bold text-slate-700">
                                                Desa:
                                            </span>

                                            <span class="text-[12px] font-medium text-slate-500">
                                                {{ $item->desa_kelurahan ?? '-' }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                {{-- ALAMAT --}}
                                <td class="px-8 py-6">

                                    <p class="max-w-[280px]
                                                                                  text-[14px]
                                                                                  leading-6
                                                                                  text-slate-500">
                                        {{ $item->alamat ?? '-' }}
                                    </p>

                                </td>


                                {{-- AKSI --}}
                                <td class="px-8 py-6">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- DETAIL --}}
                                        <a href="{{ route('superadmin.posyandu.show', $item->id_posyandu) }}"
                                            title="Lihat Detail" class="w-10 h-10
                                                                                       bg-blue-50
                                                                                       text-blue-600
                                                                                       rounded-xl
                                                                                       flex items-center justify-center
                                                                                       hover:bg-blue-100
                                                                                       transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                                <circle cx="12" cy="12" r="2.5" />
                                            </svg>
                                        </a>


                                        {{-- EDIT --}}
                                        <a href="{{ route('superadmin.posyandu.edit', $item->id_posyandu) }}" title="Edit"
                                            class="w-10 h-10
                                                                                       bg-amber-50
                                                                                       text-amber-600
                                                                                       rounded-xl
                                                                                       flex items-center justify-center
                                                                                       hover:bg-amber-100
                                                                                       transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652l-1.687 1.688m-2.652-2.652-8.15 8.15a4.5 4.5 0 0 0-1.128 1.902L6.75 17.25l2.711-.834a4.5 4.5 0 0 0 1.902-1.128l8.15-8.15m-2.652-2.652 2.652 2.652" />
                                            </svg>
                                        </a>


                                        {{-- HAPUS --}}
                                        <form action="{{ route('superadmin.posyandu.destroy', $item->id_posyandu) }}"
                                            method="POST" onsubmit="return confirm('Hapus unit {{ $item->nama_posyandu }}?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" title="Hapus" class="w-10 h-10
                                                                                           bg-red-50
                                                                                           text-red-600
                                                                                           rounded-xl
                                                                                           flex items-center justify-center
                                                                                           hover:bg-red-100
                                                                                           transition-all">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

                                <td colspan="4" class="px-8 py-20 text-center">

                                    <div class="flex flex-col items-center">

                                        <div class="w-16 h-16
                                                                                        rounded-full
                                                                                        bg-slate-50
                                                                                        flex items-center justify-center
                                                                                        text-slate-300
                                                                                        mb-4">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 7.5h18M5.25 7.5v10.125A1.875 1.875 0 0 0 7.125 19.5h9.75a1.875 1.875 0 0 0 1.875-1.875V7.5M8.25 7.5V5.625A1.875 1.875 0 0 1 10.125 3.75h3.75a1.875 1.875 0 0 1 1.875 1.875V7.5" />
                                            </svg>

                                        </div>

                                        <p class="font-semibold text-slate-500">
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
        @if($posyandu->hasPages())

            <div class="pt-2">
                {{ $posyandu->links() }}
            </div>

        @endif

    </div>

@endsection