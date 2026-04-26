@extends('layouts.superadmin')

@php
    $isEdit = isset($posyandu);
@endphp

@section('header_title', $isEdit ? 'Perbarui Unit Posyandu' : 'Pendaftaran Unit Posyandu')

@section('content')
<section class="registration-shell">
    <div class="registration-hero registration-hero-form">
        <div>
            <p class="registration-kicker">Administrasi Sistem</p>
            <h1 class="registration-title">
                {{ $isEdit ? 'Perbarui Data Unit Posyandu' : 'Pendaftaran Unit Posyandu Baru' }}
            </h1>
            <p class="registration-copy">
                Lengkapi formulir di bawah ini untuk
                {{ $isEdit ? 'memperbarui' : 'mendaftarkan' }}
                unit layanan Posyandu {{ $isEdit ? 'di' : 'ke dalam' }}
                sistem integrasi digital pusat.
            </p>
        </div>
    </div>

    <div class="registration-form-layout">
        <div class="registration-panel">
            <form
                action="{{ $isEdit ? route('superadmin.posyandu.update', $posyandu->id_posyandu) : route('superadmin.posyandu.store') }}"
                method="POST" class="unit-form-card">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="form-section-title">
                    <span></span>
                    <h3>Identitas Unit</h3>
                </div>

                <div class="form-grid form-grid-single">
                    <div class="field-block">
                        <label for="nama_posyandu">Nama Posyandu</label>
                        <input type="text" id="nama_posyandu" name="nama_posyandu"
                            value="{{ old('nama_posyandu', $posyandu->nama_posyandu ?? '') }}"
                            placeholder="Contoh: Posyandu Melati I" required>
                        @error('nama_posyandu')<small>{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="form-section-title">
                    <span></span>
                    <h3>Lokasi & Wilayah</h3>
                </div>

                <div class="form-grid form-grid-single">
                    <div class="field-block">
                        <label for="alamat">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="4" placeholder="Jl. Raya No. XX, RT/RW..."
                            required>{{ old('alamat', $posyandu->alamat ?? '') }}</textarea>
                        @error('alamat')<small>{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="form-grid form-grid-two">
                    <div class="field-block">
                        <label for="kecamatan">Kecamatan</label>
                        <input type="text" id="kecamatan" name="kecamatan"
                            value="{{ old('kecamatan', $posyandu->kecamatan ?? '') }}" placeholder="Nama Kecamatan">
                        @error('kecamatan')<small>{{ $message }}</small>@enderror
                    </div>

                    <div class="field-block">
                        <label for="kabupaten_kota">Kabupaten/Kota</label>
                        <input type="text" id="kabupaten_kota" name="kabupaten_kota"
                            value="{{ old('kabupaten_kota', $posyandu->kabupaten_kota ?? '') }}"
                            placeholder="Nama Kota">
                        @error('kabupaten_kota')<small>{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="form-section-title">
                    <span></span>
                    <h3>Penanggung Jawab</h3>
                </div>

                <div class="form-grid form-grid-single">
                    <div class="field-block">
                        <label for="nama_koordinator">Nama Koordinator</label>
                        <input type="text" id="nama_koordinator" name="nama_koordinator"
                            value="{{ old('nama_koordinator', $posyandu->nama_koordinator ?? '') }}"
                            placeholder="Nama Lengkap Beserta Gelar">
                        @error('nama_koordinator')<small>{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="form-grid form-grid-two">
                    <div class="field-block">
                        <label for="no_telp">Nomor Telepon</label>
                        <input type="text" id="no_telp" name="no_telp"
                            value="{{ old('no_telp', $posyandu->no_telp ?? '') }}" placeholder="08xx xxxx xxxx">
                        @error('no_telp')<small>{{ $message }}</small>@enderror
                    </div>

                    <div class="field-block">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $posyandu->email ?? '') }}"
                            placeholder="alamat@email.com">
                        @error('email')<small>{{ $message }}</small>@enderror
                    </div>
                </div>

                @if($isEdit)
                <div class="form-grid form-grid-single">
                    <div class="field-block">
                        <label for="status">Status Unit</label>
                        <select id="status" name="status">
                            @php($statusValue = old('status', $posyandu->status ?? 'Aktif'))
                            <option value="Aktif" {{ $statusValue === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ $statusValue === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                        </select>
                        @error('status')<small>{{ $message }}</small>@enderror
                    </div>
                </div>
                @endif

                <div class="form-footer">
                    <a href="{{ route('superadmin.posyandu.index') }}" class="form-cancel">Batal</a>
                    <button type="submit" class="primary-cta">
                        <span>{{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Unit' }}</span>
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M4.167 10h11.666m0 0-4.166-4.167M15.833 10l-4.166 4.167" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <aside class="registration-info-card">
            <h3>Informasi Penting</h3>
            <ul>
                <li>Pastikan identitas unit sesuai dengan data resmi dari Dinas Kesehatan setempat.</li>
                <li>Koordinasi pendaftaran memerlukan waktu verifikasi maksimal 2x24 jam kerja.</li>
                <li>Setelah unit aktif, Super Admin dapat menambahkan akun Bidan dan Kader dari menu direktori staf.
                </li>
            </ul>
        </aside>
    </div>
</section>
@endsection