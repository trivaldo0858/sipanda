@extends('layouts.posyandu')

@section('title', 'Laporan Bulanan')

@section('content')
    @php
        $unitName = auth()->user()->posyandu?->nama_posyandu ?? 'Posyandu Mawar 01';
        $stats = [
            ['letter' => 'S', 'label' => 'Sasaran Balita', 'value' => '142', 'detail' => '100% wilayah', 'icon' => 'fa-children'],
            ['letter' => 'K', 'label' => 'Punya Buku KIA', 'value' => '140', 'detail' => '98,5% kepemilikan', 'icon' => 'fa-book-medical'],
            ['letter' => 'D', 'label' => 'Datang Ditimbang', 'value' => '132', 'detail' => '92,9% partisipasi kunjungan', 'icon' => 'fa-scale-balanced'],
            ['letter' => 'N', 'label' => 'Naik Berat Badannya', 'value' => '118', 'detail' => '89,3% tumbuh sesuai kurva', 'icon' => 'fa-arrow-trend-up'],
        ];
        $nutrition = [
            ['label' => 'Gizi Baik (Normal)', 'count' => 120, 'percentage' => '84,5%', 'color' => 'bg-[#16A34A]', 'track' => 'bg-[#16A34A]/10', 'text' => 'text-[#16A34A]'],
            ['label' => 'Risiko Stunting (Pendek/Sangat Pendek)', 'count' => 8, 'percentage' => '5,6%', 'color' => 'bg-[#F59E0B]', 'track' => 'bg-[#F59E0B]/10', 'text' => 'text-[#F59E0B]'],
            ['label' => 'Gizi Kurang / Wasting', 'count' => 4, 'percentage' => '2,8%', 'color' => 'bg-[#F59E0B]', 'track' => 'bg-[#F59E0B]/10', 'text' => 'text-[#F59E0B]'],
            ['label' => 'BGM (Bawah Garis Merah)', 'count' => 2, 'percentage' => '1,4%', 'color' => 'bg-[#DC2626]', 'track' => 'bg-[#DC2626]/10', 'text' => 'text-[#DC2626]'],
        ];
        $rtRows = [
            ['rt' => '01', 's' => 36, 'k' => 35, 'd' => 34, 'n' => 31, 'bgm' => 1, 'kurang' => 1, 'status' => 'Perlu pemantauan', 'statusClass' => 'bg-[#F59E0B]/10 text-[#F59E0B] ring-[#F59E0B]/30'],
            ['rt' => '02', 's' => 38, 'k' => 38, 'd' => 36, 'n' => 32, 'bgm' => 0, 'kurang' => 1, 'status' => 'Baik', 'statusClass' => 'bg-[#16A34A]/10 text-[#16A34A] ring-[#16A34A]/30'],
            ['rt' => '03', 's' => 34, 'k' => 33, 'd' => 30, 'n' => 27, 'bgm' => 1, 'kurang' => 1, 'status' => 'Perlu pemantauan', 'statusClass' => 'bg-[#F59E0B]/10 text-[#F59E0B] ring-[#F59E0B]/30'],
            ['rt' => '04', 's' => 34, 'k' => 34, 'd' => 32, 'n' => 28, 'bgm' => 0, 'kurang' => 1, 'status' => 'Baik', 'statusClass' => 'bg-[#16A34A]/10 text-[#16A34A] ring-[#16A34A]/30'],
        ];
    @endphp

    <div class="space-y-6 pb-8">
        <section class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-[#155DFC]">
                    <i class="fa-solid fa-file-circle-check" aria-hidden="true"></i> Rekapitulasi &amp; Pelaporan Resmi
                </p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-[#0F172A] sm:text-3xl">Laporan Bulanan</h1>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-[#64748B]">Rekap data penimbangan, cakupan SKDN, dan status gizi balita untuk pemantauan wilayah.</p>
            </div>
            <div class="report-actions flex flex-col gap-2 sm:flex-row sm:flex-wrap xl:justify-end">
                <button type="button" disabled title="Integrasi pengiriman ke Puskesmas belum tersedia." class="inline-flex min-h-10 cursor-not-allowed items-center justify-center gap-2 rounded-lg border border-[#E2E8F0] bg-white px-3.5 py-2 text-xs font-bold text-[#64748B] sm:text-sm">
                    <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>Kirim Ke Puskesmas / Bidan Pembina
                </button>
                <button type="button" disabled title="Integrasi ekspor Excel belum tersedia." class="inline-flex min-h-10 cursor-not-allowed items-center justify-center gap-2 rounded-lg border border-[#E2E8F0] bg-white px-3.5 py-2 text-xs font-bold text-[#64748B] sm:text-sm">
                    <i class="fa-solid fa-file-excel" aria-hidden="true"></i>Unduh Laporan Excel (.XLSX)
                </button>
                <button type="button" onclick="window.print()" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#155DFC]/90 focus:outline-none focus:ring-2 focus:ring-[#155DFC] focus:ring-offset-2 sm:text-sm">
                    <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>Cetak Laporan PDF (Resmi Kemenkes)
                </button>
            </div>
        </section>

        <section class="report-filters rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-sm sm:p-5" aria-labelledby="filter-heading">
            <div class="mb-4 flex items-center gap-3 border-b border-[#E2E8F0] pb-4">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-sliders" aria-hidden="true"></i></span>
                <div>
                    <h2 id="filter-heading" class="text-sm font-extrabold text-[#0F172A]">Parameter Rekapitulasi &amp; Dokumen</h2>
                    <p class="mt-0.5 text-xs text-[#64748B]">Tentukan periode dan format laporan yang akan ditampilkan.</p>
                </div>
            </div>
            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 xl:grid-cols-[1fr_0.8fr_1.5fr_auto]">
                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-[#64748B]">Pilih Bulan</span>
                    <select name="bulan" class="min-h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 py-2 text-sm text-[#0F172A] outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $month)
                            <option value="{{ $month }}" @selected(request('bulan', 'Mei') === $month)>{{ $month }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-[#64748B]">Pilih Tahun</span>
                    <select name="tahun" class="min-h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 py-2 text-sm text-[#0F172A] outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        <option value="2025" @selected(request('tahun', '2026') === '2025')>2025</option>
                        <option value="2026" @selected(request('tahun', '2026') === '2026')>2026</option>
                    </select>
                </label>
                <label class="block sm:col-span-2 xl:col-span-1">
                    <span class="mb-1.5 block text-xs font-semibold text-[#64748B]">Jenis Laporan</span>
                    <select name="jenis_laporan" class="min-h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 py-2 text-sm text-[#0F172A] outline-none focus:border-[#155DFC] focus:ring-2 focus:ring-[#155DFC]/20">
                        <option value="skdn" @selected(request('jenis_laporan', 'skdn') === 'skdn')>Laporan Rekapitulasi SKDN &amp; KMS</option>
                        <option value="gizi" @selected(request('jenis_laporan') === 'gizi')>Laporan Status Gizi Balita</option>
                    </select>
                </label>
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-[#155DFC] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#155DFC]/90 sm:col-span-2 xl:col-span-1">
                    <i class="fa-solid fa-filter" aria-hidden="true"></i>Tampilkan Rekapitulasi Data
                </button>
            </form>
            <div class="mt-4 flex flex-col gap-3 border-t border-[#E2E8F0] pt-4 sm:flex-row sm:items-center sm:gap-6">
                <span class="text-xs font-bold text-[#64748B]">Opsi format:</span>
                <label class="inline-flex items-center gap-2 text-xs font-medium text-[#64748B]">
                    <input type="checkbox" name="format_kemenkes" checked class="h-4 w-4 rounded border-[#E2E8F0] text-[#155DFC] focus:ring-[#155DFC]">
                    Format Resmi KPPPA/Kemenkes
                </label>
                <label class="inline-flex items-center gap-2 text-xs font-medium text-[#64748B]">
                    <input type="checkbox" name="format_pbb" class="h-4 w-4 rounded border-[#E2E8F0] text-[#155DFC] focus:ring-[#155DFC]">
                    Format PBB (Scaling Up Nutrition)
                </label>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4" aria-label="Indikator kunci SKDN Posyandu">
            @foreach ($stats as $stat)
                <article class="rounded-2xl border border-[#E2E8F0] border-t-[3px] border-t-[#155DFC] bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-[#64748B]">{{ $stat['letter'] }} <span class="font-medium normal-case tracking-normal">/ {{ $stat['label'] }}</span></p>
                            <p class="mt-3 text-2xl font-extrabold tracking-tight text-[#0F172A]">{{ $stat['value'] }} <span class="text-sm font-semibold text-[#64748B]">Anak</span></p>
                        </div>
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                    </div>
                    <p class="mt-3 flex items-center gap-1.5 text-xs font-semibold text-[#64748B]"><i class="fa-solid fa-circle-check text-[#16A34A]" aria-hidden="true"></i>{{ $stat['detail'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid items-start gap-5 xl:grid-cols-2" aria-label="Detail status gizi dan rekapitulasi">
            <article class="rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-extrabold text-[#0F172A]">Ringkasan Status Gizi Balita</h2>
                        <p class="mt-1 text-xs text-[#64748B]">Standar antropometri WHO / Kemenkes</p>
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#16A34A]/10 text-[#16A34A]"><i class="fa-solid fa-heart-pulse" aria-hidden="true"></i></span>
                </div>
                <div class="mt-5 space-y-4">
                    @foreach ($nutrition as $item)
                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                                <span class="font-semibold text-[#0F172A]">{{ $item['label'] }}</span>
                                <span class="shrink-0 font-bold {{ $item['text'] }}">{{ $item['count'] }} Anak <span class="font-medium text-[#64748B]">({{ $item['percentage'] }})</span></span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full {{ $item['track'] }}" role="progressbar" aria-label="{{ $item['label'] }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (float) rtrim(str_replace(',', '.', $item['percentage']), '%') }}">
                                <div class="h-full rounded-full {{ $item['color'] }}" style="width: {{ str_replace(',', '.', $item['percentage']) }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex gap-3 rounded-xl border border-[#155DFC]/20 bg-[#155DFC]/10 p-4 text-[#0F172A]">
                    <i class="fa-solid fa-circle-info mt-0.5 text-[#155DFC]" aria-hidden="true"></i>
                    <p class="text-xs leading-5"><span class="font-extrabold">Catatan Kemenkes:</span> Balita dengan indikasi stunting, gizi kurang, atau BGM perlu dipantau berkala dan dirujuk sesuai pedoman pelayanan gizi.</p>
                </div>
            </article>

            <div class="space-y-5">
                <article class="rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-extrabold text-[#0F172A]">Bagan SKDN Bulanan</h2>
                            <p class="mt-1 text-xs text-[#64748B]">Cakupan indikator pada periode Mei 2026</p>
                        </div>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F8FAFC] text-[#155DFC]"><i class="fa-solid fa-chart-column" aria-hidden="true"></i></span>
                    </div>
                    <div class="mt-5 grid h-44 grid-cols-4 items-end gap-4 border-b border-l border-[#E2E8F0] px-3 pb-2">
                        @foreach ([['label' => 'S', 'value' => '100%', 'height' => '100%'], ['label' => 'K', 'value' => '98,5%', 'height' => '98.5%'], ['label' => 'D', 'value' => '92,9%', 'height' => '92.9%'], ['label' => 'N', 'value' => '89,3%', 'height' => '89.3%']] as $bar)
                            <div class="flex h-full flex-col items-center justify-end gap-2">
                                <span class="text-[10px] font-bold text-[#64748B]">{{ $bar['value'] }}</span>
                                <div class="flex w-full max-w-12 flex-1 items-end justify-center rounded-t-md bg-[#F8FAFC]">
                                    <div class="w-full rounded-t-md bg-[#155DFC]" style="height: {{ $bar['height'] }}"></div>
                                </div>
                                <span class="text-xs font-extrabold text-[#64748B]">{{ $bar['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex flex-wrap justify-center gap-x-5 gap-y-2 text-[11px] text-[#64748B]">
                        <span><b class="text-[#0F172A]">S</b> Sasaran</span><span><b class="text-[#0F172A]">K</b> KMS</span><span><b class="text-[#0F172A]">D</b> Ditimbang</span><span><b class="text-[#0F172A]">N</b> Naik BB</span>
                    </div>
                </article>
                <article class="rounded-2xl border border-[#F59E0B]/30 bg-[#F59E0B]/10 p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-[#F59E0B]"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></span>
                        <div>
                            <h2 class="text-sm font-extrabold text-[#0F172A]">Aksi &amp; Catatan Pendampingan</h2>
                            <p class="mt-1 text-xs leading-5 text-[#64748B]">2 balita BGM dan 8 balita berisiko stunting perlu pemantauan lanjutan. Koordinasikan kunjungan rumah serta konsultasi dengan Bidan Pembina.</p>
                            <a href="{{ route('posyandu.pemeriksaan.index') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-[#155DFC] hover:text-[#155DFC]/80">Lihat data pemeriksaan <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm" aria-labelledby="rt-table-heading">
            <div class="flex flex-col gap-2 border-b border-[#E2E8F0] p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <h2 id="rt-table-heading" class="text-base font-extrabold text-[#0F172A]">Data Rekapitulasi Tingkat Rukun Tetangga</h2>
                    <p class="mt-1 text-xs text-[#64748B]">{{ $unitName }} <span class="mx-1 text-[#E2E8F0]">•</span> Mei 2026</p>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-[#155DFC]/10 px-3 py-1.5 text-[11px] font-bold text-[#155DFC]"><i class="fa-solid fa-circle-check text-[#16A34A]" aria-hidden="true"></i> Rekapitulasi tervalidasi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] border-collapse text-left text-xs">
                    <thead class="bg-[#F8FAFC] text-[10px] uppercase tracking-wide text-[#64748B]">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-extrabold">RT</th>
                            <th scope="col" class="px-4 py-3 text-right font-extrabold">Total Balita (S)</th>
                            <th scope="col" class="px-4 py-3 text-right font-extrabold">Punya KMS (K)</th>
                            <th scope="col" class="px-4 py-3 text-right font-extrabold">Ditimbang (D)</th>
                            <th scope="col" class="px-4 py-3 text-right font-extrabold">Naik BB (N)</th>
                            <th scope="col" class="px-4 py-3 text-right font-extrabold">BGM</th>
                            <th scope="col" class="px-4 py-3 text-right font-extrabold">Gizi Kurang</th>
                            <th scope="col" class="px-4 py-3 font-extrabold">Status Wilayah</th>
                            <th scope="col" class="px-4 py-3 font-extrabold">Aksi Laporan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] text-[#0F172A]">
                        @foreach ($rtRows as $row)
                            <tr class="transition hover:bg-[#F8FAFC]">
                                <th scope="row" class="whitespace-nowrap px-4 py-3.5 font-bold text-[#0F172A]">RT {{ $row['rt'] }}</th>
                                <td class="px-4 py-3.5 text-right font-semibold">{{ $row['s'] }}</td>
                                <td class="px-4 py-3.5 text-right">{{ $row['k'] }}</td>
                                <td class="px-4 py-3.5 text-right">{{ $row['d'] }}</td>
                                <td class="px-4 py-3.5 text-right font-semibold text-[#16A34A]">{{ $row['n'] }}</td>
                                <td class="px-4 py-3.5 text-right {{ $row['bgm'] > 0 ? 'font-bold text-[#DC2626]' : '' }}">{{ $row['bgm'] }}</td>
                                <td class="px-4 py-3.5 text-right">{{ $row['kurang'] }}</td>
                                <td class="px-4 py-3.5"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold ring-1 ring-inset {{ $row['statusClass'] }}">{{ $row['status'] }}</span></td>
                                <td class="px-4 py-3.5"><button type="button" disabled aria-label="Laporan RT {{ $row['rt'] }}" class="cursor-not-allowed font-bold text-[#64748B]"><i class="fa-regular fa-eye mr-1" aria-hidden="true"></i>Detail</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-[#E2E8F0] bg-[#155DFC]/10 font-extrabold text-[#0F172A]">
                        <tr>
                            <th scope="row" class="whitespace-nowrap px-4 py-4 text-left">Total {{ $unitName }}</th>
                            <td class="px-4 py-4 text-right">142</td>
                            <td class="px-4 py-4 text-right">140</td>
                            <td class="px-4 py-4 text-right">132</td>
                            <td class="px-4 py-4 text-right text-[#16A34A]">118</td>
                            <td class="px-4 py-4 text-right text-[#DC2626]">2</td>
                            <td class="px-4 py-4 text-right">4</td>
                            <td class="px-4 py-4 text-[#64748B]">4 RT terdata</td>
                            <td class="px-4 py-4">—</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p class="border-t border-[#E2E8F0] px-4 py-3 text-[11px] text-[#64748B] sm:px-5">Data rekap ditampilkan sebagai ringkasan periode laporan terpilih.</p>
        </section>

        <footer class="rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-[#0F172A]">Pengesahan Laporan</p>
                    <p class="mt-1 text-xs text-[#64748B]">Dokumen rekapitulasi Posyandu untuk periode Mei 2026.</p>
                </div>
                <div class="grid flex-1 gap-6 sm:grid-cols-2 lg:max-w-2xl">
                    <div class="text-center">
                        <p class="text-xs font-semibold text-[#64748B]">Ketua {{ $unitName }}</p>
                        <div class="mx-auto my-3 flex h-12 max-w-48 items-center justify-center border-b border-dashed border-[#E2E8F0] text-[10px] font-semibold uppercase tracking-widest text-[#155DFC]"><i class="fa-solid fa-stamp mr-1.5" aria-hidden="true"></i> Tanda tangan digital</div>
                        <p class="text-xs font-extrabold text-[#0F172A]">Ketua Posyandu</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-semibold text-[#64748B]">Bidan Pembina Wilayah</p>
                        <div class="mx-auto my-3 flex h-12 max-w-48 items-center justify-center border-b border-dashed border-[#E2E8F0] text-[10px] font-semibold uppercase tracking-widest text-[#155DFC]"><i class="fa-solid fa-stamp mr-1.5" aria-hidden="true"></i> Tanda tangan digital</div>
                        <p class="text-xs font-extrabold text-[#0F172A]">Bidan Pembina</p>
                    </div>
                </div>
            </div>
            <p class="mt-5 border-t border-[#E2E8F0] pt-3 text-center text-[10px] text-[#64748B]">Rekapitulasi Bulanan Posyandu</p>
        </footer>
    </div>

    <style>
        @media print {
            aside,
            .report-actions,
            .report-filters {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            body main {
                padding: 0 !important;
            }

            article,
            section,
            footer {
                break-inside: avoid;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection