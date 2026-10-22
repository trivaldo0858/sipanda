<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil identitas anak dari session login Orang Tua.
        $nikAnak = $request->session()->get('orang_tua_nik_anak');

        if (!$nikAnak) {
            return redirect('/login')->withErrors([
                'login' => 'Silakan login terlebih dahulu.',
            ]);
        }

        // Ambil profil anak beserta Posyandu tempat anak terdaftar.
        $anak = Anak::with('posyandu')
            ->where('nik_anak', $nikAnak)
            ->first();

        if (!$anak) {
            $request->session()->forget('orang_tua_nik_anak');

            return redirect('/login')->withErrors([
                'login' => 'Data anak tidak ditemukan. Silakan login kembali.',
            ]);
        }

        // Ambil pemeriksaan terbaru milik anak yang sedang login.
        $pemeriksaanTerakhir = DB::table('pemeriksaan')
            ->where('nik_anak', $anak->nik_anak)
            ->orderByDesc('tgl_periksa')
            ->orderByDesc('id_periksa')
            ->first();

        // Ambil jadwal mendatang di Posyandu tempat anak terdaftar.
        $jadwalTerdekat = DB::table('jadwal_posyandu')
            ->where('id_posyandu', $anak->id_posyandu)
            ->whereDate('tgl_kegiatan', '>=', now()->toDateString())
            ->orderBy('tgl_kegiatan')
            ->orderBy('id_jadwal')
            ->first();

        // Belum menampilkan prediksi sebelum mekanismenya tersedia.
        $prediksiStunting = null;

        return view('orang_tua.dashboard', compact(
            'anak',
            'pemeriksaanTerakhir',
            'jadwalTerdekat',
            'prediksiStunting'
        ));
    }
}
