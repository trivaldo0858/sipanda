<?php

namespace Tests\Unit;

use App\Models\Anak;
use App\Models\Pemeriksaan;
use App\Services\AnthropometricService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnthropometricServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('referensi_antropometri_who');

        Schema::create('referensi_antropometri_who', function (Blueprint $table) {
            $table->id();
            $table->string('kode_referensi')->unique();
            $table->string('indikator');
            $table->string('jenis_kelamin', 1);
            $table->unsignedInteger('umur_hari')->nullable();
            $table->decimal('nilai_ukur_cm', 6, 1)->nullable();
            $table->string('jenis_ukuran', 1)->nullable();
            $table->decimal('l', 12, 8);
            $table->decimal('m', 12, 8);
            $table->decimal('s', 12, 8);
            $table->string('sumber_file')->nullable();
        });

        $this->insertReference('TB_U', 80.0, 'T', 788);
        $this->insertReference('BB_U', 20.0, null, 788);
        $this->insertReference('BB_PB_TB', 20.0, 'T', null, 80.0);
        $this->insertReference('IMT_U', 31.25, 'T', 788);
        $this->insertReference('LK_U', 47.44, null, 788);
    }

    private function insertReference(
        string $indikator,
        float $median,
        ?string $jenisUkuran,
        ?int $umurHari,
        ?float $nilaiUkurCm = null
    ): void {
        DB::table('referensi_antropometri_who')->insert([
            'kode_referensi' => $indikator . '-P-' . ($umurHari ?? $nilaiUkurCm),
            'indikator' => $indikator,
            'jenis_kelamin' => 'P',
            'umur_hari' => $umurHari,
            'nilai_ukur_cm' => $nilaiUkurCm,
            'jenis_ukuran' => $jenisUkuran,
            'l' => 1,
            'm' => $median,
            's' => 0.1,
            'sumber_file' => 'fixture_pengujian',
        ]);
    }

    public function test_all_five_anthropometric_indicators_can_be_analyzed(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 1,
            'nik_anak' => '1111111111111111',
            'tgl_periksa' => '2026-10-30',
            'berat_badan' => 20.0,
            'tinggi_badan' => 80.0,
            'lingkar_kepala' => 47.44,
        ]);

        $hasil = app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);

        $this->assertTrue($hasil['tersedia']);
        $this->assertSame(5, $hasil['jumlah_indikator']);
        $this->assertSame(5, $hasil['jumlah_indikator_tersedia']);

        $this->assertFalse($hasil['status_stunting']);

        foreach ([
            'TB_U',
            'BB_U',
            'BB_PB_TB',
            'IMT_U',
            'LK_U',
        ] as $indikator) {
            $this->assertTrue(
                $hasil['indikator'][$indikator]['tersedia']
            );

            $this->assertEquals(
                0.0,
                $hasil['indikator'][$indikator]['z_score'],
                "Z-score {$indikator} seharusnya 0."
            );
        }
    }

    public function test_analysis_continues_when_weight_is_missing(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 2,
            'nik_anak' => '1111111111111111',
            'tgl_periksa' => '2026-10-30',
            'berat_badan' => null,
            'tinggi_badan' => 80.0,
            'lingkar_kepala' => 47.44,
        ]);

        $hasil = app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);

        $this->assertTrue($hasil['tersedia']);

        $this->assertSame(5, $hasil['jumlah_indikator']);

        $this->assertSame(
            2,
            $hasil['jumlah_indikator_tersedia']
        );

        $this->assertTrue(
            $hasil['indikator']['TB_U']['tersedia']
        );

        $this->assertFalse(
            $hasil['indikator']['BB_U']['tersedia']
        );

        $this->assertFalse(
            $hasil['indikator']['BB_PB_TB']['tersedia']
        );

        $this->assertFalse(
            $hasil['indikator']['IMT_U']['tersedia']
        );

        $this->assertTrue(
            $hasil['indikator']['LK_U']['tersedia']
        );
    }

    public function test_extreme_values_are_flagged_for_review(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 3,
            'nik_anak' => '1111111111111111',
            'tgl_periksa' => '2026-10-30',
            'berat_badan' => 40.0,
            'tinggi_badan' => 80.0,
            'lingkar_kepala' => 47.44,
        ]);

        $hasil = app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);

        // BB/U harus terdeteksi ekstrem.
        $this->assertTrue(
            $hasil['indikator']['BB_U']['nilai_ekstrem']
        );

        $this->assertTrue(
            $hasil['indikator']['BB_U']['perlu_tinjau_ulang']
        );

        // BB/PB-TB harus meminta pemeriksaan ulang.
        $this->assertTrue(
            $hasil['indikator']['BB_PB_TB']['nilai_ekstrem']
        );

        $this->assertTrue(
            $hasil['indikator']['BB_PB_TB']['perlu_tinjau_ulang']
        );

        $this->assertSame(
            'Z-score berada di atas +5 SD.',
            $hasil['indikator']['BB_PB_TB']['alasan_tinjau_ulang']
        );

        // IMT/U harus meminta pemeriksaan ulang.
        $this->assertTrue(
            $hasil['indikator']['IMT_U']['nilai_ekstrem']
        );

        $this->assertTrue(
            $hasil['indikator']['IMT_U']['perlu_tinjau_ulang']
        );

        // TB/U dan LK/U tidak terpengaruh oleh perubahan berat badan.
        $this->assertFalse(
            $hasil['indikator']['TB_U']['perlu_tinjau_ulang']
        );

        $this->assertFalse(
            $hasil['indikator']['LK_U']['perlu_tinjau_ulang']
        );
    }

    public function test_analysis_rejects_different_child_identity(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 4,
            'nik_anak' => '2222222222222222',
            'tgl_periksa' => '2026-10-30',
            'berat_badan' => 20.0,
            'tinggi_badan' => 80.0,
            'lingkar_kepala' => 47.44,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Data pemeriksaan tidak sesuai dengan anak.'
        );

        app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);
    }

    public function test_analysis_rejects_examination_before_birth_date(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 5,
            'nik_anak' => '1111111111111111',
            'tgl_periksa' => '2024-09-01',
            'berat_badan' => 20.0,
            'tinggi_badan' => 80.0,
            'lingkar_kepala' => 47.44,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Tanggal pemeriksaan tidak boleh sebelum tanggal lahir.'
        );

        app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);
    }
    public function test_analysis_is_unavailable_when_child_exceeds_who_age_limit(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 6,
            'nik_anak' => '1111111111111111',
            'tgl_periksa' => '2029-09-03',
            'berat_badan' => 20.0,
            'tinggi_badan' => 80.0,
            'lingkar_kepala' => 47.44,
        ]);

        $hasil = app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);

        $this->assertFalse($hasil['tersedia']);

        $this->assertSame(5, $hasil['jumlah_indikator']);

        $this->assertSame(
            0,
            $hasil['jumlah_indikator_tersedia']
        );

        $this->assertNull($hasil['status_stunting']);

        foreach ($hasil['indikator'] as $indikator) {
            $this->assertFalse($indikator['tersedia']);
        }
    }

    public function test_analysis_continues_when_height_is_missing(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 7,
            'nik_anak' => '1111111111111111',
            'tgl_periksa' => '2026-10-30',
            'berat_badan' => 20.0,
            'tinggi_badan' => null,
            'lingkar_kepala' => 47.44,
        ]);

        $hasil = app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);

        $this->assertTrue($hasil['tersedia']);

        $this->assertSame(
            2,
            $hasil['jumlah_indikator_tersedia']
        );

        $this->assertFalse($hasil['indikator']['TB_U']['tersedia']);
        $this->assertTrue($hasil['indikator']['BB_U']['tersedia']);
        $this->assertFalse($hasil['indikator']['BB_PB_TB']['tersedia']);
        $this->assertFalse($hasil['indikator']['IMT_U']['tersedia']);
        $this->assertTrue($hasil['indikator']['LK_U']['tersedia']);
    }

    public function test_analysis_continues_when_head_circumference_is_missing(): void
    {
        $anak = new Anak();

        $anak->forceFill([
            'nik_anak' => '1111111111111111',
            'tgl_lahir' => '2024-09-02',
            'jenis_kelamin' => 'P',
        ]);

        $pemeriksaan = new Pemeriksaan();

        $pemeriksaan->forceFill([
            'id_periksa' => 8,
            'nik_anak' => '1111111111111111',
            'tgl_periksa' => '2026-10-30',
            'berat_badan' => 20.0,
            'tinggi_badan' => 80.0,
            'lingkar_kepala' => null,
        ]);

        $hasil = app(AnthropometricService::class)
            ->analyzeAll($anak, $pemeriksaan);

        $this->assertTrue($hasil['tersedia']);

        $this->assertSame(
            4,
            $hasil['jumlah_indikator_tersedia']
        );

        $this->assertTrue($hasil['indikator']['TB_U']['tersedia']);
        $this->assertTrue($hasil['indikator']['BB_U']['tersedia']);
        $this->assertTrue($hasil['indikator']['BB_PB_TB']['tersedia']);
        $this->assertTrue($hasil['indikator']['IMT_U']['tersedia']);
        $this->assertFalse($hasil['indikator']['LK_U']['tersedia']);
    }
}
