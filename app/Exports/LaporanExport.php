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
        $p = Pemeriksaan::whereBetween('tgl_periksa', [$awal, $akhir])->get();
        $i = Imunisasi::whereBetween('tgl_pemberian', [$awal, $akhir])->get();
        $rataBB = $p->whereNotNull('berat_badan')->avg('berat_badan');
        $rataTB = $p->whereNotNull('tinggi_badan')->avg('tinggi_badan');
        return collect([
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
            foreach ([7,13] as $r) $s->getStyle("A{$r}:B{$r}")->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'2E86AB']]]);
            for ($i=2;$i<=15;$i++) if ($i%2==0) $s->getStyle("A{$i}:B{$i}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'EBF5FB']]]);
            $s->getStyle('A1:B15')->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'BDC3C7']]]]);
            $s->getColumnDimension('A')->setWidth(30);
            $s->getColumnDimension('B')->setWidth(25);
        }];
    }
}

class PemeriksaanSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}
    public function title(): string { return 'Data Pemeriksaan'; }
    public function headings(): array { return ['No','Nama Balita','Nama Ibu','Tanggal Periksa','Berat Badan (kg)','Tinggi Badan (cm)','Lingkar Kepala (cm)','Keluhan']; }
    public function collection()
    {
        return Pemeriksaan::whereBetween('tgl_periksa',[$this->laporan->periode_awal,$this->laporan->periode_akhir])
            ->with(['anak.orangTua'])->orderBy('tgl_periksa')->get()
            ->map(function($p,$i){ return [
                $i+1, $p->anak->nama_anak??'-', $p->anak->orangTua->nama_ibu??'-',
                $p->tgl_periksa->format('d/m/Y'),
                $p->berat_badan ? number_format($p->berat_badan,2) : '-',
                $p->tinggi_badan ? number_format($p->tinggi_badan,2) : '-',
                $p->lingkar_kepala ? number_format($p->lingkar_kepala,2) : '-',
                $p->keluhan??'-',
            ]; });
    }
    public function registerEvents(): array
    {
        return [AfterSheet::class => function(AfterSheet $e){
            $s=$e->sheet; $lr=$s->getHighestRow();
            $s->getStyle('A1:H1')->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>11],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1B4F72']],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER]]);
            for($i=2;$i<=$lr;$i++) if($i%2==0) $s->getStyle("A{$i}:H{$i}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'EBF5FB']]]);
            $s->getStyle("A1:H{$lr}")->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'BDC3C7']]]]);
            $s->getStyle("A2:A{$lr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s->getStyle("D2:D{$lr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s->getRowDimension(1)->setRowHeight(25);
        }];
    }
}

class ImunisasiSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(private Laporan $laporan) {}
    public function title(): string { return 'Data Imunisasi'; }
    public function headings(): array { return ['No','Nama Balita','Nama Ibu','Nama Vaksin','Tanggal Pemberian','Nama Bidan','Catatan']; }
    public function collection()
    {
        return Imunisasi::whereBetween('tgl_pemberian',[$this->laporan->periode_awal,$this->laporan->periode_akhir])
            ->with(['anak.orangTua','jenisVaksin','bidan'])->orderBy('tgl_pemberian')->get()
            ->map(function($item,$idx){ return [
                $idx+1, $item->anak->nama_anak??'-', $item->anak->orangTua->nama_ibu??'-',
                $item->jenisVaksin->nama_vaksin??'-', $item->tgl_pemberian->format('d/m/Y'),
                $item->bidan->nama_bidan??'-', $item->catatan??'-',
            ]; });
    }
    public function registerEvents(): array
    {
        return [AfterSheet::class => function(AfterSheet $e){
            $s=$e->sheet; $lr=$s->getHighestRow();
            $s->getStyle('A1:G1')->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>11],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1B4F72']],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER]]);
            for($i=2;$i<=$lr;$i++) if($i%2==0) $s->getStyle("A{$i}:G{$i}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'EBF5FB']]]);
            $s->getStyle("A1:G{$lr}")->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'BDC3C7']]]]);
            $s->getStyle("A2:A{$lr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s->getStyle("E2:E{$lr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s->getRowDimension(1)->setRowHeight(25);
        }];
    }
}
