@extends('layouts.superadmin')

@section('title', isset($posyandu) ? 'Edit Unit - SIPANDA' : 'SIPANDA')

@section('content')
    <div class="max-w-4xl mx-auto"> 
        {{-- Header Section --}}
        <div class="mb-8">
            <p class="text-primary font-semibold uppercase tracking-wider text-xs mb-1">Administrasi Sistem</p>
            <h1 class="text-4xl font-bold text-slate-800">
                {{ isset($posyandu) ? 'Pembaruan Data Unit' : 'Pendaftaran Unit Posyandu Baru' }}
            </h1>
            <p class="text-slate-500 mt-2 italic text-sm">
                {{ isset($posyandu) ? 'Perbarui informasi unit layanan yang sudah terdaftar di sistem.' : 'Lengkapi formulir di bawah ini untuk mendaftarkan unit layanan baru.' }}
            </p>
        </div>

        {{-- Form Utama --}}
        <form action="{{ isset($posyandu) ? route('superadmin.posyandu.update', $posyandu->id_posyandu) : route('superadmin.posyandu.store') }}" 
              method="POST" class="space-y-8">
            
            @csrf
            @if(isset($posyandu))
                @method('PUT')
            @endif

            {{-- Card 1: Identitas Unit --}}
            <div class="bg-white p-8 rounded-xl3 border border-line shadow-card space-y-6">
                <h3 class="font-bold text-slate-800 flex items-center gap-3">
                    <span class="w-8 h-[2px] bg-primary"></span> IDENTITAS UNIT
                </h3>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Posyandu</label>
                    <input type="text" name="nama_posyandu" 
                           value="{{ old('nama_posyandu', $posyandu->nama_posyandu ?? '') }}"
                           placeholder="Contoh: Posyandu Melati I" required
                           class="w-full px-5 py-4 bg-softbg border-none rounded-2xl focus:ring-2 focus:ring-primary/20 outline-none text-slate-700 font-medium transition-all">
                </div>
            </div>

            {{-- Card 2: Lokasi & Wilayah --}}
            <div class="bg-white p-8 rounded-xl3 border border-line shadow-card space-y-6">
                <h3 class="font-bold text-slate-800 flex items-center gap-3">
                    <span class="w-8 h-[2px] bg-primary"></span> LOKASI & WILAYAH
                </h3>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kabupaten/Kota --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Kabupaten / Kota</label>
                            <input type="text" value="Indramayu" readonly
                                class="w-full px-5 py-4 bg-softbg border-none rounded-2xl text-slate-400 cursor-not-allowed font-semibold outline-none">
                        </div>

                        {{-- Kecamatan --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Kecamatan</label>
                            <div class="relative group">
                                <select id="kecamatan" name="kecamatan" required
                                    class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 appearance-none cursor-pointer focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300">
                                    <option value="" disabled selected>Pilih Kecamatan</option>
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Desa & Alamat --}}
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Desa / Kelurahan</label>
                            <div class="relative group">
                                <select id="desa" name="desa_kelurahan" required disabled
                                    class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 appearance-none cursor-pointer focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300 disabled:opacity-40">
                                    <option value="" disabled selected>Pilih Kecamatan Dulu</option>
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" placeholder="Contoh: Jl. Lohbener Lama No. 08, RT 01/RW 02" required
                                class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300 resize-none">{{ old('alamat', $posyandu->alamat ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Password Section --}}
                    <div class="pt-4 border-t border-line">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 block">
                            {{ isset($posyandu) ? 'Ganti Password (Kosongkan jika tidak ingin diubah)' : 'Password Akses Kader' }}
                        </label>
                        <input type="password" name="password_kader" id="password_kader"
                            {{ isset($posyandu) ? '' : 'required' }}
                            class="w-full px-5 py-4 bg-softbg border-none rounded-2xl focus:ring-2 focus:ring-primary/20 outline-none font-medium"
                            placeholder="Minimal 6 karakter">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end items-center gap-6 pt-4">
                <a href="{{ route('superadmin.posyandu.index') }}" class="text-slate-500 font-bold hover:text-slate-800 transition-colors">Batal</a>
                <button type="submit" class="bg-primary text-white px-10 py-4 rounded-full font-bold shadow-lg shadow-primary/20 hover:scale-105 transition-all flex items-center gap-3">
                    {{ isset($posyandu) ? 'Simpan Perubahan' : 'Daftarkan Unit' }}
                    <span class="text-lg">→</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Database Wilayah (Diringkas untuk efisiensi)
            const dataWilayah = {
                "Anjatan": ["Anjatan", "Anjatan Baru", "Anjatan Utara", "Bugis", "Bugis Tua", "Cilandak", "Cilandak Lor", "Kedungwungu", "Kopyah", "Lempuyang", "Mangunjaya", "Salamdarma", "Wanguk"],
                "Arahan": ["Arahan Kidul", "Arahan Lor", "Cidempet", "Linggajati", "Pranggong", "Sukadadi", "Sukasari", "Tawangsari"],
                "Balongan": ["Balongan", "Gelarmendala", "Majakerta", "Rawadalem", "Sudimampir", "Sudimampir Lor", "Sukareja", "Sukaurip", "Tegalsembrada", "Tegalurung"],
                "Cantigi": ["Cangkring", "Cantigi Kulon", "Cantigi Wetan", "Cemara", "Lamarantarung", "Panyingkiran Kidul", "Panyingkiran Lor"],
                "Lohbener": ["Bojongslawi", "Kiajaran Kulon", "Kiajaran Wetan", "Lanjan", "Langut", "Larangan", "Legok", "Lohbener", "Pamayahan", "Rambatan Kulon", "Sindangkerta", "Waru"],
                // Tambahkan kecamatan lainnya sesuai kebutuhan...
            };

            const kecamatanSelect = document.getElementById('kecamatan');
            const desaSelect = document.getElementById('desa');

            // 2. Mengambil data lama dari Laravel
            const oldKecamatan = "{{ old('kecamatan', $posyandu->kecamatan ?? '') }}";
            const oldDesa = "{{ old('desa_kelurahan', $posyandu->desa_kelurahan ?? '') }}";

            // 3. Fungsi Isi Kecamatan
            function populateKecamatan() {
                kecamatanSelect.innerHTML = '<option value="" disabled selected>Pilih Kecamatan</option>';
                Object.keys(dataWilayah).sort().forEach(kec => {
                    const option = document.createElement('option');
                    option.value = kec;
                    option.textContent = kec;
                    if (kec === oldKecamatan) option.selected = true;
                    kecamatanSelect.appendChild(option);
                });

                // Jika ada data kecamatan tersimpan, panggil fungsi pengisi desa
                if (oldKecamatan) {
                    updateDesa(oldKecamatan, oldDesa);
                }
            }

            // 4. Fungsi Isi Desa
            function updateDesa(kecamatan, selectedDesa = '') {
                const desaList = dataWilayah[kecamatan];
                desaSelect.innerHTML = '<option value="" disabled selected>Pilih Desa / Kelurahan</option>';

                if (desaList) {
                    desaSelect.disabled = false;
                    desaList.sort().forEach(desa => {
                        const option = document.createElement('option');
                        option.value = desa;
                        option.textContent = desa;
                        if (desa === selectedDesa) option.selected = true;
                        desaSelect.appendChild(option);
                    });
                } else {
                    desaSelect.disabled = true;
                }
            }

            // Jalankan fungsi awal
            populateKecamatan();

            // Event Listener perubahan kecamatan
            kecamatanSelect.addEventListener('change', function () {
                updateDesa(this.value);
            });
        });
    </script>
@endpush