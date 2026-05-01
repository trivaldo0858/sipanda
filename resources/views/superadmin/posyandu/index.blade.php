@extends('layouts.superadmin')

@section('title', 'Manajemen Unit Posyandu')

@section('content')
    <div class="space-y-6">
        {{-- Header Halaman --}}
        <div class="flex justify-between items-end">
            <div>
                <p class="text-primary font-semibold uppercase tracking-wider text-xs mb-1">Administrasi Sistem</p>
                <h1 class="text-4xl font-bold text-slate-800">Daftar Unit Posyandu</h1>
                <p class="text-slate-500 mt-2">Kelola unit layanan, perbarui lokasi, atau hapus unit yang sudah tidak aktif.
                </p>
            </div>
            {{-- AKSI: TAMBAH (Create) --}}
            <a href="{{ route('superadmin.posyandu.create') }}"
                class="bg-primary hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-semibold shadow-lg shadow-blue-200 transition-all flex items-center gap-2">
                <span>+ Registrasi Unit Baru</span>
            </a>
        </div>

        {{-- Fitur Search --}}
        <div class="bg-white p-4 rounded-xl3 border border-line shadow-card">
            <form action="{{ route('superadmin.posyandu.index') }}" method="GET" class="flex gap-4">
                <div class="flex-1 relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama unit, kategori, atau alamat..."
                        class="w-full pl-12 pr-4 py-3 bg-softbg border-none rounded-2xl focus:ring-2 focus:ring-primary/20 outline-none transition">
                </div>
                <button type="submit"
                    class="bg-slate-800 text-white px-8 py-3 rounded-2xl font-semibold hover:bg-slate-900 transition">
                    Cari Unit
                </button>
            </form>
        </div>

        {{-- Tabel Daftar Unit --}}
        <div class="bg-white rounded-xl3 border border-line shadow-card overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-line">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-slate-600 text-sm uppercase">Nama Posyandu</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 text-sm uppercase">Kecamatan</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 text-sm uppercase">Desa/Kelurahan</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 text-sm uppercase">Alamat Detail</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 text-sm uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($posyandu as $item)
                        <tr class="hover:bg-slate-50 transition">
                            {{-- Nama Posyandu --}}
                            <td class="px-6 py-5 font-bold text-slate-800">{{ $item->nama_posyandu }}</td>

                            {{-- Kolom Kecamatan (Baru) --}}
                            <td class="px-6 py-5 text-slate-600 font-medium">{{ $item->kecamatan ?? 'Data Kosong'}}</td>

                            {{-- Kolom Desa/Kelurahan (Baru --}}
                            <td class="px-6 py-5 text-slate-500">{{ $item->desa_kelurahan ?? 'Data Kosong' }}</td>

                            {{-- Alamat Detail --}}
                            <td class="px-6 py-5 text-slate-500 text-sm max-w-[200px] truncate">
                                {{ $item->alamat }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-5">
                                <div class="flex justify-center items-center gap-4">
                                    <a href="{{ route('superadmin.posyandu.edit', $item->id_posyandu) }}"
                                        class="text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1 transition">
                                        <span>✏️</span> Edit
                                    </a>

                                    <form action="{{ route('superadmin.posyandu.destroy', $item->id_posyandu) }}" method="POST"
                                        onsubmit="return confirm('Hapus unit {{ $item->nama_posyandu }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 font-semibold flex items-center gap-1 transition">
                                            <span>🗑️</span> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-slate-400 italic">
                                Belum ada unit Posyandu yang terdaftar di wilayah Indramayu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $posyandu->links() }}
        </div>
    </div>
@endsection