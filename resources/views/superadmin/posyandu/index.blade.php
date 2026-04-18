@extends('layouts.superadmin')
@section('page-title', 'Manajemen Unit Posyandu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Unit Posyandu</h5>
        <p class="text-muted small mb-0">Kelola semua unit posyandu yang terdaftar</p>
    </div>
    <a href="{{ route('superadmin.posyandu.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Posyandu
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari nama posyandu..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('superadmin.posyandu.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset
                </a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Nama Posyandu</th>
                        <th>Wilayah</th>
                        <th>Alamat</th>
                        <th>No. Telp</th>
                        <th class="text-center">Bidan</th>
                        <th class="text-center">Kader</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posyandu as $p)
                    <tr>
                        <td class="px-4 fw-semibold">{{ $p->nama_posyandu }}</td>
                        <td>{{ $p->wilayah ?? '-' }}</td>
                        <td class="text-muted small">{{ Str::limit($p->alamat, 40) ?? '-' }}</td>
                        <td>{{ $p->no_telp ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $p->bidan_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info">
                                {{ $p->kader_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $p->status === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }} px-2 py-1">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('superadmin.posyandu.edit', $p->id_posyandu) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('superadmin.posyandu.destroy', $p->id_posyandu) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus posyandu ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-hospital fs-2 d-block mb-2 opacity-25"></i>
                            Belum ada data posyandu.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posyandu->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $posyandu->links() }}
        </div>
        @endif
    </div>
</div>
@endsection