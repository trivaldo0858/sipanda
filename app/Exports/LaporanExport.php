<?php

namespace App\Exports;

use App\Models\Imunisasi;
use App\Models\Laporan;
use App\Models\Pemeriksaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
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

class RingkasanSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}
    public function title(): string { return 'Ringkasan'; }
    public function headings(): array { return ['Keterangan', 'Nilai']; }
    public function collection()
    {
        $awal  = $this->laporan->periode_awal;
        $akhir = $this->laporan->periode_akhir;
        $idPosyandu = $this->laporan->id_posyandu;
        $p = Pemeriksaan::whereBetween('tgl_periksa', [$awal, $akhir])->where('id_posyandu', $idPosyandu)->get();
        $nikAnak = \App\Models\Anak::whereHas('orangTua.pengguna', fn($q) => $q->where('id_posyandu', $this->laporan->id_posyandu))->pluck('nik_anak');
        $i = Imunisasi::whereBetween('tgl_pemberian', [$awal, $akhir])->whereIn('nik_anak', $nikAnak)->get();
        $rataBB = $p->whereNotNull('berat_badan')->avg('berat_badan');
        $rataTB = $p->whereNotNull('tinggi_badan')->avg('tinggi_badan');
        return collect([
            ['Nama Posyandu',            $this->laporan->posyandu?->nama_posyandu ?? '-'],
            ['Nama Petugas',             $this->laporan->bidan?->nama_bidan ?? 'Kader'],
            ['Jenis Laporan',            $this->laporan->jenis_laporan],
            ['Periode Awal',             $awal->format('d/m/Y')],
            ['Periode Akhir',            $akhir->format('d/m/Y')],
            ['Tanggal Cetak',            $this->laporan->tgl_cetak->format('d/m/Y')],
            ['', ''],
            ['REKAPITULASI PEMERIKSAAN', ''],
            ['Total Pemeriksaan',        $p->count()],
            ['Total Balita Diperiksa',   $p->pluck('nik_anak')->unique()->count()],
            ['Rata-rata Berat Badan',    $rataBB ? round($rataBB, 2).' kg' : '-'],
            ['Rata-rata Tinggi Badan',   $rataTB ? round($rataTB, 2).' cm' : '-'],
            ['', ''],
            ['REKAPITULASI IMUNISASI',   ''],
            ['Total Imunisasi',          $i->count()],
            ['Total Balita Diimunisasi', $i->pluck('nik_anak')->unique()->count()],
        ]);
    }
    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $e) {
            $s = $e->sheet;
            $s->getStyle('A1:B1')->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>12],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1B4F72']],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER]]);
            foreach ([8,14] as $r) $s->getStyle("A{$r}:B{$r}")->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'2E86AB']]]);
            for ($i=2;$i<=16;$i++) if ($i%2==0) $s->getStyle("A{$i}:B{$i}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'EBF5FB']]]);
            $s->getStyle('A1:B16')->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'BDC3C7']]]]);
            $s->getColumnDimension('A')->setWidth(30);
            $s->getColumnDimension('B')->setWidth(25);
        }];
    }
}

class PemeriksaanSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}
    public function title(): string { return 'Data Pemeriksaan'; }
    public function headings(): array
    {
        return ['No', 'Nama Balita', 'Nama Ibu', 'Tanggal Periksa', 'BB (kg)', 'TB (cm)', 'LK (cm)', 'Posyandu'];
    }
    public function collection()
    {
        $namaPosyandu = $this->laporan->posyandu?->nama_posyandu ?? '-';
        $idPosyandu = $this->laporan->id_posyandu;
        return Pemeriksaan::whereBetween('tgl_periksa', [
            $this->laporan->periode_awal,
            $this->laporan->periode_akhir,
        ])
        ->where('id_posyandu', $idPosyandu)
        ->with(['anak.orangTua'])
        ->orderBy('tgl_periksa')
        ->get()
        ->map(function ($p, $i) use ($namaPosyandu) {
            return [
                $i + 1,
                $p->anak->nama_anak ?? '-',
                $p->anak->orangTua->nama_ibu ?? '-',
                $p->tgl_periksa->format('d/m/Y'),
                $p->berat_badan ? number_format($p->berat_badan, 2) : '-',
                $p->tinggi_badan ? number_format($p->tinggi_badan, 2) : '-',
                $p->lingkar_kepala ? number_format($p->lingkar_kepala, 2) : '-',
                $namaPosyandu,
            ];
        });
    }
    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $e) {
            $s = $e->sheet;
            $lr = $s->getHighestRow();
            $s->insertNewRowBefore(1, 1);
            $s->mergeCells('A1:H1');
            $s->setCellValue('A1', 'DATA PEMERIKSAAN POSYANDU');
            $s->getStyle('A1')->applyFromArray([
                'font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>13],
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0D47A1']],
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            ]);
            $s->getRowDimension(1)->setRowHeight(28);
            $s->getStyle('A2:H2')->applyFromArray([
                'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>11],
                'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1B4F72']],
                'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            ]);
            $s->getRowDimension(2)->setRowHeight(22);
            for ($i=3;$i<=$lr+1;$i++) {
                if ($i%2==1) $s->getStyle("A{$i}:H{$i}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'EBF5FB']]]);
            }
            $s->getStyle("A1:H".($lr+1))->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'BDC3C7']]]]);
            $s->getStyle("A3:A".($lr+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s->getStyle("D3:D".($lr+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }];
    }
}

class ImunisasiSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}
    public function title(): string { return 'Data Imunisasi'; }
    public function headings(): array
    {
        return ['No', 'Nama Balita', 'Nama Ibu', 'Nama Vaksin', 'Tanggal Pemberian', 'Nama Bidan', 'Catatan'];
    }
    public function collection()
    {
        $nikAnak = \App\Models\Anak::whereHas('orangTua.pengguna', fn($q) => $q->where('id_posyandu', $this->laporan->id_posyandu))->pluck('nik_anak');
        return Imunisasi::whereBetween('tgl_pemberian', [
            $this->laporan->periode_awal,
            $this->laporan->periode_akhir,
        ])
        ->whereIn('nik_anak', $nikAnak)
        ->with(['anak.orangTua', 'jenisVaksin', 'bidan'])
        ->orderBy('tgl_pemberian')
        ->get()
        ->map(function ($item, $idx) {
            return [
                $idx + 1,
                $item->anak->nama_anak ?? '-',
                $item->anak->orangTua->nama_ibu ?? '-',
                $item->jenisVaksin->nama_vaksin ?? '-',
                $item->tgl_pemberian->format('d/m/Y'),
                $item->bidan->nama_bidan ?? '-',
                $item->catatan ?? '-',
            ];
        });
    }
    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $e) {
            $s = $e->sheet;
            $lr = $s->getHighestRow();
            $s->insertNewRowBefore(1, 1);
            $s->mergeCells('A1:G1');
            $s->setCellValue('A1', 'DATA IMUNISASI POSYANDU');
            $s->getStyle('A1')->applyFromArray([
                'font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>13],
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'4A148C']],
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            ]);
            $s->getRowDimension(1)->setRowHeight(28);
            $s->getStyle('A2:G2')->applyFromArray([
                'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>11],
                'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1B4F72']],
                'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            ]);
            $s->getRowDimension(2)->setRowHeight(22);
            for ($i=3;$i<=$lr+1;$i++) {
                if ($i%2==1) $s->getStyle("A{$i}:G{$i}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'F3E5F5']]]);
            }
            $s->getStyle("A1:G".($lr+1))->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'BDC3C7']]]]);
            $s->getStyle("A3:A".($lr+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s->getStyle("E3:E".($lr+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }];
    }
}