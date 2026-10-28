@extends('layouts.posyandu')

@section('title', 'Input Data Pemeriksaan')

@section('content')
	<div class="space-y-6">
		<div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
			<div>
				<h1 class="mt-2 text-2xl font-extrabold text-[#0F172A]">Input Data Pemeriksaan</h1>
				<p class="mt-1 text-sm text-[#64748B]">Catat hasil pengukuran antropometri balita pada kunjungan Posyandu.
				</p>
			</div>
			<div class="flex flex-wrap gap-2">
				<a href="#form-pemeriksaan" aria-current="page"
					class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-[#155DFC]/20 bg-[#155DFC]/10 px-4 py-2 text-sm font-bold text-[#155DFC]">
					<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Input Pengukuran Baru
				</a>
				<a href="#riwayat-hari-ini"
					class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-[#E2E8F0] bg-white px-4 py-2 text-sm font-semibold text-[#64748B] transition hover:bg-[#F8FAFC]">
					<i class="fa-regular fa-clock" aria-hidden="true"></i>Riwayat Hari Ini ({{ $pemeriksaanHariIni }}
					Tercatat)
				</a>
			</div>
		</div>

		@if (session('success'))
			<div role="status"
				class="flex items-center gap-3 rounded-lg border border-[#16A34A]/20 bg-[#16A34A]/10 px-4 py-3 text-sm font-medium text-[#16A34A]">
				<i class="fa-solid fa-circle-check" aria-hidden="true"></i><span>{{ session('success') }}</span>
			</div>
		@endif

		@if ($errors->any())
			<div role="alert"
				class="flex items-start gap-3 rounded-lg border border-[#DC2626]/20 bg-[#DC2626]/10 px-4 py-3 text-sm text-[#DC2626]">
				<i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>
				<div>
					<p class="font-bold">Periksa kembali data pemeriksaan.</p>
					<ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>@endforeach
					</ul>
				</div>
			</div>
		@endif

		<div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(380px,0.85fr)]">
			<section id="form-pemeriksaan" class="space-y-5">
				<div class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
					<div class="mb-5 flex items-start gap-3">
						<span
							class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#155DFC]/10 text-sm font-extrabold text-[#155DFC]">1</span>
						<div>
							<h2 class="font-bold text-[#0F172A]">Pilih Data Balita</h2>
							<p class="mt-1 text-sm text-[#64748B]">Cari balita terdaftar berdasarkan nama atau NIK.</p>
						</div>
					</div>

					<form id="balita-picker" action="{{ route('posyandu.pemeriksaan.index') }}" method="GET"
						class="flex flex-col gap-2 sm:flex-row">
						<label for="balita-search" class="sr-only">Cari balita</label>
						<input id="balita-search" type="search" list="balita-options" autocomplete="off" required
							value="{{ $selectedAnak ? $selectedAnak->nama_anak . ' - ' . $selectedAnak->nik_anak : '' }}"
							placeholder="Ketik nama atau pilih balita..."
							class="min-h-11 min-w-0 flex-1 rounded-lg border border-[#E2E8F0] px-3.5 py-2.5 text-sm outline-none transition placeholder:text-[#64748B] focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
						<input id="selected-nik" type="hidden" name="nik_anak" value="{{ $selectedAnak?->nik_anak }}">
						<datalist id="balita-options">
							@foreach ($anak as $balita)
								<option value="{{ $balita->nama_anak }} - {{ $balita->nik_anak }}"
									data-nik="{{ $balita->nik_anak }}"></option>
							@endforeach
						</datalist>
						<button type="submit"
							class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#155DFC]/90">
							<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>Pilih Balita
						</button>
					</form>

					@if ($anak->isEmpty())
						<p class="mt-3 text-sm text-[#F59E0B]">Belum ada data balita di Posyandu ini. Daftarkan balita terlebih
							dahulu.</p>
					@endif

					@if ($selectedAnak)
						<div class="mt-5 rounded-lg border border-[#155DFC]/20 bg-[#F8FAFC] p-4">
							<div class="flex flex-col gap-4 sm:flex-row sm:items-start">
								<span
									class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#155DFC] text-lg font-extrabold uppercase text-white">{{ mb_substr($selectedAnak->nama_anak, 0, 1) }}</span>
								<div class="min-w-0 flex-1">
									<div class="flex flex-wrap items-center gap-2">
										<h3 class="text-base font-extrabold text-[#0F172A]">{{ $selectedAnak->nama_anak }}</h3>
										<span
											class="rounded-full bg-[#16A34A]/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-[#16A34A]">Terdaftar</span>
									</div>
									<p class="mt-1 text-xs text-[#64748B]">{{ $selectedAnak->jenis_kelamin_label }} <span
											class="mx-1 text-[#64748B]">•</span> {{ $selectedAnak->umur_format }}</p>
									<div class="mt-4 grid gap-3 text-xs sm:grid-cols-2">
										<div>
											<p class="text-[#64748B]">NIK</p>
											<p class="mt-1 font-semibold text-[#0F172A]">{{ $selectedAnak->nik_anak }}</p>
										</div>
										<div>
											<p class="text-[#64748B]">Nama Ibu</p>
											<p class="mt-1 font-semibold text-[#0F172A]">
												{{ $selectedAnak->orangTua?->nama_ibu ?? 'Belum tersedia' }}</p>
										</div>
										<div class="sm:col-span-2">
											<p class="text-[#64748B]">Kunjungan berikutnya</p>
											<p class="mt-1 font-semibold text-[#0F172A]">
												Ke-{{ $selectedAnak->pemeriksaan()->count() + 1 }}</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					@else
						<div
							class="mt-5 flex items-center gap-3 rounded-lg border border-dashed border-[#E2E8F0] bg-[#F8FAFC] px-4 py-5 text-sm text-[#64748B]">
							<i class="fa-regular fa-id-card text-lg text-[#64748B]" aria-hidden="true"></i>
							<span>Informasi balita akan tampil setelah balita dipilih.</span>
						</div>
					@endif
				</div>

				<form action="{{ route('posyandu.pemeriksaan.store') }}" method="POST"
					class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm">
					@csrf
					<input type="hidden" name="nik_anak" value="{{ old('nik_anak', $selectedAnak?->nik_anak) }}">
					<div class="space-y-5 p-5 sm:p-6">
						<div class="flex items-start gap-3">
							<span
								class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#155DFC]/10 text-sm font-extrabold text-[#155DFC]">2</span>
							<div>
								<h2 class="font-bold text-[#0F172A]">Pencatatan Fisik &amp; Antropometri</h2>
								<p class="mt-1 text-sm text-[#64748B]">Catat hasil pengukuran pada kunjungan ini.</p>
							</div>
						</div>

						<div class="grid gap-4 sm:grid-cols-2">
							<label class="space-y-1.5 sm:col-span-2">
								<span class="block text-sm font-semibold text-[#0F172A]">Tanggal Periksa <span
										class="text-[#DC2626]">*</span></span>
								<input type="date" name="tgl_periksa"
									value="{{ old('tgl_periksa', today()->format('Y-m-d')) }}"
									max="{{ today()->format('Y-m-d') }}" required @disabled(!$selectedAnak)
									class="min-h-11 w-full rounded-lg border border-[#E2E8F0] bg-white px-3.5 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20 disabled:cursor-not-allowed disabled:bg-[#F8FAFC]">
								@error('tgl_periksa')<span
								class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
							</label>
							<label class="space-y-1.5">
								<span class="block text-sm font-semibold text-[#0F172A]">Berat Badan <span
										class="text-[#DC2626]">*</span></span>
								<span class="relative block"><input type="number" name="berat_badan" min="0" max="200"
										step="0.1" value="{{ old('berat_badan') }}" placeholder="0,0" required
										@disabled(!$selectedAnak)
										class="min-h-11 w-full rounded-lg border border-[#E2E8F0] bg-white py-2.5 pl-3.5 pr-12 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20 disabled:cursor-not-allowed disabled:bg-[#F8FAFC]"><span
										class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#64748B]">kg</span></span>
								@error('berat_badan')<span
								class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
							</label>
							<label class="space-y-1.5">
								<span class="block text-sm font-semibold text-[#0F172A]">Tinggi Badan <span
										class="text-[#DC2626]">*</span></span>
								<span class="relative block"><input type="number" name="tinggi_badan" min="0" max="300"
										step="0.1" value="{{ old('tinggi_badan') }}" placeholder="0,0" required
										@disabled(!$selectedAnak)
										class="min-h-11 w-full rounded-lg border border-[#E2E8F0] bg-white py-2.5 pl-3.5 pr-12 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20 disabled:cursor-not-allowed disabled:bg-[#F8FAFC]"><span
										class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#64748B]">cm</span></span>
								@error('tinggi_badan')<span
								class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
							</label>
							<label class="space-y-1.5">
								<span class="block text-sm font-semibold text-[#0F172A]">Lingkar Kepala <span
										class="font-normal text-[#64748B]">(Opsional)</span></span>
								<span class="relative block"><input type="number" name="lingkar_kepala" min="0" max="100"
										step="0.1" value="{{ old('lingkar_kepala') }}" placeholder="0,0"
										@disabled(!$selectedAnak)
										class="min-h-11 w-full rounded-lg border border-[#E2E8F0] bg-white py-2.5 pl-3.5 pr-12 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20 disabled:cursor-not-allowed disabled:bg-[#F8FAFC]"><span
										class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#64748B]">cm</span></span>
								@error('lingkar_kepala')<span
								class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
							</label>
							<label class="space-y-1.5 sm:col-span-2">
								<span class="block text-sm font-semibold text-[#0F172A]">Keluhan / Catatan <span
										class="font-normal text-[#64748B]">(Opsional)</span></span>
								<textarea name="keluhan" rows="3" maxlength="2000"
									placeholder="Catatan pemeriksaan atau keluhan yang disampaikan..."
									@disabled(!$selectedAnak)
									class="w-full resize-y rounded-lg border border-[#E2E8F0] px-3.5 py-3 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20 disabled:cursor-not-allowed disabled:bg-[#F8FAFC]">{{ old('keluhan') }}</textarea>
								@error('keluhan')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror
							</label>
						</div>
					</div>
					<div
						class="flex flex-col-reverse gap-3 border-t border-[#E2E8F0] bg-[#F8FAFC] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<p class="text-xs text-[#64748B]"><i class="fa-solid fa-shield-heart mr-1 text-[#16A34A]"
								aria-hidden="true"></i>Data akan menunggu validasi bidan.</p>
						<button type="submit" @disabled(!$selectedAnak)
							class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#155DFC]/90 focus:outline-none focus:ring-2 focus:ring-[#155DFC] focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-[#E2E8F0] disabled:text-[#64748B]">
							<i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan Pemeriksaan
						</button>
					</div>
				</form>
			</section>

			<aside class="space-y-5">
				<section class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6"
					aria-labelledby="zscore-heading">
					<div class="flex flex-wrap items-start justify-between gap-3">
						<div>
							<h2 id="zscore-heading" class="font-bold text-[#0F172A]">Status Gizi &amp; Z-Score KMS</h2>
							<p class="mt-1 text-xs text-[#64748B]">Kalkulasi berdasarkan standar WHO</p>
						</div>
						<span
							class="rounded-full bg-[#F59E0B]/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-[#F59E0B]">Belum
							dihitung</span>
					</div>
					<div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3 xl:grid-cols-1 2xl:grid-cols-3">
						<article class="rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] p-3">
							<p class="text-xs font-bold text-[#64748B]">BB / U <span class="font-normal">(Usia)</span></p>
							<p class="mt-2 text-xl font-extrabold text-[#64748B]">— <span
									class="text-xs font-semibold">SD</span></p>
							<p class="mt-1 text-[11px] text-[#64748B]">Menunggu rujukan WHO</p>
						</article>
						<article class="rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] p-3">
							<p class="text-xs font-bold text-[#64748B]">TB / U <span class="font-normal">(Tinggi)</span></p>
							<p class="mt-2 text-xl font-extrabold text-[#64748B]">— <span
									class="text-xs font-semibold">SD</span></p>
							<p class="mt-1 text-[11px] text-[#64748B]">Menunggu rujukan WHO</p>
						</article>
						<article class="rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] p-3">
							<p class="text-xs font-bold text-[#64748B]">BB / TB <span class="font-normal">(Proporsi)</span>
							</p>
							<p class="mt-2 text-xl font-extrabold text-[#64748B]">— <span
									class="text-xs font-semibold">SD</span></p>
							<p class="mt-1 text-[11px] text-[#64748B]">Menunggu rujukan WHO</p>
						</article>
					</div>
					<p
						class="mt-4 rounded-lg border border-[#F59E0B]/20 bg-[#F59E0B]/10 px-3 py-2.5 text-xs leading-relaxed text-[#F59E0B]">
						Aplikasi belum memiliki tabel standar WHO untuk menghitung Z-score. Hasil tidak ditampilkan sebelum
						referensi resmi tersedia.</p>
				</section>

				<section class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6"
					aria-labelledby="growth-heading">
					<div class="flex items-start justify-between gap-3">
						<div>
							<h2 id="growth-heading" class="font-bold text-[#0F172A]">Trayektori Pertumbuhan</h2>
							<p class="mt-1 text-xs text-[#64748B]">Riwayat berat badan tercatat</p>
						</div>
						<span
							class="rounded-md bg-[#155DFC]/10 px-2 py-1 text-[10px] font-bold text-[#155DFC]">{{ $selectedAnak ? 'Data aktual' : 'Pilih balita' }}</span>
					</div>
					@if ($chartRecords->count() > 1)
						<div class="mt-5 h-48 rounded-lg bg-[#F8FAFC] p-3">
							<svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-full w-full" role="img"
								aria-label="Grafik riwayat berat badan">
								<line x1="0" y1="20" x2="100" y2="20" stroke="#e2e8f0" stroke-width="0.7"></line>
								<line x1="0" y1="50" x2="100" y2="50" stroke="#e2e8f0" stroke-width="0.7"></line>
								<line x1="0" y1="80" x2="100" y2="80" stroke="#e2e8f0" stroke-width="0.7"></line>
								<polyline points="{{ $chartPoints }}" fill="none" stroke="#155DFC" stroke-width="2.5"
									stroke-linecap="round" stroke-linejoin="round"></polyline>
							</svg>
						</div>
						<div class="mt-2 flex justify-between text-[10px] text-[#64748B]">
							<span>{{ $chartRecords->first()->tgl_periksa->format('d M Y') }}</span>
							<span>{{ $chartRecords->last()->tgl_periksa->format('d M Y') }}</span>
						</div>
					@else
						<div
							class="mt-5 flex h-48 flex-col items-center justify-center rounded-lg border border-dashed border-[#E2E8F0] bg-[#F8FAFC] px-5 text-center">
							<i class="fa-solid fa-chart-line text-2xl text-[#64748B]" aria-hidden="true"></i>
							<p class="mt-3 text-sm font-semibold text-[#64748B]">
								{{ $selectedAnak ? 'Belum cukup data pengukuran' : 'Pilih balita untuk melihat grafik' }}</p>
							<p class="mt-1 text-xs text-[#64748B]">Grafik akan terbentuk dari hasil pengukuran yang tersimpan.
							</p>
						</div>
					@endif
				</section>

				<section class="rounded-xl border border-[#16A34A]/20 bg-[#16A34A]/10 p-5 sm:p-6"
					aria-labelledby="recommendation-heading">
					<div class="flex items-start gap-3">
						<span
							class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-[#16A34A]"><i
								class="fa-solid fa-comments" aria-hidden="true"></i></span>
						<div>
							<h2 id="recommendation-heading" class="font-bold text-[#0F172A]">Rekomendasi Kader &amp;
								Konseling Orang Tua</h2>
							<p class="mt-2 text-sm leading-relaxed text-[#64748B]">Catat hasil ukur sesuai prosedur
								Posyandu. Konseling dan tindak lanjut gizi dilakukan berdasarkan hasil validasi petugas
								kesehatan.</p>
						</div>
					</div>
				</section>
			</aside>
		</div>

		<section id="riwayat-hari-ini" class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm">
			<div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#E2E8F0] px-5 py-4">
				<div>
					<h2 class="font-bold text-[#0F172A]">Riwayat Pemeriksaan Hari Ini</h2>
					<p class="mt-1 text-xs text-[#64748B]">Pemeriksaan yang sudah tercatat di Posyandu ini.</p>
				</div>
				<span
					class="rounded-full bg-[#155DFC]/10 px-3 py-1.5 text-xs font-bold text-[#155DFC]">{{ $pemeriksaanHariIni }}
					tercatat</span>
			</div>
			@if ($riwayatHariIni->isNotEmpty())
				<div class="overflow-x-auto">
					<table class="w-full min-w-[700px] text-left text-sm">
						<thead class="bg-[#F8FAFC] text-[10px] font-bold uppercase tracking-wider text-[#64748B]">
							<tr>
								<th class="px-5 py-3">Balita</th>
								<th class="px-5 py-3">Waktu</th>
								<th class="px-5 py-3">Berat</th>
								<th class="px-5 py-3">Tinggi</th>
								<th class="px-5 py-3">Status</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-[#E2E8F0]">
							@foreach ($riwayatHariIni as $record)
								<tr>
									<td class="px-5 py-3.5 font-semibold text-[#0F172A]">
										{{ $record->anak?->nama_anak ?? 'Data anak tidak tersedia' }}</td>
									<td class="px-5 py-3.5 text-[#64748B]">{{ $record->created_at?->format('H:i') ?? '—' }}</td>
									<td class="px-5 py-3.5 text-[#64748B]">
										{{ $record->berat_badan !== null ? number_format($record->berat_badan, 1) . ' kg' : '—' }}
									</td>
									<td class="px-5 py-3.5 text-[#64748B]">
										{{ $record->tinggi_badan !== null ? number_format($record->tinggi_badan, 1) . ' cm' : '—' }}
									</td>
									<td class="px-5 py-3.5"><span
											class="rounded-full bg-[#F59E0B]/10 px-2.5 py-1 text-[11px] font-semibold text-[#F59E0B]">{{ $record->status_validasi ?? 'Menunggu' }}</span>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@else
				<div class="px-5 py-10 text-center">
					<i class="fa-regular fa-clipboard text-2xl text-[#64748B]" aria-hidden="true"></i>
					<p class="mt-3 text-sm font-semibold text-[#0F172A]">Belum ada pemeriksaan hari ini</p>
					<p class="mt-1 text-xs text-[#64748B]">Pemeriksaan yang disimpan akan muncul di sini.</p>
				</div>
			@endif
		</section>

	</div>
@endsection

@push('scripts')
	<script>
		const pickerForm = document.getElementById('balita-picker');
		const balitaSearch = document.getElementById('balita-search');
		const selectedNik = document.getElementById('selected-nik');
		const balitaOptions = document.getElementById('balita-options');

		const updateSelectedBalita = () => {
			const option = Array.from(balitaOptions.options).find((item) => item.value === balitaSearch.value);
			selectedNik.value = option?.dataset.nik ?? '';
			balitaSearch.setCustomValidity('');
		};

		balitaSearch.addEventListener('input', updateSelectedBalita);
		pickerForm.addEventListener('submit', (event) => {
			updateSelectedBalita();
			if (!selectedNik.value) {
				event.preventDefault();
				balitaSearch.setCustomValidity('Pilih balita dari daftar yang tersedia.');
				balitaSearch.reportValidity();
			}
		});
	</script>
@endpush