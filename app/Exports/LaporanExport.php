<?php

namespace App\Exports;

use App\Models\Imunisasi;
use App\Models\Laporan;
use App\Models\Pemeriksaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanExport implements WithMultipleSheets
{
    public function __construct(private Laporan $laporan) {}

    public function sheets(): array
    {
        return [
            new RingkasanSheet($this->laporan),
            new PemeriksaanSheet($this->laporan),
            new ImunisasiSheet($this->laporan),
        ];
    }
}

// ── Sheet 1: Ringkasan ────────────────────────────────────────────────
class RingkasanSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}

    public function title(): string { return 'Ringkasan'; }

    public function headings(): array
    {
        return ['Keterangan', 'Nilai'];
    }

    public function collection()
    {
        $periodeAwal  = $this->laporan->periode_awal;
        $periodeAkhir = $this->laporan->periode_akhir;

        $pemeriksaan = Pemeriksaan::whereBetween('tgl_pemeriksaan', [$periodeAwal, $periodeAkhir])->get();
        $imunisasi   = Imunisasi::whereBetween('tgl_pemberian', [$periodeAwal, $periodeAkhir])->get();

        return collect([
            ['Nama Bidan',            $this->laporan->bidan->nama_bidan ?? '-'],
            ['Jenis Laporan',         $this->laporan->jenis_laporan],
            ['Periode Awal',          $periodeAwal->format('d/m/Y')],
            ['Periode Akhir',         $periodeAkhir->format('d/m/Y')],
            ['Tanggal Cetak',         $this->laporan->tgl_cetak->format('d/m/Y')],
            ['', ''],
            ['Total Pemeriksaan',     $pemeriksaan->count()],
            ['Total Anak Diperiksa',  $pemeriksaan->pluck('nik_anak')->unique()->count()],
            ['Total Imunisasi',       $imunisasi->count()],
            ['Rata-rata Berat Badan', ($pemeriksaan->avg('berat_badan') ?? 0) . ' kg'],
            ['Rata-rata Tinggi Badan',($pemeriksaan->avg('tinggi_badan') ?? 0) . ' cm'],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Header style
                $event->sheet->getStyle('A1:B1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E86AB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                // Border seluruh data
                $event->sheet->getStyle('A1:B11')->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}

// ── Sheet 2: Detail Pemeriksaan ───────────────────────────────────────
class PemeriksaanSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}

    public function title(): string { return 'Data Pemeriksaan'; }

    public function headings(): array
    {
        return [
            'No', 'Nama Balita', 'Nama Ibu', 'Tgl Pemeriksaan',
            'BB (kg)', 'TB (cm)', 'LK (cm)', 'Keluhan', 'Kader', 'Bidan',
        ];
    }

    public function collection()
    {
        $data = Pemeriksaan::whereBetween('tgl_pemeriksaan', [
            $this->laporan->periode_awal,
            $this->laporan->periode_akhir,
        ])
        ->with(['anak.orangTua', 'kader', 'bidan'])
        ->orderBy('tgl_pemeriksaan')
        ->get();

        return $data->map(fn ($p, $i) => [
            $i + 1,
            $p->anak->nama_anak ?? '-',
            $p->anak->orangTua->nama_ibu ?? '-',
            $p->tgl_pemeriksaan->format('d/m/Y'),
            $p->berat_badan ?? '-',
            $p->tinggi_badan ?? '-',
            $p->lingkar_kepala ?? '-',
            $p->keluhan ?? '-',
            $p->kader->nama_kader ?? '-',
            $p->bidan->nama_bidan ?? '-',
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E86AB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}

// ── Sheet 3: Detail Imunisasi ─────────────────────────────────────────
class ImunisasiSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}

    public function title(): string { return 'Data Imunisasi'; }

    public function headings(): array
    {
        return ['No', 'Nama Balita', 'Nama Ibu', 'Vaksin', 'Tgl Pemberian', 'Bidan'];
    }

    public function collection()
    {
        $data = Imunisasi::whereBetween('tgl_pemberian', [
            $this->laporan->periode_awal,
            $this->laporan->periode_akhir,
        ])
        ->with(['anak.orangTua', 'jenisVaksin', 'bidan'])
        ->orderBy('tgl_pemberian')
        ->get();

        return $data->map(fn ($i, $idx) => [
            $idx + 1,
            $i->anak->nama_anak ?? '-',
            $i->anak->orangTua->nama_ibu ?? '-',
            $i->jenisVaksin->nama_vaksin ?? '-',
            $i->tgl_pemberian->format('d/m/Y'),
            $i->bidan->nama_bidan ?? '-',
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E86AB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}