@extends('layouts.posyandu')

@section('title', 'Kelola Jadwal Posyandu')

@section('content')
	<div class="space-y-6">
		<div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
			<div>
				<h1 class="mt-2 text-2xl font-extrabold tracking-tight text-[#0F172A] sm:text-3xl">Kelola Jadwal Posyandu</h1>
				<p class="mt-1 max-w-2xl text-sm text-[#64748B]">Atur agenda pelayanan Posyandu dan pantau jadwal kegiatan unit Anda.</p>
			</div>
			<div class="flex flex-wrap gap-2">
				<a href="https://wa.me/?text={{ rawurlencode('Pengingat kegiatan Posyandu ' . ($unit?->nama_posyandu ?? '') . '. Silakan periksa jadwal pelayanan terbaru di SIPANDA.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-[#16A34A] bg-white px-4 py-2 text-sm font-semibold text-[#16A34A] transition hover:bg-[#16A34A]/10">
					<i class="fa-brands fa-whatsapp" aria-hidden="true"></i>Kirim Pengingat WhatsApp
				</a>
				<button id="open-jadwal-modal" type="button" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:opacity-90">
					<i class="fa-solid fa-plus" aria-hidden="true"></i>Tambah Jadwal Baru
				</button>
			</div>
		</div>

		@if (session('success'))
			<div role="status" class="flex items-center gap-3 rounded-lg border border-[#16A34A] bg-[#16A34A]/10 px-4 py-3 text-sm font-medium text-[#16A34A]">
				<i class="fa-solid fa-circle-check" aria-hidden="true"></i><span>{{ session('success') }}</span>
			</div>
		@endif
		@if ($errors->any())
			<div role="alert" class="flex items-start gap-3 rounded-lg border border-[#DC2626] bg-[#DC2626]/10 px-4 py-3 text-sm text-[#DC2626]">
				<i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>
				<div><p class="font-bold">Periksa kembali data jadwal.</p><ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
			</div>
		@endif

		<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan jadwal">
			<article class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
				<div class="flex items-start justify-between">
					<div><p class="text-xs font-semibold text-[#64748B]">Jadwal Bulan Ini</p><p class="mt-2 text-2xl font-extrabold text-[#0F172A]">{{ $monthCount }} <span class="text-sm font-semibold text-[#64748B]">agenda</span></p></div>
					<span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#155DFC]"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span>
				</div>
				<p class="mt-3 text-[11px] text-[#64748B]">{{ $completedSchedules->count() }} selesai <span class="mx-1 text-[#64748B]">•</span> {{ $todaySchedules->count() }} hari ini <span class="mx-1 text-[#64748B]">•</span> {{ $schedules->where('tgl_kegiatan', '>=', today())->count() }} mendatang</p>
			</article>
			<article class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
				<div class="flex items-start justify-between">
					<div><p class="text-xs font-semibold text-[#64748B]">Sasaran Terdaftar</p><p class="mt-2 text-2xl font-extrabold text-[#0F172A]">{{ number_format($totalChildren) }} <span class="text-sm font-semibold text-[#64748B]">balita</span></p></div>
					<span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-children" aria-hidden="true"></i></span>
				</div>
				<p class="mt-3 text-[11px] text-[#64748B]">Jumlah anak terdaftar di unit ini. Sasaran per agenda tidak tersedia.</p>
			</article>
			<article class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
				<div class="flex items-start justify-between">
					<div><p class="text-xs font-semibold text-[#64748B]">Kehadiran Rata-rata</p><p class="mt-2 text-2xl font-extrabold text-[#0F172A]">{{ $averageAttendance !== null ? number_format($averageAttendance, 1) . '%' : '—' }}</p></div>
					<span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#16A34A]"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
				</div>
				<p class="mt-3 text-[11px] text-[#64748B]">{{ $averageAttendance !== null ? 'Rata-rata dari pemeriksaan pada agenda yang telah lewat.' : 'Belum ada data kehadiran untuk menghitung rata-rata.' }}</p>
			</article>
			<article class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
				<div class="flex items-start justify-between">
					<div><p class="text-xs font-semibold text-[#64748B]">Petugas Terdaftar</p><p class="mt-2 text-2xl font-extrabold text-[#0F172A]">{{ $bidan->count() + $kader->count() }} <span class="text-sm font-semibold text-[#64748B]">petugas</span></p></div>
					<span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#F59E0B]"><i class="fa-solid fa-user-group" aria-hidden="true"></i></span>
				</div>
				<p class="mt-3 text-[11px] text-[#64748B]">{{ $bidan->count() }} bidan <span class="mx-1 text-[#64748B]">•</span> {{ $kader->count() }} kader terdaftar</p>
			</article>
		</section>

		<div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(360px,0.85fr)]">
			<section class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm" aria-labelledby="calendar-heading">
				<div class="flex flex-col gap-4 border-b border-[#E2E8F0] p-5 sm:flex-row sm:items-center sm:justify-between">
					<div class="flex items-center gap-3">
						<a aria-label="Bulan sebelumnya" href="{{ route('posyandu.jadwal.index', ['month' => $month->copy()->subMonth()->format('Y-m'), 'date' => $month->copy()->subMonth()->startOfMonth()->toDateString()]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-[#E2E8F0] text-[#64748B] transition hover:bg-[#F8FAFC]"><i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i></a>
						<div><h2 id="calendar-heading" class="font-bold text-[#0F172A]">{{ $month->locale('id')->translatedFormat('F Y') }}</h2><p class="mt-0.5 text-[11px] text-[#64748B]">Kalender pelayanan Posyandu</p></div>
						<a aria-label="Bulan berikutnya" href="{{ route('posyandu.jadwal.index', ['month' => $month->copy()->addMonth()->format('Y-m'), 'date' => $month->copy()->addMonth()->startOfMonth()->toDateString()]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-[#E2E8F0] text-[#64748B] transition hover:bg-[#F8FAFC]"><i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i></a>
					</div>
					<div class="flex flex-wrap items-center gap-2">
						<form action="{{ route('posyandu.jadwal.index') }}" method="GET" class="flex items-center gap-2">
							<label for="calendar-month" class="sr-only">Pilih bulan dan tahun pelayanan</label>
							<input id="calendar-month" type="month" name="month" value="{{ $month->format('Y-m') }}" class="min-h-9 rounded-lg border border-[#E2E8F0] px-2.5 text-xs font-semibold text-[#64748B] focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
							<button class="min-h-9 rounded-lg border border-[#E2E8F0] px-3 text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Tampilkan</button>
						</form>
						<div class="inline-flex rounded-lg bg-[#F8FAFC] p-1 text-[11px] font-semibold text-[#64748B]">
							<span aria-current="page" class="rounded-md bg-white px-3 py-1.5 text-[#155DFC] shadow-sm">Bulan</span><a href="#agenda-mendatang" class="rounded-md px-3 py-1.5 hover:text-[#0F172A]">Agenda</a>
						</div>
					</div>
				</div>
				<div class="p-3 sm:p-5">
					<div class="grid grid-cols-7 border-b border-[#E2E8F0] pb-2 text-center text-[10px] font-bold uppercase tracking-wide text-[#64748B] sm:text-xs">
						@foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)<div>{{ $weekday }}</div>@endforeach
					</div>
					<div class="grid grid-cols-7">
						@foreach ($calendarDays as $day)
							<a href="{{ route('posyandu.jadwal.index', ['month' => $month->format('Y-m'), 'date' => $day['date']->toDateString()]) }}" class="group min-h-20 border-b border-r border-[#E2E8F0] p-1.5 transition hover:bg-[#F8FAFC] sm:min-h-24 sm:p-2.5 {{ !$day['in_month'] ? 'bg-[#F8FAFC]/70 text-[#64748B]' : '' }} {{ $day['is_selected'] ? 'bg-[#155DFC]/10 ring-2 ring-inset ring-[#155DFC]' : '' }}">
								<span class="mx-auto flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold {{ $day['is_selected'] ? 'bg-[#155DFC] text-white' : ($day['is_today'] ? 'bg-[#155DFC]/10 text-[#155DFC]' : 'text-[#64748B]') }}">{{ $day['date']->day }}</span>
								<div class="mt-1 space-y-1">
									@foreach ($day['events']->take(2) as $event)
										<span class="block truncate rounded bg-[#155DFC]/10 px-1 py-0.5 text-left text-[9px] font-semibold text-[#155DFC] sm:px-1.5 sm:text-[10px]">{{ $event->agenda ?: $event->lokasi }}</span>
									@endforeach
									@if ($day['events']->count() > 2)<span class="block text-[9px] text-[#64748B]">+{{ $day['events']->count() - 2 }} agenda</span>@endif
								</div>
							</a>
						@endforeach
					</div>
					<div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-[10px] text-[#64748B]">
						<span class="inline-flex items-center gap-1.5"><i class="h-2 w-2 rounded-full bg-[#155DFC]"></i>Penimbangan &amp; Imunisasi</span>
						<span class="inline-flex items-center gap-1.5"><i class="h-2 w-2 rounded-full bg-[#16A34A]"></i>Penyuluhan / Edukasi MP-ASI</span>
						<span class="inline-flex items-center gap-1.5"><i class="h-2 w-2 rounded-full bg-[#F59E0B]"></i>Evaluasi Desa</span>
					</div>
				</div>
			</section>

			<div class="space-y-5">
				<section class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm" aria-labelledby="session-heading">
					<div class="border-b border-[#E2E8F0] px-5 py-4">
						<p class="text-[10px] font-bold uppercase tracking-wide text-[#155DFC]">Detail agenda terpilih</p>
						<h2 id="session-heading" class="mt-1 font-bold text-[#0F172A]">{{ $selectedDate->locale('id')->translatedFormat('l, d F Y') }}</h2>
						<p class="mt-1 text-xs text-[#64748B]">{{ $unit?->nama_posyandu ?? 'Posyandu' }}</p>
					</div>
					<div class="space-y-4 p-5">
						@if ($selectedSchedules->isNotEmpty())
							@foreach ($selectedSchedules as $schedule)
								<article class="{{ !$loop->first ? 'border-t border-[#E2E8F0] pt-4' : '' }}">
									<div class="flex items-start justify-between gap-3">
										<div><h3 class="text-sm font-bold leading-relaxed text-[#0F172A]">Sesi Pelayanan: {{ $schedule->agenda ?: 'Kegiatan Posyandu' }}</h3><p class="mt-1 text-xs text-[#64748B]">{{ $schedule->lokasi }}</p></div>
										<span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $schedule->tgl_kegiatan->isPast() && !$schedule->tgl_kegiatan->isToday() ? 'bg-[#F8FAFC] text-[#64748B]' : ($schedule->tgl_kegiatan->isToday() ? 'bg-[#16A34A]/10 text-[#16A34A]' : 'bg-[#155DFC]/10 text-[#155DFC]') }}">{{ $schedule->tgl_kegiatan->isPast() && !$schedule->tgl_kegiatan->isToday() ? 'Selesai' : ($schedule->tgl_kegiatan->isToday() ? 'Hari ini' : 'Mendatang') }}</span>
									</div>
									<div class="mt-4 flex items-center gap-2 rounded-lg bg-[#F8FAFC] px-3 py-2.5 text-xs text-[#64748B]"><i class="fa-regular fa-clock text-[#155DFC]" aria-hidden="true"></i>Jam pelayanan belum dicatat</div>
									<div class="mt-4">
										<p class="text-xs font-bold text-[#64748B]">Agenda pelayanan</p>
										<p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-[#64748B]">{{ $schedule->agenda ?: 'Agenda belum ditambahkan.' }}</p>
									</div>
									<div class="mt-4 flex items-center justify-between gap-3 rounded-lg border border-[#E2E8F0] px-3 py-2.5">
										<span class="text-xs font-semibold text-[#64748B]">Publikasi ke warga &amp; kader</span>
										<span class="rounded-full bg-[#F8FAFC] px-2.5 py-1 text-[10px] font-bold text-[#64748B]">Status belum tersedia</span>
									</div>
									<div class="mt-4">
										<p class="text-xs font-bold text-[#64748B]">Kader &amp; petugas unit</p>
										<div class="mt-2 flex flex-wrap gap-2">
											@forelse ($bidan as $person)
												<span class="rounded-full bg-[#155DFC]/10 px-2.5 py-1 text-[10px] font-semibold text-[#155DFC]">{{ $person->nama_bidan }}</span>
											@empty
												<span class="text-[11px] text-[#64748B]">Belum ada bidan terdaftar.</span>
											@endforelse
											@forelse ($kader as $person)
												<span class="rounded-full bg-[#16A34A]/10 px-2.5 py-1 text-[10px] font-semibold text-[#16A34A]">{{ $person->nama_kader }}</span>
											@empty
												<span class="text-[11px] text-[#64748B]">Belum ada kader terdaftar.</span>
											@endforelse
										</div>
									</div>
									<div class="mt-4 flex flex-wrap gap-2">
										<a href="{{ route('posyandu.jadwal.index', ['month' => $month->format('Y-m'), 'date' => $selectedDate->toDateString(), 'edit' => $schedule->id_jadwal]) }}" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-[#E2E8F0] px-3 text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Ubah Jadwal</a>
										<a href="{{ route('posyandu.pemeriksaan.index') }}" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-[#E2E8F0] px-3 text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>Absensi Balita</a>
										<a href="https://wa.me/?text={{ rawurlencode('Undangan ' . ($schedule->agenda ?: 'kegiatan Posyandu') . ' pada ' . $schedule->tgl_kegiatan->format('d/m/Y') . ' di ' . $schedule->lokasi . '.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-[#16A34A] px-3 text-xs font-bold text-white hover:bg-[#16A34A]/90"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i>Bagikan Jadwal</a>
									</div>
								</article>
							@endforeach
						@else
							<div class="py-6 text-center">
								<i class="fa-regular fa-calendar-xmark text-2xl text-[#64748B]" aria-hidden="true"></i>
								<p class="mt-3 text-sm font-semibold text-[#64748B]">Belum ada agenda pada tanggal ini</p>
								<p class="mt-1 text-xs text-[#64748B]">Pilih tanggal bertanda di kalender atau tambahkan jadwal baru.</p>
							</div>
						@endif
					</div>
				</section>

				<section id="agenda-mendatang" class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm" aria-labelledby="upcoming-heading">
					<div class="border-b border-[#E2E8F0] px-5 py-4"><h2 id="upcoming-heading" class="font-bold text-[#0F172A]">Agenda Mendatang</h2><p class="mt-1 text-xs text-[#64748B]">Kegiatan berikutnya di unit Posyandu.</p></div>
					@if ($upcomingSchedules->isNotEmpty())
						<ul class="divide-y divide-[#E2E8F0]">
							@foreach ($upcomingSchedules as $schedule)
								<li><a href="{{ route('posyandu.jadwal.index', ['month' => $schedule->tgl_kegiatan->format('Y-m'), 'date' => $schedule->tgl_kegiatan->toDateString()]) }}" class="flex items-center gap-3 px-5 py-3 transition hover:bg-[#F8FAFC]">
									<span class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-lg bg-[#155DFC]/10 text-[#155DFC]"><span class="text-[9px] font-bold uppercase">{{ $schedule->tgl_kegiatan->locale('id')->translatedFormat('M') }}</span><span class="text-sm font-extrabold">{{ $schedule->tgl_kegiatan->day }}</span></span>
									<span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold text-[#0F172A]">{{ $schedule->agenda ?: 'Kegiatan Posyandu' }}</span><span class="mt-1 block truncate text-[10px] text-[#64748B]">{{ $schedule->lokasi }}</span></span>
									<i class="fa-solid fa-chevron-right text-[10px] text-[#64748B]" aria-hidden="true"></i>
								</a></li>
							@endforeach
						</ul>
					@else
						<p class="px-5 py-6 text-center text-xs text-[#64748B]">Belum ada jadwal Posyandu mendatang.</p>
					@endif
				</section>
			</div>
		</div>

		<section class="flex flex-col gap-4 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-5 sm:flex-row sm:items-center sm:p-6">
			<span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-lg text-[#155DFC] shadow-sm"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
			<div><h2 class="font-bold text-[#0F172A]">Standar Operasional 5 Meja Posyandu</h2><p class="mt-1 text-xs leading-relaxed text-[#64748B]">Pastikan alur pelayanan berjalan tertib: pendaftaran, penimbangan dan pengukuran, pencatatan, penyuluhan, serta pelayanan kesehatan.</p></div>
		</section>
	</div>

	<div id="jadwal-modal" class="fixed inset-0 z-50 items-center justify-center bg-[#0F172A]/50 p-4 {{ $editingSchedule || $errors->any() ? 'flex' : 'hidden' }}" @if($editingSchedule || $errors->any()) role="dialog" aria-modal="true" aria-labelledby="form-heading" @else aria-hidden="true" @endif>
		<div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-5 shadow-2xl sm:p-7">
			<div class="mb-5 flex items-start justify-between gap-4">
				<div>
					<p class="text-[10px] font-bold uppercase tracking-wide text-[#155DFC]">{{ $editingSchedule ? 'Perbarui agenda' : 'Agenda baru' }}</p>
					<h2 id="form-heading" class="mt-1 text-lg font-bold text-[#0F172A]">{{ $editingSchedule ? 'Ubah Jadwal Posyandu' : 'Tambah Jadwal Baru' }}</h2>
					<p class="mt-1 text-xs text-[#64748B]">Masukkan tanggal, lokasi, agenda, dan kader penanggung jawab.</p>
				</div>
				<button id="close-jadwal-modal" type="button" aria-label="Tutup formulir" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-[#64748B] transition hover:bg-[#F8FAFC] hover:text-[#0F172A]"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
			</div>
			@if ($kader->isEmpty())
				<div class="rounded-lg border border-[#F59E0B]/30 bg-[#F59E0B]/10 px-4 py-4 text-sm text-[#F59E0B]">
					<p>Belum ada kader yang terhubung ke unit Posyandu ini. Hubungkan akun yang sedang digunakan sebagai kader penanggung jawab agar dapat membuat jadwal.</p>
					<form action="{{ route('posyandu.jadwal.link-current-user') }}" method="POST" class="mt-3">
						@csrf
						<button type="submit" class="inline-flex min-h-9 items-center gap-2 rounded-lg bg-[#155DFC] px-3.5 py-2 text-xs font-bold text-white transition hover:opacity-90">
							<i class="fa-solid fa-link" aria-hidden="true"></i>Hubungkan akun saya sebagai kader
						</button>
					</form>
				</div>
			@else
				<form action="{{ route('posyandu.jadwal.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2">
					@csrf
					@if ($editingSchedule)<input type="hidden" name="id_jadwal" value="{{ $editingSchedule->id_jadwal }}">@endif
					<label class="space-y-1.5"><span class="block text-xs font-semibold text-[#64748B]">Tanggal kegiatan <span class="text-[#DC2626]">*</span></span><input type="date" name="tgl_kegiatan" min="{{ today()->format('Y-m-d') }}" value="{{ old('tgl_kegiatan', $editingSchedule?->tgl_kegiatan?->format('Y-m-d') ?? $selectedDate->format('Y-m-d')) }}" required class="min-h-10 w-full rounded-lg border border-[#E2E8F0] px-3 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">@error('tgl_kegiatan')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror</label>
					<label class="space-y-1.5"><span class="block text-xs font-semibold text-[#64748B]">Lokasi kegiatan <span class="text-[#DC2626]">*</span></span><input type="text" name="lokasi" maxlength="255" value="{{ old('lokasi', $editingSchedule?->lokasi ?? '') }}" placeholder="Contoh: Balai RW" required class="min-h-10 w-full rounded-lg border border-[#E2E8F0] px-3 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">@error('lokasi')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror</label>
					<label class="space-y-1.5 sm:col-span-2"><span class="block text-xs font-semibold text-[#64748B]">Kader penanggung jawab <span class="text-[#DC2626]">*</span></span><select name="id_kader" required class="min-h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20"><option value="">Pilih kader</option>@foreach ($kader as $person)<option value="{{ $person->id_kader }}" @selected(old('id_kader', $editingSchedule?->id_kader) == $person->id_kader)>{{ $person->nama_kader }}</option>@endforeach</select>@error('id_kader')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror</label>
					<label class="space-y-1.5 sm:col-span-2"><span class="block text-xs font-semibold text-[#64748B]">Agenda / keterangan</span><textarea name="agenda" rows="3" maxlength="2000" placeholder="Tuliskan jenis pelayanan atau kegiatan..." class="w-full rounded-lg border border-[#E2E8F0] px-3 py-2.5 text-sm outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">{{ old('agenda', $editingSchedule?->agenda) }}</textarea>@error('agenda')<span class="block text-xs text-[#DC2626]">{{ $message }}</span>@enderror</label>
					<div class="flex flex-wrap justify-end gap-2 sm:col-span-2"><button id="cancel-jadwal-modal" type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-[#E2E8F0] px-4 py-2 text-sm font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Batal</button><button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-4 py-2 text-sm font-bold text-white hover:opacity-90"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>{{ $editingSchedule ? 'Simpan Perubahan' : 'Simpan Jadwal' }}</button></div>
				</form>
			@endif
		</div>
	</div>
	<script>
		const jadwalModal = document.getElementById('jadwal-modal');
		const openJadwalModal = document.getElementById('open-jadwal-modal');
		const closeJadwalModal = document.getElementById('close-jadwal-modal');
		const cancelJadwalModal = document.getElementById('cancel-jadwal-modal');

		const showJadwalModal = () => {
			jadwalModal.classList.remove('hidden');
			jadwalModal.classList.add('flex');
			jadwalModal.setAttribute('role', 'dialog');
			jadwalModal.setAttribute('aria-modal', 'true');
			jadwalModal.removeAttribute('aria-hidden');
			jadwalModal.querySelector('input, select, textarea, button')?.focus();
		};

		const hideJadwalModal = () => {
			jadwalModal.classList.add('hidden');
			jadwalModal.classList.remove('flex');
			jadwalModal.setAttribute('aria-hidden', 'true');
			jadwalModal.removeAttribute('aria-modal');
			jadwalModal.removeAttribute('role');
			openJadwalModal.focus();
		};

		openJadwalModal.addEventListener('click', showJadwalModal);
		closeJadwalModal.addEventListener('click', hideJadwalModal);
		cancelJadwalModal?.addEventListener('click', hideJadwalModal);
		jadwalModal.addEventListener('click', (event) => {
			if (event.target === jadwalModal) hideJadwalModal();
		});
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && !jadwalModal.classList.contains('hidden')) hideJadwalModal();
		});
	</script>
@endsection