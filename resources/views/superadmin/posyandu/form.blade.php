@extends('layouts.superadmin')
@section('page-title', isset($posyandu) ? 'Edit Posyandu' : 'Tambah Posyandu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('superadmin.posyandu.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0">
                    {{ isset($posyandu) ? 'Edit Posyandu' : 'Tambah Posyandu Baru' }}
                </h5>
                <p class="text-muted small mb-0">
                    {{ isset($posyandu) ? 'Perbarui data unit posyandu' : 'Daftarkan unit posyandu baru' }}
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <form action="{{ isset($posyandu)
                    ? route('superadmin.posyandu.update', $posyandu->id_posyandu)
                    : route('superadmin.posyandu.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($posyandu)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Posyandu <span class="text-danger">*</span></label>
                        <input type="text" name="nama_posyandu" class="form-control @error('nama_posyandu') is-invalid @enderror"
                               value="{{ old('nama_posyandu', $posyandu->nama_posyandu ?? '') }}"
                               placeholder="Contoh: Posyandu Mawar RW 03" required>
                        @error('nama_posyandu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wilayah</label>
                        <input type="text" name="wilayah" class="form-control"
                               value="{{ old('wilayah', $posyandu->wilayah ?? '') }}"
                               placeholder="Contoh: RW 03 Desa Sukamaju">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"
                                  placeholder="Alamat lengkap posyandu">{{ old('alamat', $posyandu->alamat ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. Telepon</label>
                        <input type="text" name="no_telp" class="form-control"
                               value="{{ old('no_telp', $posyandu->no_telp ?? '') }}"
                               placeholder="Contoh: 081234567890">
                    </div>

                    @if(isset($posyandu))
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="Aktif"       {{ ($posyandu->status ?? 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ ($posyandu->status ?? '') === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    @endif

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('superadmin.posyandu.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>
                            {{ isset($posyandu) ? 'Simpan Perubahan' : 'Tambah Posyandu' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection