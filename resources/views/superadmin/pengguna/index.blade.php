{{-- resources/views/superadmin/pengguna/index.blade.php --}}
@extends('layouts.superadmin')

@section('title', 'Direktori Bidan')

@section('content')

<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
        <div>
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight">
                Direktori Bidan
            </h1>
            <p class="mt-2 text-lg text-slate-500">
                Kelola akun Bidan terdaftar dalam sistem SIPANDA.
            </p>
        </div>

        <a href="{{ route('superadmin.pengguna.create') }}"
           class="inline-flex items-center justify-center h-14 px-7 rounded-full bg-blue-600 text-white font-semibold tracking-wide shadow-lg hover:bg-blue-700 transition">
            + Tambah Bidan Baru
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm p-6">
        <form method="GET" action="{{ route('superadmin.pengguna.index') }}"
              class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Search --}}
            <div class="lg:col-span-1">
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-3">
                    Cari Nama/Username
                </label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari Bidan..."
                    class="w-full h-12 rounded-xl border border-slate-200 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            {{-- Posyandu --}}
            <div class="lg:col-span-1">
                <label class="block text-xs font-bold tracking-[2px] text-slate-500 uppercase mb-3">
                    Filter Wilayah Posyandu
                </label>
                <select
                    name="id_posyandu"
                    class="w-full h-12 rounded-xl border border-slate-200 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Wilayah</option>
                    @foreach($posyanduList as $item)
                        <option value="{{ $item->id_posyandu }}"
                            {{ request('id_posyandu') == $item->id_posyandu ? 'selected' : '' }}>
                            {{ $item->nama_posyandu }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Button --}}
            <div class="flex items-end gap-3">
                <button type="submit"
                    class="h-12 px-6 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                    Filter
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
                Daftar Akun Bidan
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs uppercase tracking-[2px] text-slate-500">
                        <th class="px-8 py-4">Nama Bidan</th>
                        <th class="px-6 py-4">Status Role</th>
                        <th class="px-6 py-4">Wilayah Tugas</th>
                        <th class="px-6 py-4 text-right pr-12">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($pengguna as $item)
                        @if($item->role === 'Bidan') {{-- Filter di sisi View untuk memastikan hanya Bidan --}}
                            @php
                                $nama = $item->bidan->nama_bidan ?? $item->username;
                            @endphp

                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5">
                                    <div class="font-semibold text-slate-900">{{ $nama }}</div>
                                    <div class="text-sm text-slate-500 mt-1">@ {{ $item->username }}</div>
                                </td>

                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                                        Bidan
                                    </span>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2 max-w-md">
                                        @forelse($item->posyanduList as $ps)
                                            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-sm">
                                                {{ $ps->nama_posyandu }}
                                            </span>
                                        @empty
                                            <span class="text-slate-400 text-sm italic">Belum ditentukan</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-end gap-3 pr-6">
                                        <a href="{{ route('superadmin.pengguna.edit', $item->id_user) }}"
                                           class="h-10 px-4 rounded-xl bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition inline-flex items-center">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('superadmin.pengguna.destroy', $item->id_user) }}" onsubmit="return confirm('Hapus akun bidan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-10 px-4 rounded-xl bg-red-50 text-red-600 font-medium hover:bg-red-100 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-14 text-center text-slate-400">
                                Data Bidan tidak ditemukan.
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