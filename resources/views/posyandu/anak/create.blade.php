@extends('layouts.posyandu')

@section('title', 'Pendaftaran Anak')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <a href="{{ route('posyandu.anak.index') }}" class="text-sm font-semibold text-primary hover:underline">
                &larr; Kembali ke Daftar Anak
            </a>

            <h1 class="mt-4 text-xl font-bold text-slate-800">
                Pendaftaran Anak
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Lengkapi data anak sesuai dokumen yang dimiliki orang tua.
            </p>
        </div>

        <form action="{{ route('posyandu.anak.store') }}" method="POST"
            class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-card md:p-7">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-semibold">
                        Periksa kembali data berikut:
                    </p>
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <h2 class="font-bold text-slate-800">Data Anak</h2>
                <p class="mt-1 text-sm text-slate-500">
                    NIK anak digunakan untuk login Orang Tua bersama tanggal lahir anak.
                </p>
            </div>

            <div class="space-y-2">
                <label for="nik_anak" class="block font-semibold text-slate-700">
                    NIK Anak
                </label>
                <input id="nik_anak" name="nik_anak" type="text" inputmode="numeric" maxlength="16" minlength="16"
                    pattern="[0-9]{16}" required value="{{ old('nik_anak') }}" placeholder="Masukkan 16 digit NIK"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-primary focus:ring-2 focus:ring-blue-100">
                <p class="text-xs text-slate-500">
                    Masukkan 16 digit angka tanpa spasi.
                </p>
            </div>

            <div class="space-y-2">
                <label for="nama_anak" class="block font-semibold text-slate-700">
                    Nama Anak
                </label>
                <input id="nama_anak" name="nama_anak" type="text" required maxlength="100" value="{{ old('nama_anak') }}"
                    placeholder="Nama lengkap anak"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-primary focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="tgl_lahir" class="block font-semibold text-slate-700">
                        Tanggal Lahir
                    </label>
                    <input id="tgl_lahir" name="tgl_lahir" type="date" max="{{ date('Y-m-d') }}" required
                        value="{{ old('tgl_lahir') }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-primary focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="space-y-2">
                    <label for="jenis_kelamin" class="block font-semibold text-slate-700">
                        Jenis Kelamin
                    </label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-primary focus:ring-2 focus:ring-blue-100">
                        <option value="">Pilih jenis kelamin</option>
                        <option value="L" @selected(old('jenis_kelamin') === 'L')>
                            Laki-laki
                        </option>
                        <option value="P" @selected(old('jenis_kelamin') === 'P')>
                            Perempuan
                        </option>
                    </select>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5">
                <h2 class="font-bold text-slate-800">Data Orang Tua</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Data ibu disimpan bersama data anak.
                </p>
            </div>

            <div class="space-y-2">
                <label for="nama_ibu" class="block font-semibold text-slate-700">
                    Nama Ibu
                </label>
                <input id="nama_ibu" name="nama_ibu" type="text" maxlength="100" required value="{{ old('nama_ibu') }}"
                    placeholder="Nama lengkap ibu"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-primary focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="space-y-2">
                <label for="nama_ayah" class="block font-semibold text-slate-700">
                    Nama Ayah <span class="font-normal text-slate-400">(Opsional)</span>
                </label>
                <input id="nama_ayah" name="nama_ayah" type="text" maxlength="100" value="{{ old('nama_ayah') }}"
                    placeholder="Nama lengkap ayah"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-primary focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('posyandu.anak.index') }}"
                    class="rounded-xl border border-slate-300 px-5 py-2.5 font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                    class="rounded-xl bg-primary px-5 py-2.5 font-semibold text-white transition hover:bg-blue-700">
                    Simpan Data Anak
                </button>
            </div>
        </form>
    </div>
@endsection