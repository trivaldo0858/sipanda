<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil NIK anak dari session login Orang Tua.
        $nikAnak = $request->session()->get('orang_tua_nik_anak');

        // Jika session tidak ada, kembalikan ke halaman login.
        if (!$nikAnak) {
            return redirect('/login')
                ->withErrors([
                    'login' => 'Silakan login terlebih dahulu.',
                ]);
        }

        // Ambil hanya data anak yang sesuai dengan session.
        $anak = Anak::with([
            'posyandu',
            'pemeriksaan',
            'imunisasi',
        ])->where('nik_anak', $nikAnak)->first();

        // Jika data anak sudah tidak ditemukan, hapus session.
        if (!$anak) {
            $request->session()->forget('orang_tua_nik_anak');

            return redirect('/login')
                ->withErrors([
                    'login' => 'Data anak tidak ditemukan. Silakan login kembali.',
                ]);
        }

        // Pemeriksaan terbaru berdasarkan relasi pemeriksaan.
        $pemeriksaanTerakhir = $anak->pemeriksaan->first();

        // Jadwal akan ditambahkan setelah struktur tabel jadwal diperiksa.
        $jadwalTerdekat = null;

        // Prediksi akan dihubungkan setelah sumber data prediksi diperiksa.
        $prediksiStunting = null;

        return view('orang_tua.dashboard', compact(
            'anak',
            'pemeriksaanTerakhir',
            'jadwalTerdekat',
            'prediksiStunting'
        ));
    }
}