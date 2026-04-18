@extends('layouts.superadmin')
@section('page-title', 'Ringkasan Global Semua Posyandu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Ringkasan Global</h5>
        <p class="text-muted small mb-0">Laporan lintas posyandu dalam satu periode</p>
    </div>
</div>

{{-- Filter Periode --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Periode Awal</label>
                <input type="date" name="periode_awal" class="form-control"
                       value="{{ $periodeAwal }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Periode Akhir</label>
                <input type="date" name="periode_akhir" class="form-control"
                       value="{{ $periodeAkhir }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Ringkasan --}}
<div class="card">
    <div class="card-header py-3 px-4">
        <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
        Rekapitulasi Per Posyandu
        <span class="text-muted small ms-2">
            {{ \Carbon\Carbon::parse($periodeAwal)->format('d/m/Y') }} —
            {{ \Carbon\Carbon::parse($periodeAkhir)->format('d/m/Y') }}
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Posyandu</th>
                        <th>Wilayah</th>
                        <th class="text-center">Pemeriksaan</th>
                        <th class="text-center">Imunisasi</th>
                        <th class="text-center">Total Kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalPemeriksaan = 0; $totalImunisasi = 0; @endphp
                    @forelse($posyanduList as $p)
                    @php
                        $totalPemeriksaan += $p['pemeriksaan'];
                        $totalImunisasi   += $p['imunisasi'];
                    @endphp
                    <tr>
                        <td class="px-4 fw-semibold">{{ $p['nama_posyandu'] }}</td>
                        <td class="text-muted">{{ $p['wilayah'] ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold">
                                {{ $p['pemeriksaan'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold">
                                {{ $p['imunisasi'] }}
                            </span>
                        </td>
                        <td class="text-center fw-bold">
                            {{ $p['pemeriksaan'] + $p['imunisasi'] }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                            Tidak ada data untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($posyanduList->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="px-4" colspan="2">TOTAL</td>
                        <td class="text-center">{{ $totalPemeriksaan }}</td>
                        <td class="text-center">{{ $totalImunisasi }}</td>
                        <td class="text-center">{{ $totalPemeriksaan + $totalImunisasi }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection