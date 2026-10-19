@extends('layouts.superadmin')

@section('title', 'Detail Pengguna')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Detail Pengguna
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi akun Bidan yang terdaftar.
                </p>
            </div>

            <a href="{{ route('superadmin.pengguna.index') }}" class="rounded-full border border-slate-200 px-5 py-2.5
                          text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Kembali
            </a>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">

            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center
                                rounded-2xl bg-blue-50 text-xl font-bold text-blue-600">
                    {{ strtoupper(substr(
        $pengguna_single->bidan->nama_bidan
        ?? $pengguna_single->username,
        0,
        1
    )) }}
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        {{ $pengguna_single->bidan->nama_bidan
        ?? $pengguna_single->username }}
                    </h2>

                    <p class="text-sm text-slate-500">
                        Detail akun Bidan
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <p class="text-sm text-slate-500">Nama Bidan</p>
                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $pengguna_single->bidan->nama_bidan ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Username</p>
                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $pengguna_single->username }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Role</p>
                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $pengguna_single->role }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Posyandu Pendamping</p>
                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $pengguna_single->posyandu->nama_posyandu
        ?? 'Belum ditentukan' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">NIP</p>
                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $pengguna_single->bidan->nip ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Nomor Telepon</p>
                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $pengguna_single->bidan->no_telp ?? '-' }}
                    </p>
                </div>

            </div>

            <div class="mt-8 border-t border-slate-100 pt-5">
                <a href="{{ route(
        'superadmin.pengguna.edit',
        $pengguna_single->id_user
    ) }}" class="inline-flex rounded-full bg-blue-600 px-5 py-2.5
                              text-sm font-semibold text-white hover:bg-blue-700">
                    Edit Akun
                </a>
            </div>

        </div>
    </div>
@endsection