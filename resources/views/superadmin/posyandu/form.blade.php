@extends('layouts.superadmin')

@section('title', isset($posyandu) ? 'Edit Unit - SIPANDA' : 'SIPANDA')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header Section --}}
        <div class="mb-8">
            <h1 class="text-[30px] leading-[38px] font-bold text-slate-800">
                {{ isset($posyandu) ? 'Pembaruan Data Unit' : 'Pendaftaran Unit Posyandu Baru' }}
            </h1>
            <p class="mt-2 text-[16px] leading-6 text-slate-500">
                {{ isset($posyandu) ? 'Perbarui informasi unit layanan yang sudah terdaftar di sistem.' : 'Lengkapi formulir di bawah ini untuk mendaftarkan unit layanan baru.' }}
            </p>
        </div>

        {{-- Form Utama --}}
        <form
            action="{{ isset($posyandu) ? route('superadmin.posyandu.update', $posyandu->id_posyandu) : route('superadmin.posyandu.store') }}"
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
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Kabupaten /
                                Kota</label>
                            <input type="text" value="Indramayu" readonly
                                class="w-full px-5 py-4 bg-softbg border-none rounded-2xl text-slate-400 cursor-not-allowed font-semibold outline-none">
                        </div>

                        {{-- Kecamatan --}}
                        <div class="space-y-2">
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Kecamatan</label>
                            <div class="relative group">
                                <select id="kecamatan" name="kecamatan" required
                                    class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 appearance-none cursor-pointer focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300">
                                    <option value="" disabled selected>Pilih Kecamatan</option>
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Desa & Alamat --}}
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Desa /
                                Kelurahan</label>
                            <div class="relative group">
                                <select id="desa" name="desa_kelurahan" required disabled
                                    class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 appearance-none cursor-pointer focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300 disabled:opacity-40">
                                    <option value="" disabled selected>Pilih Kecamatan Dulu</option>
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">Alamat
                                Lengkap</label>
                            <textarea name="alamat" rows="3" placeholder="Contoh: Jl. Lohbener Lama No. 08, RT 01/RW 02"
                                required
                                class="w-full px-5 py-4 bg-softbg border-2 border-transparent rounded-2xl text-slate-700 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300 resize-none">{{ old('alamat', $posyandu->alamat ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                Latitude
                            </label>
                            <input type="number" step="any" id="latitude" name="latitude"
                                value="{{ old('latitude', $posyandu->latitude ?? '') }}" placeholder="-6.40123779"
                                class="w-full px-5 py-4 bg-softbg rounded-2xl outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                Longitude
                            </label>
                            <input type="number" step="any" id="longitude" name="longitude"
                                value="{{ old('longitude', $posyandu->longitude ?? '') }}" placeholder="108.27668902"
                                class="w-full px-5 py-4 bg-softbg rounded-2xl outline-none">
                        </div>
                    </div>
                    {{-- Peta Lokasi Posyandu --}}
                    <div class="space-y-3">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px]">
                            Titik Lokasi Posyandu
                        </label>

                        <p class="text-sm text-slate-500">
                            Klik peta atau geser marker biru untuk menentukan lokasi Posyandu.
                        </p>

                        <div id="posyandu-map"
                            style="height: 350px; width: 100%; border-radius: 16px; overflow: hidden; position: relative; z-index: 0;">
                        </div>


                        <p id="map-status" class="text-sm text-slate-500">
                            Pilih titik lokasi pada peta.
                        </p>
                    </div>

                    {{-- Password Section --}}
                    {{-- Akun Akses Posyandu --}}
                    <div class="pt-4 border-t border-line space-y-4">

                        <div>
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 block">
                                Akun Akses Posyandu
                            </label>

                            <p class="text-sm text-slate-500">
                                Akun ini digunakan untuk login ke sistem oleh pengelola Posyandu.
                            </p>
                        </div>

                        {{-- Username --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">
                                Username
                            </label>

                            <input type="text" name="username"
                                value="{{ old('username', $posyandu->penggunaAkses->username ?? '') }}" {{ isset($posyandu) ? 'readonly' : 'required' }} placeholder="Contoh: posyandu_melati"
                                class="w-full px-5 py-4 bg-softbg border-none rounded-2xl focus:ring-2 focus:ring-primary/20 outline-none font-medium">
                        </div>

                        {{-- Password --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] ml-1">
                                {{ isset($posyandu) ? 'Password Baru' : 'Password' }}
                            </label>

                            <input type="password" name="password" id="password" {{ isset($posyandu) ? '' : 'required' }}
                                class="w-full px-5 py-4 bg-softbg border-none rounded-2xl focus:ring-2 focus:ring-primary/20 outline-none font-medium"
                                placeholder="Minimal 6 karakter">
                        </div>

                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end items-center gap-6 pt-4">
                <a href="{{ route('superadmin.posyandu.index') }}"
                    class="text-slate-500 font-bold hover:text-slate-800 transition-colors">Batal</a>
                <button type="submit"
                    class="bg-primary text-white px-10 py-4 rounded-full font-bold shadow-lg shadow-primary/20 hover:scale-105 transition-all flex items-center gap-3">
                    {{ isset($posyandu) ? 'Simpan Perubahan' : 'Daftarkan Unit' }}
                    <span class="text-lg">→</span>
                </button>
            </div>
        </form>
    </div>
@endsection


@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        #posyandu-map {
            display: block;
            width: 100%;
            height: 400px;
            min-height: 400px;
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            background: #e5e7eb;
            z-index: 0;
        }

        #posyandu-map .leaflet-tile {
            max-width: none !important;
            max-height: none !important;
            padding: 0 !important;
            border: 0 !important;
        }

        #posyandu-map img {
            max-width: none !important;
            max-height: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Database Wilayah (Diringkas untuk efisiensi)
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
                "Sliyeg": ["Gadingan", "Longok", "Majasari", "Majasih", "Mekargading", "Sleman", "Sleman Lor", "Sliyeg", "Sliyeg Lor", "Sudikampiran", "Tambi", "Tambi Lor", "Tugu", "Tuguk Kidul"],
                "Sukagumiwang": ["Bondan", "Cadangpinggan", "Cibeber", "Gedangan", "Gunungsari", "Sukagumiwang", "Tersana"],
                "Sukra": ["Bogor", "Karanglayung", "Sukra", "Sukra Wetan", "Sumuradem", "Sumuradem Timur", "Tegal Taman", "Ujunggebang"],
                "Terisi": ["Cibereng", "Cikawung", "Jatimulya", "Jatimunggul", "Karangasem", "Kendayakan", "Maanggungan", "Plosokerep", "Rajasinga"],
                "Tukdana": ["Bodas", "Cangko", "Gadel", "Karangkerta", "Kerticala", "Lajer", "Mekarsari", "Pagedangan", "Rancajawat", "Sukadana", "Sukamulya", "Sukaperna", "Tukdana"],
                "Widasari": ["Bangkaloa Ilir", "Bunder", "Kalensari", "Kasmaran", "Kongsijaya", "Leuwigede", "Ujungaris", "Ujungjaya", "Ujung pendok Jaya", "Widasari"],
            };

            const kecamatanSelect = document.getElementById('kecamatan');
            const desaSelect = document.getElementById('desa');

            // 2. Mengambil data lama dari Laravel
            const oldKecamatan = "{{ old('kecamatan', $posyandu->kecamatan ?? '') }}";
            const oldDesa = "{{ old('desa_kelurahan', $posyandu->desa_kelurahan ?? '') }}";

            // ===== PETA LOKASI POSYANDU =====
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const alamatInput = document.querySelector('[name="alamat"]');
            const mapStatus = document.getElementById('map-status');

            const defaultLocation = [-6.327, 108.32];
            const savedLat = parseFloat(latitudeInput.value);
            const savedLng = parseFloat(longitudeInput.value);

            const initialLocation =
                Number.isFinite(savedLat) && Number.isFinite(savedLng)
                    ? [savedLat, savedLng]
                    : defaultLocation;

            const map = L.map('posyandu-map').setView(initialLocation, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            let marker = null;
            let lookupTimer = null;
            let lookupController = null;

            function setMarker(lat, lng, reverseLookup = true) {
                latitudeInput.value = Number(lat).toFixed(8);
                longitudeInput.value = Number(lng).toFixed(8);

                if (!marker) {
                    marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map);

                    marker.on('dragend', function (event) {
                        const position = event.target.getLatLng();
                        setMarker(position.lat, position.lng);
                    });
                } else {
                    marker.setLatLng([lat, lng]);
                }

                if (reverseLookup) {
                    clearTimeout(lookupTimer);
                    lookupTimer = setTimeout(() => {
                        reverseGeocode(lat, lng);
                    }, 800);
                }
            }

            map.on('click', function (event) {
                setMarker(event.latlng.lat, event.latlng.lng);
            });

            async function reverseGeocode(lat, lng) {
                if (lookupController) {
                    lookupController.abort();
                }

                lookupController = new AbortController();
                mapStatus.textContent = 'Mencari alamat berdasarkan titik lokasi...';

                try {
                    const url = new URL(
                        'https://nominatim.openstreetmap.org/reverse'
                    );

                    url.search = new URLSearchParams({
                        format: 'jsonv2',
                        lat: lat,
                        lon: lng,
                        zoom: '18',
                        addressdetails: '1',
                        'accept-language': 'id'
                    });

                    const response = await fetch(url, {
                        signal: lookupController.signal,
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Layanan pencarian alamat gagal.');
                    }

                    const result = await response.json();

                    if (!result.address) {
                        throw new Error('Alamat tidak ditemukan.');
                    }

                    const a = result.address;

                    // Cari nama kecamatan dari hasil geocoding
                    const namaKecamatan =
                        a.county || a.city_district || a.state_district || '';

                    const kecamatanCocok = Object.keys(dataWilayah).find(k =>
                        k.toLowerCase() === namaKecamatan.toLowerCase()
                    );

                    if (kecamatanCocok) {
                        kecamatanSelect.value = kecamatanCocok;

                        const namaDesa =
                            a.village || a.suburb || a.neighbourhood ||
                            a.town || a.city || '';

                        updateDesa(kecamatanCocok);

                        const desaCocok = [...desaSelect.options].find(option =>
                            option.value.toLowerCase() === namaDesa.toLowerCase()
                        );

                        if (desaCocok) {
                            desaSelect.value = desaCocok.value;
                        }
                    }

                    // Isi alamat berdasarkan hasil pencarian
                    if (result.display_name) {
                        alamatInput.value = result.display_name;
                    }

                    mapStatus.textContent =
                        'Alamat ditemukan. Periksa kembali kecamatan, desa, dan alamat sebelum menyimpan.';

                } catch (error) {
                    if (error.name === 'AbortError') return;

                    console.error(error);
                    mapStatus.textContent =
                        'Alamat otomatis tidak tersedia. Silakan isi alamat secara manual.';
                }
            }

            // Tampilkan marker pada koordinat lama ketika form edit dibuka
            if (
                Number.isFinite(savedLat) &&
                Number.isFinite(savedLng)
            ) {
                setMarker(savedLat, savedLng, false);
            }

            function fixMapSize() {
                map.invalidateSize(true);
            }

            window.addEventListener('load', fixMapSize);

            setTimeout(fixMapSize, 300);
            setTimeout(fixMapSize, 1000);

            window.addEventListener('resize', fixMapSize);


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