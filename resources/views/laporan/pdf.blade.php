<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan SIPANDA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; }

        /* Header */
        .header { text-align: center; border-bottom: 3px solid #2E86AB; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; color: #2E86AB; font-weight: bold; }
        .header h2 { font-size: 13px; color: #555; margin-top: 4px; }
        .header p  { font-size: 10px; color: #777; margin-top: 2px; }

        /* Info laporan */
        .info-box { background: #f0f7fb; border: 1px solid #c9e4f0; border-radius: 6px;
                    padding: 10px 14px; margin-bottom: 18px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 3px 6px; font-size: 10px; }
        .info-box td:first-child { font-weight: bold; color: #2E86AB; width: 160px; }

        /* Ringkasan statistik */
        .stat-grid { display: table; width: 100%; margin-bottom: 20px; border-spacing: 8px; }
        .stat-item { display: table-cell; background: #2E86AB; color: white; text-align: center;
                     padding: 10px; border-radius: 6px; width: 16%; }
        .stat-item .angka { font-size: 20px; font-weight: bold; }
        .stat-item .label { font-size: 9px; margin-top: 3px; }

        /* Section title */
        .section-title { background: #2E86AB; color: white; padding: 6px 12px;
                         font-size: 11px; font-weight: bold; margin-bottom: 8px;
                         border-radius: 4px; }

        /* Table */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9.5px; }
        table.data-table th { background: #2E86AB; color: white; padding: 6px 8px;
                               text-align: center; border: 1px solid #ccc; }
        table.data-table td { padding: 5px 8px; border: 1px solid #ddd; vertical-align: middle; }
        table.data-table tr:nth-child(even) td { background: #f5f9fc; }
        table.data-table .center { text-align: center; }

        /* Footer */
        .footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 12px;
                  text-align: right; font-size: 9px; color: #888; }
        .ttd { margin-top: 40px; float: right; text-align: center; }
        .ttd .garis { margin-top: 50px; border-top: 1px solid #333;
                      width: 160px; padding-top: 4px; font-size: 10px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

{{-- ═══════════════════ HALAMAN 1: RINGKASAN ═══════════════════ --}}
<div class="header">
    <h1>SISTEM INFORMASI POSYANDU ANAK DIGITAL</h1>
    <h2>LAPORAN {{ strtoupper($laporan->jenis_laporan) }} POSYANDU</h2>
    <p>Periode: {{ $laporan->periode_awal->format('d/m/Y') }} s/d {{ $laporan->periode_akhir->format('d/m/Y') }}</p>
</div>

<div class="info-box">
    <table>
        <tr>
            <td>Nama Bidan</td>
            <td>: {{ $laporan->bidan->nama_bidan ?? '-' }}</td>
            <td>NIP</td>
            <td>: {{ $laporan->nip_bidan }}</td>
        </tr>
        <tr>
            <td>Jenis Laporan</td>
            <td>: {{ $laporan->jenis_laporan }}</td>
            <td>Tanggal Cetak</td>
            <td>: {{ $laporan->tgl_cetak->format('d/m/Y') }}</td>
        </tr>
    </table>
</div>

{{-- Statistik Ringkasan --}}
<div class="section-title">📊 Ringkasan Statistik</div>
<table style="width:100%; margin-bottom:18px; border-collapse:separate; border-spacing:6px;">
    <tr>
        <td style="background:#2E86AB; color:white; text-align:center; padding:10px; border-radius:6px;">
            <div style="font-size:20px; font-weight:bold;">{{ $ringkasan['total_pemeriksaan'] }}</div>
            <div style="font-size:9px; margin-top:3px;">Total Pemeriksaan</div>
        </td>
        <td style="background:#27AE60; color:white; text-align:center; padding:10px; border-radius:6px;">
            <div style="font-size:20px; font-weight:bold;">{{ $ringkasan['total_anak_diperiksa'] }}</div>
            <div style="font-size:9px; margin-top:3px;">Anak Diperiksa</div>
        </td>
        <td style="background:#E67E22; color:white; text-align:center; padding:10px; border-radius:6px;">
            <div style="font-size:20px; font-weight:bold;">{{ $ringkasan['total_imunisasi'] }}</div>
            <div style="font-size:9px; margin-top:3px;">Total Imunisasi</div>
        </td>
        <td style="background:#8E44AD; color:white; text-align:center; padding:10px; border-radius:6px;">
            <div style="font-size:20px; font-weight:bold;">{{ $ringkasan['rata_berat_badan'] ?? '-' }}</div>
            <div style="font-size:9px; margin-top:3px;">Rata BB (kg)</div>
        </td>
        <td style="background:#C0392B; color:white; text-align:center; padding:10px; border-radius:6px;">
            <div style="font-size:20px; font-weight:bold;">{{ $ringkasan['rata_tinggi_badan'] ?? '-' }}</div>
            <div style="font-size:9px; margin-top:3px;">Rata TB (cm)</div>
        </td>
    </tr>
</table>

{{-- Imunisasi per Vaksin --}}
@if(!empty($ringkasan['imunisasi_per_vaksin']))
<div class="section-title">💉 Imunisasi per Jenis Vaksin</div>
<table class="data-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Vaksin</th>
            <th>Jumlah Pemberian</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ringkasan['imunisasi_per_vaksin'] as $vaksin => $jumlah)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $vaksin }}</td>
            <td class="center">{{ $jumlah }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="page-break"></div>

{{-- ═══════════════════ HALAMAN 2: DETAIL PEMERIKSAAN ═══════════════════ --}}
<div class="header">
    <h1>SIPANDA — Detail Pemeriksaan</h1>
    <p>Periode: {{ $laporan->periode_awal->format('d/m/Y') }} s/d {{ $laporan->periode_akhir->format('d/m/Y') }}</p>
</div>

<div class="section-title">📋 Data Pemeriksaan Balita</div>
<table class="data-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Balita</th>
            <th>Nama Ibu</th>
            <th>Tgl Periksa</th>
            <th>BB (kg)</th>
            <th>TB (cm)</th>
            <th>LK (cm)</th>
            <th>Keluhan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detail as $i => $row)
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $row['nama_anak'] }}</td>
            <td>{{ $row['nama_ibu'] }}</td>
            <td class="center">{{ $row['tgl_pemeriksaan'] }}</td>
            <td class="center">{{ $row['berat_badan'] }}</td>
            <td class="center">{{ $row['tinggi_badan'] }}</td>
            <td class="center">{{ $row['lingkar_kepala'] }}</td>
            <td>{{ $row['keluhan'] }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center; color:#999;">Tidak ada data pemeriksaan</td></tr>
        @endforelse
    </tbody>
</table>

{{-- TTD --}}
<div style="margin-top:40px; overflow:hidden;">
    <div class="ttd">
        <p>{{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        <p>Bidan Pemeriksa,</p>
        <div class="garis">
            <strong>{{ $laporan->bidan->nama_bidan ?? '-' }}</strong><br>
            NIP. {{ $laporan->nip_bidan }}
        </div>
    </div>
</div>

<div class="footer">
    <p>Dicetak oleh SIPANDA — Sistem Informasi Posyandu Anak Digital</p>
    <p>{{ now()->format('d/m/Y H:i') }}</p>
</div>

</body>
</html>