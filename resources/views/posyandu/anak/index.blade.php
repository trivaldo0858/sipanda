@extends('layouts.posyandu')

@section('title', 'Kelola Data Anak')

@section('content')
    <div class="space-y-6 lg:space-y-7">
        <div class="flex flex-col gap-4 rounded-xl border border-[#E2E8F0] bg-white px-5 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[#64748B]">Data Kependudukan</p>
                <h1 class="mt-1 text-2xl font-extrabold text-[#0F172A]">Kelola Data Anak</h1>
            </div>
            <button id="open-anak-modal" type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#155DFC]/90 focus:outline-none focus:ring-2 focus:ring-[#155DFC] focus:ring-offset-2">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                <span>Mendaftarkan Anak Baru</span>
            </button>
        </div>

        @if (session('success'))
            <div role="status" class="flex items-center gap-3 rounded-lg border border-[#16A34A] bg-[#F8FAFC] px-4 py-3 text-sm font-medium text-[#16A34A]">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto pb-2">
            <section class="grid min-w-[900px] grid-cols-4 gap-4" aria-label="Ringkasan data balita">
            <article class="min-h-36 rounded-xl border border-[#E2E8F0] border-t-2 border-t-[#155DFC] bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-[#64748B]">Total Balita</p>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-children" aria-hidden="true"></i></span>
                </div>
                <p class="mt-4 text-2xl font-extrabold text-[#0F172A]">{{ number_format($statistik['total']) }} <span class="text-sm font-semibold text-[#64748B]">Anak</span></p>
                <p class="mt-1 text-xs text-[#64748B]">{{ $statistik['total'] > 0 ? 'Data anak terdaftar' : 'Belum ada data masuk' }}</p>
            </article>
            <article class="min-h-36 rounded-xl border border-[#E2E8F0] border-t-2 border-t-[#155DFC] bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-[#64748B]">Laki-Laki</p>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-child" aria-hidden="true"></i></span>
                </div>
                <p class="mt-4 text-2xl font-extrabold text-[#0F172A]">{{ number_format($statistik['laki_laki']) }} <span class="text-sm font-semibold text-[#64748B]">Anak</span></p>
                <p class="mt-1 text-xs font-semibold text-[#16A34A]">{{ number_format($statistik['persentase_laki_laki'], 1) }}% dari total balita</p>
            </article>
            <article class="min-h-36 rounded-xl border border-[#E2E8F0] border-t-2 border-t-[#155DFC] bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-[#64748B]">Perempuan</p>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-child-dress" aria-hidden="true"></i></span>
                </div>
                <p class="mt-4 text-2xl font-extrabold text-[#0F172A]">{{ number_format($statistik['perempuan']) }} <span class="text-sm font-semibold text-[#64748B]">Anak</span></p>
                <p class="mt-1 text-xs font-semibold text-[#16A34A]">{{ number_format($statistik['persentase_perempuan'], 1) }}% dari total balita</p>
            </article>
            <article class="min-h-36 rounded-xl border border-[#E2E8F0] border-t-2 border-t-[#155DFC] bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-[#64748B]">Perlu Perhatian</p>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
                </div>
                <p class="mt-4 text-2xl font-extrabold text-[#0F172A]">0 <span class="text-sm font-semibold text-[#64748B]">Balita</span></p>
                <p class="mt-1 text-xs font-medium text-[#64748B]">Belum ada data penilaian gizi</p>
            </article>
            </section>
        </div>

        <section class="rounded-xl border border-[#E2E8F0] bg-white p-4 shadow-sm sm:p-5" aria-label="Filter data balita">
            <div class="mb-4 flex items-center gap-2 border-b border-[#E2E8F0] pb-4">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-sliders" aria-hidden="true"></i></span>
                <h2 class="text-sm font-bold text-[#0F172A]">Filter Data</h2>
            </div>
            <form action="{{ route('posyandu.anak.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-[#64748B]">Cari data</span>
                    <span class="relative block">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-[#64748B]" aria-hidden="true"></i>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="NIK atau nama" class="min-h-10 w-full rounded-lg border border-[#E2E8F0] bg-white py-2 pl-9 pr-3 text-sm outline-none transition focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                    </span>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-[#64748B]">Jenis kelamin</span>
                    <select aria-label="Jenis kelamin" name="jenis_kelamin" class="min-h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 py-2 text-sm text-[#0F172A] outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        <option value="" @selected(request('jenis_kelamin') === null || request('jenis_kelamin') === '')>Semua</option>
                        <option value="L" @selected(request('jenis_kelamin') === 'L')>Laki-Laki</option>
                        <option value="P" @selected(request('jenis_kelamin') === 'P')>Perempuan</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-[#64748B]">Status gizi</span>
                    <select aria-label="Status gizi" disabled class="min-h-10 w-full cursor-not-allowed rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] px-3 py-2 text-sm text-[#64748B]">
                        <option>Belum tersedia</option>
                    </select>
                </label>
                <div class="flex items-end justify-end gap-2 sm:col-span-2 lg:col-span-3 2xl:col-span-4">
                    <button type="submit" class="min-h-10 rounded-lg bg-[#155DFC] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#155DFC]/90 focus:outline-none focus:ring-2 focus:ring-[#155DFC] focus:ring-offset-2">Terapkan</button>
                    <a href="{{ route('posyandu.anak.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-[#E2E8F0] px-4 text-sm font-semibold text-[#64748B] transition hover:bg-[#F8FAFC]">Reset</a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#E2E8F0] px-4 py-4 sm:px-5">
                <div>
                    <h2 class="text-base font-bold text-[#0F172A]">Daftar Balita Terdaftar</h2>
                    <p class="mt-1 text-sm text-[#64748B]">Data anak dan ringkasan tumbuh kembang</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F8FAFC] px-3 py-1.5 text-xs font-bold text-[#155DFC]">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>{{ $anak->total() }} data
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-5 py-3.5">NIK Anak</th>
                            <th scope="col" class="px-5 py-3.5">Nama Balita</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3.5">Tanggal Lahir &amp; Usia</th>
                            <th scope="col" class="px-5 py-3.5">Orang Tua</th>
                            <th scope="col" class="px-5 py-3.5">Domisili</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3.5">Status Tumbuh Kembang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        @forelse ($anak as $item)
                            <tr class="transition-colors hover:bg-[#F8FAFC]/40">
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-[#0F172A]">{{ $item->nik_anak }}</td>
                                <td class="px-5 py-4">
                                    <span class="block font-bold text-[#0F172A]">{{ $item->nama_anak }}</span>
                                    <span class="mt-1 block text-xs text-[#64748B]">{{ $item->jenis_kelamin_label }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="block text-[#0F172A]">{{ $item->tgl_lahir->format('d M Y') }}</span>
                                    <span class="mt-1 block text-xs text-[#64748B]">{{ $item->umur_format }}</span>
                                </td>
                                <td class="px-5 py-4 text-[#0F172A]">{{ $item->orangTua?->nama_ibu ?? 'Belum tersedia' }}</td>
                                <td class="px-5 py-4">
                                    <span class="block font-medium text-[#0F172A]">{{ $item->posyandu?->nama_posyandu ?? 'Posyandu belum tersedia' }}</span>
                                    <span class="mt-1 block text-xs text-[#64748B]">{{ $item->posyandu?->alamat ?? $item->posyandu?->desa_kelurahan ?? 'Domisili belum tersedia' }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F8FAFC] px-2.5 py-1.5 text-xs font-semibold text-[#64748B]">
                                        <i class="fa-regular fa-clock" aria-hidden="true"></i>Belum ada pemeriksaan
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#F8FAFC] text-lg text-[#155DFC]"><i class="fa-solid fa-child-reaching" aria-hidden="true"></i></span>
                                    <p class="mt-3 font-semibold text-[#0F172A]">Belum ada data anak</p>
                                    <p class="mt-1 text-sm text-[#64748B]">Data yang didaftarkan akan muncul di tabel ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($anak->hasPages())
                <div class="border-t border-[#E2E8F0] px-4 py-4 sm:px-5">{{ $anak->links() }}</div>
            @endif
        </section>
    </div>

    <div id="anak-modal" class="fixed inset-0 z-50 items-center justify-center bg-[#0F172A]/50 p-4 {{ $errors->any() ? 'flex' : 'hidden' }}" @if($errors->any()) role="dialog" aria-modal="true" aria-labelledby="anak-modal-title" @else aria-hidden="true" @endif>
        <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-5 shadow-2xl sm:p-7">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wide text-[#155DFC]">Pendaftaran baru</p>
                    <h2 id="anak-modal-title" class="mt-1 text-lg font-bold text-[#0F172A]">Mendaftarkan Anak Baru</h2>
                    <p class="mt-1 text-xs text-[#64748B]">Lengkapi data anak dan orang tua sesuai dokumen yang dimiliki.</p>
                </div>
                <button id="close-anak-modal" type="button" aria-label="Tutup formulir" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-[#64748B] transition hover:bg-[#F8FAFC] hover:text-[#0F172A]">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-[#DC2626] bg-[#F8FAFC] p-4 text-sm text-[#DC2626]">
                    <p class="font-semibold">Periksa kembali data berikut:</p>
                    <ul class="mt-1 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('posyandu.anak.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <h3 class="font-bold text-[#0F172A]">Data Anak</h3>
                    <p class="mt-1 text-xs text-[#64748B]">NIK anak digunakan untuk login Orang Tua bersama tanggal lahir anak.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label for="modal_nik_anak" class="space-y-1.5 sm:col-span-2">
                        <span class="block text-xs font-semibold text-[#0F172A]">NIK Anak <span class="text-[#DC2626]">*</span></span>
                        <input id="modal_nik_anak" name="nik_anak" type="text" inputmode="numeric" maxlength="16" minlength="16" pattern="[0-9]{16}" required value="{{ old('nik_anak') }}" placeholder="Masukkan 16 digit NIK" class="min-h-11 w-full rounded-lg border border-[#E2E8F0] px-3.5 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        <span class="block text-[11px] text-[#64748B]">Masukkan 16 digit angka tanpa spasi.</span>
                        @error('nik_anak')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
                    </label>
                    <label for="modal_nama_anak" class="space-y-1.5 sm:col-span-2">
                        <span class="block text-xs font-semibold text-[#0F172A]">Nama Anak <span class="text-[#DC2626]">*</span></span>
                        <input id="modal_nama_anak" name="nama_anak" type="text" maxlength="100" required value="{{ old('nama_anak') }}" placeholder="Nama lengkap anak" class="min-h-11 w-full rounded-lg border border-[#E2E8F0] px-3.5 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        @error('nama_anak')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
                    </label>
                    <label for="modal_tgl_lahir" class="space-y-1.5">
                        <span class="block text-xs font-semibold text-[#0F172A]">Tanggal Lahir <span class="text-[#DC2626]">*</span></span>
                        <input id="modal_tgl_lahir" name="tgl_lahir" type="date" max="{{ today()->format('Y-m-d') }}" required value="{{ old('tgl_lahir') }}" class="min-h-11 w-full rounded-lg border border-[#E2E8F0] px-3.5 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        @error('tgl_lahir')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
                    </label>
                    <label for="modal_jenis_kelamin" class="space-y-1.5">
                        <span class="block text-xs font-semibold text-[#0F172A]">Jenis Kelamin <span class="text-[#DC2626]">*</span></span>
                        <select id="modal_jenis_kelamin" name="jenis_kelamin" required class="min-h-11 w-full rounded-lg border border-[#E2E8F0] bg-white px-3.5 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
                    </label>
                </div>

                <div class="border-t border-[#E2E8F0] pt-4">
                    <h3 class="font-bold text-[#0F172A]">Data Orang Tua</h3>
                    <p class="mt-1 text-xs text-[#64748B]">Masukkan nama ibu dan nama ayah jika tersedia.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label for="modal_nama_ibu" class="space-y-1.5">
                        <span class="block text-xs font-semibold text-[#0F172A]">Nama Ibu <span class="text-[#DC2626]">*</span></span>
                        <input id="modal_nama_ibu" name="nama_ibu" type="text" maxlength="100" required value="{{ old('nama_ibu') }}" placeholder="Nama lengkap ibu" class="min-h-11 w-full rounded-lg border border-[#E2E8F0] px-3.5 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        @error('nama_ibu')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
                    </label>
                    <label for="modal_nama_ayah" class="space-y-1.5">
                        <span class="block text-xs font-semibold text-[#0F172A]">Nama Ayah <span class="font-normal text-[#64748B]">(Opsional)</span></span>
                        <input id="modal_nama_ayah" name="nama_ayah" type="text" maxlength="100" value="{{ old('nama_ayah') }}" placeholder="Nama lengkap ayah" class="min-h-11 w-full rounded-lg border border-[#E2E8F0] px-3.5 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        @error('nama_ayah')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
                    </label>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-[#E2E8F0] pt-4 sm:flex-row sm:justify-end">
                    <button id="cancel-anak-modal" type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-[#E2E8F0] px-4 py-2 text-sm font-semibold text-[#64748B] transition hover:bg-[#F8FAFC]">Batal</button>
                    <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#155DFC]/90">
                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan Data Anak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const anakModal = document.getElementById('anak-modal');
        const openAnakModal = document.getElementById('open-anak-modal');
        const closeAnakModal = document.getElementById('close-anak-modal');
        const cancelAnakModal = document.getElementById('cancel-anak-modal');

        const showAnakModal = () => {
            anakModal.classList.remove('hidden');
            anakModal.classList.add('flex');
            anakModal.setAttribute('role', 'dialog');
            anakModal.setAttribute('aria-modal', 'true');
            anakModal.removeAttribute('aria-hidden');
            anakModal.querySelector('input, select, textarea, button')?.focus();
        };

        const hideAnakModal = () => {
            anakModal.classList.add('hidden');
            anakModal.classList.remove('flex');
            anakModal.setAttribute('aria-hidden', 'true');
            anakModal.removeAttribute('aria-modal');
            anakModal.removeAttribute('role');
            openAnakModal.focus();
        };

        openAnakModal.addEventListener('click', showAnakModal);
        closeAnakModal.addEventListener('click', hideAnakModal);
        cancelAnakModal.addEventListener('click', hideAnakModal);
        anakModal.addEventListener('click', (event) => {
            if (event.target === anakModal) hideAnakModal();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !anakModal.classList.contains('hidden')) hideAnakModal();
        });
    </script>
@endsection