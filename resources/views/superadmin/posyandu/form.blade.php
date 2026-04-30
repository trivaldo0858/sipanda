@extends('layouts.superadmin')

@section('title', 'Registrasi Unit')

@section('content')
    <div class="max-w-5xl">
        <div class="mb-8">
            <p class="text-primary font-semibold uppercase tracking-wider text-xs mb-1">Administrasi Sistem</p>
            <h1 class="text-4xl font-bold text-slate-800">Pendaftaran Unit Posyandu Baru</h1>
            <p class="text-slate-500 mt-2">Lengkapi formulir di bawah ini untuk mendaftarkan unit layanan baru ke dalam
                sistem.</p>
        </div>

        <form action="{{ route('superadmin.posyandu.store') }}" method="POST" class="grid grid-cols-12 gap-8">
            @csrf
            <div class="col-span-8 space-y-6">
                {{-- Identitas Unit --}}
                <div class="bg-white p-8 rounded-xl3 border border-line shadow-card space-y-6">
                    <h3 class="font-bold text-slate-800 flex items-center gap-3">
                        <span class="w-8 h-[2px] bg-primary"></span> IDENTITAS UNIT
                    </h3>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Posyandu</label>
                        <input type="text" name="nama_posyandu" placeholder="Contoh: Posyandu Melati I" required
                            class="w-full px-5 py-4 bg-softbg border-none rounded-2xl focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                {{-- Bagian Lokasi & Wilayah--}}
                <div class="bg-white p-8 rounded-xl3 border border-line shadow-card space-y-6">
                    <h3 class="font-bold text-slate-800 flex items-center gap-3">
                        <span class="w-8 h-[2px] bg-primary text-primary-500"></span> LOKASI & WILAYAH
                    </h3>

                    <div class="flex flex-col gap-6">
                        {{-- 1. Kabupaten/Kota --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Kabupaten /
                                Kota</label>
                            <div class="relative">
                                <input type="text" name="kabupaten" value="Indramayu" readonly
                                    class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-500 cursor-not-allowed font-semibold focus:outline-none transition-all duration-300">
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 italic text-xs">Lokasi
                                    Tetap</span>
                            </div>
                        </div>

                        {{-- 2. Kecamatan (Modern Dropdown) --}}
                        <div class="space-y-2">
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Kecamatan</label>
                            <div class="relative group">
                                <select id="kecamatan" name="kecamatan" required
                                    class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 appearance-none cursor-pointer focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300">
                                </select>
                                {{-- Custom Arrow Icon --}}
                                <div
                                    class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Desa / Kelurahan (Modern Dropdown) --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Desa /
                                Kelurahan</label>
                            <div class="relative group">
                                <select id="desa" name="desa_kelurahan" required disabled
                                    class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 appearance-none cursor-pointer focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed">
                                    <option value="">Pilih Kecamatan Dulu</option>
                                </select>
                                {{-- Custom Arrow Icon --}}
                                <div
                                    class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Alamat Lengkap --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Alamat
                                Lengkap</label>
                            <textarea name="alamat" rows="3" placeholder="Contoh: Jl. Lohbener Lama No. 08, RT 01/RW 02"
                                class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300 resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <script>
                    // Data Wilayah Indramayu Lengkap (Sesuai Referensi)
                    const dataWilayah = {
                        "Anjatan": ["Anjatan", "Anjatan Baru", "Anjatan Utara", "Bugis", "Bugis Tua", "Cilandak", "Cilandak Lor", "Kedungwungu", "Kopyah", "Lempuyang", "Mangunjaya", "Salamdarma", "Wanguk"],
                        "Arahan": ["Arahan Kidul", "Arahan Lor", "Cidempet", "Linggajati", "Pranggong", "Sukadadi", "Sukasari", "Tawangsari"],
                        "Balongan": ["Balongan", "Gelarmendala", "Majakerta", "Rawadalem", "Sudimampir", "Sudimampir Lor", "Sukareja", "Sukaurip", "Tegalsembrada", "Tegalurung"],
                        "Bangodua": ["Bangodua", "Beduyut", "Karanggetas", "Malangsari", "Mulyasari", "Rancasari", "Tegalgirang", "Wanasari"],
                        "Bongas": ["Bongas", "Cipaat", "Cipedang", "Kertajaya", "Kertamulya", "Margamulya", "Plawangan", "Sidamulya"],
                        "Cantigi": ["Cangkring", "Cantigi Kulon", "Cantigi Wetan", "Cemara", "Lamarantarung", "Panyingkiran Kidul", "Panyingkiran Lor"],
                        "Cikedung": ["Amis", "Cikedung", "Cikedung Lor", "Jambak", "Jatisura", "Loyang", "Mundakjaya"],
                        "Gabuswetan": ["Babakanjaya", "Drunten Kulon", "Drunten Wetan", "Gabuskulon", "Gabuswetan", "Kedokangabus", "Kedungdawa", "Rancahan", "Rancamulya", "Sekarmulya"],
                        "Gantar": ["Baleraja", "Bantarwaru", "Gantar", "Mekarjaya", "Mekarwaru", "Sanca", "Situraja"],
                        "Haurgeulis": ["Cipancuh", "Haurgeulis", "Haurkolot", "Karangtumaritis", "Kertanegara", "Mekarjati", "Sidadadi", "Sukajati", "Sumbermulya", "Wanakaya"],
                        "Indramayu": ["Dukuh", "Karangsong", "Pabeanudik", "Pekandangan", "Pekandangan Jaya", "Plumbon", "Singajaya", "Singaraja", "Tambak", "Telukagung", "Bojongsari", "Karanganyar", "Karangmalang", "Kepandean", "Lemahabang", "Lemahmekar", "Margadadi", "Paoman"],
                        "Jatibarang": ["Bulak", "Bulak Lor", "Jatibarang", "Jatibarang Baru", "Jatisawit", "Jatisawit Lor", "Kalimati", "Kebulen", "Krasak", "Lobener", "Lobener Lor", "Malang Semirang", "Pawidean", "Pilangsari", "Sukalila"],
                        "Juntinyuat": ["Dadap", "Juntikebon", "Juntinyuat", "Juntiweden", "Limbangan", "Pondoh", "Sambimaya", "Segeran", "Segeran Kidul", "Tinumpuk"],
                        "Kandanghaur": ["Bulak", "Curug", "Eretan Kulon", "Eretan Wetan", "Ilir", "Karanganyar", "Karangmulya", "Kertawinangun", "Pareangirang", "Pranti", "Soge", "Wirakanan", "Wirapanjunan"],
                        "Karangampel": ["Benda", "Dukuh Jeruk", "Dukuh Tengah", "Mundu", "Kaplongan Lor", "Karangampel Kidul", "Karangampel", "Pringgacala", "Sendang", "Tanjungpura", "Tanjungsari"],
                        "Kedokan Bunder": ["Cangkingan", "Jayalaksana", "Jayawinangun", "Kaplongan", "Kedokan Agung", "Kedokan Bunder", "Kedokan Bunder Wetan"],
                        "Kertasemaya": ["Jambe", "Jengkok", "Kertasmaya", "Kliwed", "Lemahayu", "Manguntara", "Sukawera", "Tenajar", "Tenajar Kidul", "Tenajar Lor", "Tulungagung"],
                        "Krangkeng": ["Dukuh Jati", "Kalianyar", "Kapringan", "Kedungwungu", "Krangkeng", "Luwunggesik", "Purwajaya", "Singakerta", "Srengseng", "Tanjakan", "Tegalmulya"],
                        "Kroya": ["Jayamulya", "Kroya", "Sukamelang", "Sukaslamet", "Sumberjaya", "Sumbon", "Tanjungkerta", "Temiyang", "Temiyangsari"],
                        "Lelea": ["Cempeh", "Langgengsari", "Lelea", "Nunuk", "Pangauban", "Tamansari", "Telagasari", "Tempel", "Tempelkulon", "Tugu", "Tunggulpayung"],
                        "Lohbener": ["Bojongslawi", "Kiajaran Kulon", "Kiajaran Wetan", "Lanjan", "Langut", "Larangan", "Legok", "Lohbener", "Pamayahan", "Rambatan Kulon", "Sindangkerta", "Waru"],
                        "Losarang": ["Cemara Kulon", "Jangga", "Jumbleng", "Krimun", "Losarang", "Muntur", "Pangkalan", "Pegagan", "Puntang", "Rajaiyang", "Santing", "Ranjeng"],
                        "Pasekan": ["Brondong", "Karanganyar", "Pasekan", "Pabean Ilir", "Pagirikan", "Totoran"],
                        "Patrol": ["Arjasari", "Bugel", "Limpas", "Mekarsari", "Patrol", "Patrol Baru", "Patrol Lor", "Sukahaji"],
                        "Sindang": ["Babadan", "Dermayu", "Kenanga", "Panyindangan Kulon", "Panyindangan Wetan", "Penganjang", "Rambatan Wetan", "Sindang", "Terusan", "Wanantara"],
                        "Sliyeg": ["Gadingan", "Longok", "Majasari", "Majasih", "Mekargading", "Sleman", "Sleman Lor" "Sliyeg", "Sliyeg Lor", "Sudikampiran", "Tambi", "Tambi Lor", "Tugu", "Tuguk Kidul"],
                        "Sukagumiwang": ["Bondan", "Cadangpinggan", "Cibeber", "Gedangan", "Gunungsari", "Sukagumiwang", "Tersana"],
                        "Sukra": ["Bogor", "Karanglayung", "Sukra", "Sukra Wetan", "Sumuradem", "Sumuradem Timur", "Tegal Taman", "Ujunggebang"],
                        "Terisi": ["Cibereng", "Cikawung", "Jatimulya", "Jatimunggul", "Karangasem", "Kendayakan", "Maanggungan", "Plosokerep", "Rajasinga"],
                        "Tukdana": ["Bodas", "Cangko", "Gadel", "Karangkerta", "Kerticala", "Lajer", "Mekarsari", "Pagedangan", "Rancajawat", "Sukadana", "Sukamulya", "Sukaperna", "Tukdana"],
                        "Widasari": ["Bangkaloa Ilir", "Bunder", "Kalensari" "Kasmaran", "Kongsijaya", "Leuwigede", "Ujungaris", "Ujungjaya", "Ujung pendok Jaya " "Widasari"],
                    };

                    const kecamatanSelect = document.getElementById('kecamatan');
                    const desaSelect = document.getElementById('desa');

                    // Update Dropdown Kecamatan
                    kecamatanSelect.innerHTML = '<option value="" disabled selected>Pilih Kecamatan</option>';
                    Object.keys(dataWilayah).sort().forEach(kec => {
                        const option = document.createElement('option');
                        option.value = kec;
                        option.textContent = kec;
                        kecamatanSelect.appendChild(option);
                    });

                    kecamatanSelect.addEventListener('change', function () {
                        const desaList = dataWilayah[this.value];
                        desaSelect.innerHTML = '<option value="" disabled selected>Pilih Desa / Kelurahan</option>';

                        if (desaList) {
                            desaSelect.disabled = false;
                            desaList.sort().forEach(desa => {
                                const option = document.createElement('option');
                                option.value = desa;
                                option.textContent = desa;
                                desaSelect.appendChild(option);
                            });
                        } else {
                            desaSelect.disabled = true;
                            desaSelect.innerHTML = '<option value="">Pilih Kecamatan Dulu</option>';
                        }
                    });
                </script>

                <div class="flex justify-end items-center gap-6">
                    <a href="{{ route('superadmin.posyandu.index') }}"
                        class="text-slate-500 font-semibold hover:text-slate-800">Batal</a>
                    <button type="submit"
                        class="bg-primary text-white px-10 py-4 rounded-full font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-3">
                        Daftarkan Unit <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Sidebar Informasi --}}
            <div class="col-span-4">
                <div class="bg-primary/5 border border-primary/10 p-6 rounded-xl3 space-y-4">
                    <h4 class="font-bold text-primary text-sm uppercase tracking-wider">Informasi Penting</h4>
                    <div class="flex gap-4">
                        <span class="text-primary text-xl">✔️</span>
                        <p class="text-xs text-slate-600 leading-relaxed">Pastikan ID Unit sesuai dengan data resmi dari
                            Dinas Kesehatan setempat.</p>
                    </div>
                    <div class="flex gap-4">
                        <span class="text-primary text-xl">ℹ️</span>
                        <p class="text-xs text-slate-600 leading-relaxed">Koordinasi pendaftaran memerlukan waktu verifikasi
                            maksimal 2x24 jam kerja.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection