@extends('layouts.superadmin')
@section('page-title', 'Dashboard Global')

@section('content')
<div class="row g-3 mb-4">
    {{-- Stat Cards --}}
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon" style="background:#e8f4fb;">
                    <i class="bi bi-hospital-fill text-primary"></i>
                </div>
                <div>
                    <div class="value">{{ $stats['total_posyandu'] }}</div>
                    <div class="label">Unit Posyandu</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon" style="background:#e8faf0;">
                    <i class="bi bi-emoji-smile-fill text-success"></i>
                </div>
                <div>
                    <div class="value">{{ $stats['total_anak'] }}</div>
                    <div class="label">Total Balita</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon" style="background:#fff3e0;">
                    <i class="bi bi-clipboard2-pulse-fill text-warning"></i>
                </div>
                <div>
                    <div class="value">{{ $stats['total_pemeriksaan'] }}</div>
                    <div class="label">Pemeriksaan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon" style="background:#fdeaea;">
                    <i class="bi bi-syringe-fill text-danger"></i>
                </div>
                <div>
                    <div class="value">{{ $stats['total_imunisasi'] }}</div>
                    <div class="label">Imunisasi Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sub stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon" style="background:#f0e8fb;">
                <i class="bi bi-person-badge-fill text-purple" style="color:#8e44ad;"></i>
            </div>
            <div>
                <div class="value" style="font-size:22px;">{{ $stats['total_bidan'] }}</div>
                <div class="label">Total Bidan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon" style="background:#e8f8fb;">
                <i class="bi bi-person-fill-gear text-info"></i>
            </div>
            <div>
                <div class="value" style="font-size:22px;">{{ $stats['total_kader'] }}</div>
                <div class="label">Total Kader</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon" style="background:#e8fbf0;">
                <i class="bi bi-people-fill text-success"></i>
            </div>
            <div>
                <div class="value" style="font-size:22px;">{{ $stats['total_pengguna'] }}</div>
                <div class="label">Total Pengguna</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel per posyandu --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
        <span><i class="bi bi-table me-2 text-primary"></i>Ringkasan Per Posyandu</span>
        <a href="{{ route('superadmin.posyandu.index') }}" class="btn btn-sm btn-outline-primary">
            Kelola Posyandu
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Nama Posyandu</th>
                        <th>Wilayah</th>
                        <th class="text-center">Bidan</th>
                        <th class="text-center">Kader</th>
                        <th class="text-center">Pemeriksaan Bulan Ini</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posyanduList as $p)
                    <tr>
                        <td class="px-4 fw-semibold">{{ $p['nama_posyandu'] }}</td>
                        <td class="text-muted">{{ $p['wilayah'] ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $p['total_bidan'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info">
                                {{ $p['total_kader'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                {{ $p['pemeriksaan_bulan'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('superadmin.posyandu.edit', $p['id_posyandu']) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Belum ada data posyandu.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection