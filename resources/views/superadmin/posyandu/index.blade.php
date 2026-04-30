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

        {{-- Tabel Daftar Unit (Kolom disesuaikan dengan Form) --}}
        <div class="bg-white rounded-xl3 border border-line shadow-card overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-line">
                    <tr>
                        <th class="px-8 py-4 font-semibold text-slate-600 text-sm uppercase">Nama Posyandu</th>
                        <th class="px-8 py-4 font-semibold text-slate-600 text-sm uppercase">Kategori</th>
                        <th class="px-8 py-4 font-semibold text-slate-600 text-sm uppercase">Kecamatan</th>
                        <th class="px-8 py-4 font-semibold text-slate-600 text-sm uppercase">Alamat</th>
                        <th class="px-8 py-4 font-semibold text-slate-600 text-sm uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($posyandu as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-5 font-bold text-slate-800">{{ $item->nama_posyandu }}</td>
                            <td class="px-8 py-5">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium {{ $item->kategori == 'Balita' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }}">
                                    {{ $item->kategori ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-slate-500">{{ $item->desa_kelurahan }}</td>
                            <td class="px-8 py-5 text-slate-500 text-sm max-w-xs truncate">{{ $item->alamat }}</td>
                            <td class="px-8 py-5">
                                <div class="flex justify-center items-center gap-4">
                                    {{-- AKSI: EDIT LOKASI --}}
                                    <a href="{{ route('superadmin.posyandu.edit', $item->id_posyandu) }}"
                                        class="text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1">
                                        <span>✏️</span> Edit
                                    </a>

                                    {{-- AKSI: HAPUS (Delete) --}}
                                    <form action="{{ route('superadmin.posyandu.destroy', $item->id_posyandu) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 font-semibold flex items-center gap-1">
                                            <span>🗑️</span> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center">
                                <div class="text-slate-400 italic">Belum ada unit Posyandu yang terdaftar.</div>
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