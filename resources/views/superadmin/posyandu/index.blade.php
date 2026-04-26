@extends('layouts.superadmin')

@section('header_title', 'Registrasi Unit Posyandu')

@section('content')
    <section class="registration-shell">
        <div class="registration-hero">
            <div>
                <p class="registration-kicker">Administrasi Sistem</p>
                <h1 class="registration-title">Daftar Unit Posyandu</h1>
                <p class="registration-copy">Kelola seluruh unit Posyandu yang terdaftar dan lanjutkan proses registrasi
                    unit baru dari dashboard Super Admin.</p>
            </div>
            <a href="{{ route('superadmin.posyandu.create') }}" class="primary-cta">
                <span>Registrasi Unit Baru</span>
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M4.167 10h11.666m0 0-4.166-4.167M15.833 10l-4.166 4.167" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>

        <div class="registration-stats">
            <article class="metric-card">
                <span>Total Unit Posyandu</span>
                <strong>{{ $summary['total_unit'] }}</strong>
                <p>Unit Posyandu terdaftar.</p>
            </article>
        </div>

        <div class="registration-toolbar">
            <form method="GET" class="registration-filter">
                <div class="search-field">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M17.5 17.5l-3.625-3.625m1.958-4.167a6.125 6.125 0 11-12.25 0 6.125 6.125 0 0112.25 0z"
                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama unit atau alamat...">
                </div>
                <button type="submit" class="toolbar-button">Cari Unit</button>
                @if(request()->filled('search'))
                    <a href="{{ route('superadmin.posyandu.index') }}" class="toolbar-link">Reset</a>
                @endif
            </form>
        </div>

        <div class="unit-grid">
            @forelse($posyandu as $item)
                <article class="unit-card">
                    <div class="unit-card-head">
                        <div>
                            {{-- Baris kategori dihapus karena sistem hanya untuk Posyandu Balita --}}
                            <h3 style="margin-top: 0.5rem;">{{ $item->nama_posyandu }}</h3>
                        </div>
                    </div>

                    <p class="unit-address" style="margin-top: 1rem;">{{ $item->alamat }}</p>

                    <div class="unit-card-footer">
                        <div class="unit-mini-stats">
                            <span>{{ $item->bidan_count }} Bidan</span>
                            <span>{{ $item->kader_count }} Kader</span>
                        </div>

                        <div class="unit-actions">
                            <a href="{{ route('superadmin.posyandu.edit', $item->id_posyandu) }}"
                                class="action-link action-link-edit">Edit</a>
                            <form action="{{ route('superadmin.posyandu.destroy', $item->id_posyandu) }}" method="POST"
                                onsubmit="return confirm('Hapus unit posyandu ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link action-link-delete">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="unit-empty-state">
                    <h3>Belum ada unit Posyandu terdaftar</h3>
                    <p>Mulai proses registrasi unit baru agar Super Admin dapat mengelola layanan Posyandu secara terpusat.</p>
                    <a href="{{ route('superadmin.posyandu.create') }}" class="primary-cta primary-cta-inline">
                        <span>Daftarkan Unit Pertama</span>
                    </a>
                </div>
            @endforelse
        </div>

        @if($posyandu->hasPages())
            <div class="unit-pagination">
                {{ $posyandu->links() }}
            </div>
        @endif
    </section>
@endsection