@extends('layouts.superadmin')

@section('title', 'Detail Pengguna')

@section('content')
    <div class="space-y-5 pb-10">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-8 text-slate-800">
                    Detail Pengguna
                </h1>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Informasi akun Bidan yang terdaftar pada Posyandu-In.
                </p>
            </div>

            <a href="{{ route('superadmin.pengguna.index') }}"
                class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h12" />
                </svg>

                Kembali ke Daftar
            </a>
        </div>

        {{-- KARTU DETAIL PENGGUNA --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">

            {{-- IDENTITAS PENGGUNA --}}
            <div class="border-b border-slate-100 p-5 sm:p-7">
                <div class="flex items-center gap-4">

                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-xl font-bold text-[#155DFC]">
                        {{ strtoupper(substr(
        $pengguna_single->bidan?->nama_bidan
        ?? $pengguna_single->username
        ?? 'U',
        0,
        1
    )) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="break-words text-lg font-bold leading-7 text-slate-800">
                            {{ $pengguna_single->bidan?->nama_bidan
        ?? $pengguna_single->username }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Detail akun Bidan
                        </p>

                        <span
                            class="mt-2 inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                            {{ $pengguna_single->role }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- INFORMASI PENGGUNA --}}
            <div class="p-5 sm:p-7">
                <div class="mb-5">
                    <h3 class="text-base font-bold text-slate-800">
                        Informasi Akun
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Data identitas dan wilayah tugas Bidan.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    {{-- NAMA BIDAN --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <p class="text-xs font-medium text-slate-500">
                            Nama Bidan
                        </p>

                        <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-800">
                            {{ $pengguna_single->bidan?->nama_bidan ?? '-' }}
                        </p>
                    </div>

                    {{-- USERNAME --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <p class="text-xs font-medium text-slate-500">
                            Username
                        </p>

                        <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-800">
                            {{ $pengguna_single->username }}
                        </p>
                    </div>

                    {{-- POSYANDU --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <p class="text-xs font-medium text-slate-500">
                            Posyandu Pendamping
                        </p>

                        <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-800">
                            {{ $pengguna_single->posyandu?->nama_posyandu ?? 'Belum ditentukan' }}
                        </p>
                    </div>

                    {{-- NIP --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <p class="text-xs font-medium text-slate-500">
                            NIP Bidan
                        </p>

                        <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-800">
                            {{ $pengguna_single->bidan?->nip ?? '-' }}
                        </p>
                    </div>

                    {{-- NOMOR TELEPON --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4 sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500">
                            Nomor Telepon
                        </p>

                        <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-800">
                            {{ $pengguna_single->bidan?->no_telp ?? '-' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- FOOTER AKSI --}}
            <div
                class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-5 sm:flex-row sm:justify-end sm:px-7">

                <a href="{{ route('superadmin.pengguna.index') }}"
                    class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200">
                    Batal
                </a>

                <a href="{{ route('superadmin.pengguna.edit', $pengguna_single->id_user) }}"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[#155DFC] px-6 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 2.651 2.651M8 16l3.5-.5L19 8a2.121 2.121 0 0 0-3-3l-7.5 7.5L8 16Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                    </svg>

                    Edit Akun
                </a>

            </div>
        </div>
    </div>
@endsection