{{-- resources/views/superadmin/pengguna/index.blade.php --}}
@extends('layouts.superadmin')

@section('title', 'Direktori Staff')

@section('content')

<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

        <div>
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight">
                Direktori Staff
            </h1>

            <p class="mt-2 text-lg text-slate-500">
                Kelola akun Bidan, Kader, dan Orang Tua dalam sistem SIPANDA.
            </p>
        </div>

        <a href="{{ route('superadmin.pengguna.create') }}"
           class="inline-flex items-center justify-center h-14 px-7 rounded-full bg-blue-600 text-white font-semibold tracking-wide shadow-lg hover:bg-blue-700 transition">
            + Buat Akun Staff
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm p-6">

        <form method="GET" action="{{ route('superadmin.pengguna.index') }}"
              class="grid grid-cols-1 lg:grid-cols-4 gap-5">

            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-3">
                    Cari Pengguna
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari username..."
                    class="w-full h-12 rounded-xl border border-slate-200 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-3">
                    Role
                </label>

                <select
                    name="role"
                    class="w-full h-12 rounded-xl border border-slate-200 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Role</option>
                    <option value="Bidan" {{ request('role') == 'Bidan' ? 'selected' : '' }}>Bidan</option>
                    <option value="Kader" {{ request('role') == 'Kader' ? 'selected' : '' }}>Kader</option>
                    <option value="OrangTua" {{ request('role') == 'OrangTua' ? 'selected' : '' }}>Orang Tua</option>
                </select>
            </div>

            {{-- Posyandu --}}
            <div>
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-3">
                    Posyandu
                </label>

                <select
                    name="id_posyandu"
                    class="w-full h-12 rounded-xl border border-slate-200 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Unit</option>

                    @foreach($posyanduList as $item)
                        <option value="{{ $item->id_posyandu }}"
                            {{ request('id_posyandu') == $item->id_posyandu ? 'selected' : '' }}>
                            {{ $item->nama_posyandu }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Button --}}
            <div class="lg:col-span-4 flex flex-wrap gap-3 pt-2">
                <button type="submit"
                    class="h-12 px-6 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                    Terapkan Filter
                </button>

                <a href="{{ route('superadmin.pengguna.index') }}"
                    class="h-12 px-6 rounded-xl border border-slate-200 text-slate-600 font-semibold inline-flex items-center hover:bg-slate-50 transition">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm overflow-hidden">

        <div class="px-8 py-6 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-800">
                Daftar Pengguna
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-left">

                <thead class="bg-slate-50">
                    <tr class="text-xs uppercase tracking-[2px] text-slate-500">
                        <th class="px-8 py-4">Pengguna</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Detail</th>
                        <th class="px-6 py-4">Unit Akses</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($pengguna as $item)

                        @php
                            $nama = $item->username;

                            if($item->role === 'Bidan' && $item->bidan){
                                $nama = $item->bidan->nama_bidan;
                            }

                            if($item->role === 'Kader' && $item->kader){
                                $nama = $item->kader->nama_kader;
                            }

                            if($item->role === 'OrangTua' && $item->orangTua){
                                $nama = $item->orangTua->nama_ibu;
                            }
                        @endphp

                        <tr class="hover:bg-slate-50 transition">

                            {{-- Pengguna --}}
                            <td class="px-8 py-5">
                                <div class="font-semibold text-slate-900">
                                    {{ $nama }}
                                </div>

                                <div class="text-sm text-slate-500 mt-1">
                                    {{ $item->username }}
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-5">
                                @php
                                    $badge = match($item->role){
                                        'Bidan' => 'bg-blue-100 text-blue-700',
                                        'Kader' => 'bg-amber-100 text-amber-700',
                                        default => 'bg-emerald-100 text-emerald-700'
                                    };
                                @endphp

                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">
                                    {{ $item->role }}
                                </span>
                            </td>

                            {{-- Detail --}}
                            <td class="px-6 py-5 text-sm text-slate-600 leading-7">

                                @if($item->role === 'Bidan' && $item->bidan)
                                    NIP: {{ $item->bidan->nip ?? '-' }} <br>
                                    Telp: {{ $item->bidan->no_telp ?? '-' }}
                                @elseif($item->role === 'Kader' && $item->kader)
                                    Wilayah: {{ $item->kader->wilayah ?? '-' }}
                                @elseif($item->role === 'OrangTua' && $item->orangTua)
                                    Alamat: {{ $item->orangTua->alamat ?? '-' }}
                                @else
                                    -
                                @endif

                            </td>

                            {{-- Posyandu --}}
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-2 max-w-md">

                                    @forelse($item->posyanduList as $ps)

                                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-sm">
                                            {{ $ps->nama_posyandu }}
                                        </span>

                                    @empty

                                        <span class="text-slate-400 text-sm">Belum ada</span>

                                    @endforelse

                                </div>
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-5">

                                <div class="flex items-center justify-end gap-3">

                                    <a href="{{ route('superadmin.pengguna.edit', $item->id_user) }}"
                                       class="h-10 px-4 rounded-xl bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('superadmin.pengguna.destroy', $item->id_user) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="h-10 px-4 rounded-xl bg-red-50 text-red-600 font-medium hover:bg-red-100 transition"
                                        >
                                            Hapus
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-8 py-14 text-center text-slate-400">
                                Tidak ada data pengguna ditemukan.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        @if(method_exists($pengguna, 'links'))
            <div class="px-8 py-6 border-t border-slate-100">
                {{ $pengguna->links() }}
            </div>
        @endif

    </div>

</div>

@endsection