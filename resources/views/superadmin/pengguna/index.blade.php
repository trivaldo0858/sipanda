@extends('layouts.superadmin')
@section('page-title', 'Manajemen Akun Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Akun Pengguna</h5>
        <p class="text-muted small mb-0">Kelola semua akun Bidan, Kader, dan Orang Tua</p>
    </div>
    <a href="{{ route('superadmin.pengguna.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm"
                   style="max-width:220px"
                   placeholder="Cari username..." value="{{ request('search') }}">
            <select name="role" class="form-select form-select-sm" style="max-width:150px">
                <option value="">Semua Role</option>
                <option value="Bidan"    {{ request('role') === 'Bidan'    ? 'selected' : '' }}>Bidan</option>
                <option value="Kader"    {{ request('role') === 'Kader'    ? 'selected' : '' }}>Kader</option>
                <option value="OrangTua" {{ request('role') === 'OrangTua' ? 'selected' : '' }}>Orang Tua</option>
            </select>
            <select name="posyandu" class="form-select form-select-sm" style="max-width:200px">
                <option value="">Semua Posyandu</option>
                @foreach($posyanduList as $p)
                    <option value="{{ $p->id_posyandu }}"
                        {{ request('posyandu') == $p->id_posyandu ? 'selected' : '' }}>
                        {{ $p->nama_posyandu }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="bi bi-search"></i>
            </button>
            @if(request()->anyFilled(['search', 'role', 'posyandu']))
                <a href="{{ route('superadmin.pengguna.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <th class="px-4">Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Posyandu</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengguna as $p)
                    <tr>
                        <td class="px-4 fw-semibold">{{ $p->username }}</td>
                        <td>
                            @if($p->isBidan())    {{ $p->bidan->nama_bidan ?? '-' }}
                            @elseif($p->isKader()) {{ $p->kader->nama_kader ?? '-' }}
                            @else                  {{ $p->orangTua->nama_ibu ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @php
                                $roleColor = match($p->role) {
                                    'Bidan'    => 'danger',
                                    'Kader'    => 'info',
                                    'OrangTua' => 'success',
                                    default    => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }}">
                                {{ $p->role }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $p->posyandu->nama_posyandu ?? '-' }}
                        </td>
                        <td class="text-muted small">
                            {{ $p->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('superadmin.pengguna.edit', $p->id_user) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('superadmin.pengguna.destroy', $p->id_user) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus pengguna {{ $p->username }}?')">
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
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                            Belum ada data pengguna.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengguna->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $pengguna->links() }}
        </div>
        @endif
    </div>
</div>
@endsection