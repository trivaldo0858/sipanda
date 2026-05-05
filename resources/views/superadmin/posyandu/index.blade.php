@extends('layouts.superadmin')

@section('title', 'SIPANDA')

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
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-blue-100 transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-3">
                <span class="text-xl">+</span>
                <span>Registrasi Unit Baru</span>
            </a>
        </div>

        {{-- Fitur Search --}}
        <div class="bg-white/60 backdrop-blur-md p-3 rounded-[2rem] border border-white shadow-sm">
            <form action="{{ route('superadmin.posyandu.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative group">
                    <span
                        class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"><svg
                            xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama unit..."
                        class="w-full pl-14 pr-6 py-4 bg-white border-none rounded-[1.5rem] focus:ring-4 focus:ring-blue-50 outline-none transition-all font-medium placeholder:text-slate-300">
                </div>
                <button type="submit"
                    class="bg-slate-800 text-white px-10 py-4 rounded-[1.5rem] font-bold hover:bg-slate-900 transition-all">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('superadmin.posyandu.index') }}"
                        class="px-6 py-4 rounded-[1.5rem] font-bold text-slate-400 hover:text-slate-600 transition-colors flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Daftar Unit --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-50 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-6 font-black text-slate-400 text-[11px] uppercase tracking-[0.15em]">
                                Informasi Unit</th>
                            <th class="px-8 py-6 font-black text-slate-400 text-[11px] uppercase tracking-[0.15em]">Lokasi
                                Wilayah</th>
                            <th class="px-8 py-6 font-black text-slate-400 text-[11px] uppercase tracking-[0.15em]">Alamat
                                Lengkap</th>
                            <th
                                class="px-8 py-6 font-black text-slate-400 text-[11px] uppercase tracking-[0.15em] text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($posyandu as $item)
                            <tr class="hover:bg-blue-50/30 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition-colors">{{ $item->nama_posyandu }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-700">Kec:</span>
                                            <span
                                                class="text-xs text-slate-500 font-medium">{{ $item->kecamatan ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-700">Desa:</span>
                                            <span
                                                class="text-xs text-slate-500 font-medium">{{ $item->desa_kelurahan ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm text-slate-500 leading-relaxed max-w-[250px]">
                                        {{ $item->alamat }}
                                    </p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-center items-center gap-3">
                                        <a href="{{ route('superadmin.posyandu.edit', $item->id_posyandu) }}"
                                            class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center hover:bg-amber-100 transition-all shadow-sm shadow-amber-100"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('superadmin.posyandu.destroy', $item->id_posyandu) }}"
                                            method="POST" onsubmit="return confirm('Hapus unit {{ $item->nama_posyandu }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-100 transition-all shadow-sm shadow-red-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h14" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-5xl mb-4">Empty</span>
                                        <p class="text-slate-400 font-medium italic">Belum ada data unit posyandu yang
                                            terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $posyandu->links() }}
        </div>
    </div>
@endsection