@extends('layouts.posyandu')

@section('title', 'Kelola Data Anak')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">
                    Kelola Data Anak
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Daftar anak yang terdaftar di Posyandu Anda.
                </p>
            </div>

            <a href="{{ route('posyandu.anak.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 font-semibold text-white shadow-sm transition hover:bg-blue-700">
                + Daftarkan Anak
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">NIK Anak</th>
                            <th class="px-5 py-4">Nama Anak</th>
                            <th class="px-5 py-4">Tanggal Lahir</th>
                            <th class="px-5 py-4">Jenis Kelamin</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($anak as $item)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-4 text-slate-500">
                                    {{ $anak->firstItem() + $loop->index }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-700">
                                    {{ $item->nik_anak }}
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    {{ $item->nama_anak }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->tgl_lahir)->format('d-m-Y') }}
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $item->jenis_kelamin_label }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <p class="font-semibold text-slate-700">
                                        Belum ada data anak
                                    </p>
                                    <p class="mt-1 text-slate-500">
                                        Daftarkan anak untuk mulai mengisi data Posyandu.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($anak->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $anak->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection