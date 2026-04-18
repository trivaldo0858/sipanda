<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Imunisasi;
use App\Models\Notifikasi;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class ValidasiController extends Controller
{
    /**
     * List data yang menunggu validasi
     */
    public function index(Request $request)
    {
        $nip = $request->user()->bidan->nip;

        $pemeriksaan = Pemeriksaan::menungguValidasi()
            ->with(['anak', 'kader'])
            ->whereHas('kader', function ($q) use ($request) {
                // Filter per posyandu bidan
                $q->where('id_posyandu', $request->user()->id_posyandu);
            })
            ->orderBy('tgl_pemeriksaan', 'desc')
            ->get();

        $imunisasi = Imunisasi::menungguValidasi()
            ->with(['anak', 'jenisVaksin'])
            ->orderBy('tgl_pemberian', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'pemeriksaan' => $pemeriksaan,
                'imunisasi'   => $imunisasi,
                'total'       => $pemeriksaan->count() + $imunisasi->count(),
            ],
        ]);
    }

    /**
     * Validasi pemeriksaan — setujui atau tolak
     */
    public function validasiPemeriksaan(Request $request, $id)
    {
        $request->validate([
            'status_validasi'   => 'required|in:Disetujui,Ditolak',
            'catatan_validasi'  => 'nullable|string',
        ]);

        $pemeriksaan = Pemeriksaan::with('anak.orangTua.pengguna')->findOrFail($id);
        $nip         = $request->user()->bidan->nip;

        $pemeriksaan->update([
            'status_validasi'  => $request->status_validasi,
            'catatan_validasi' => $request->catatan_validasi,
            'nip_validator'    => $nip,
        ]);

        // Notifikasi ke orang tua
        if ($pemeriksaan->anak->orangTua?->pengguna) {
            $status = $request->status_validasi === 'Disetujui' ? 'disetujui' : 'ditolak';
            Notifikasi::create([
                'id_user'     => $pemeriksaan->anak->orangTua->pengguna->id_user,
                'nik_anak'    => $pemeriksaan->nik_anak,
                'pesan'       => "Hasil pemeriksaan {$pemeriksaan->anak->nama_anak} telah {$status} oleh Bidan.",
                'tgl_kirim'   => now(),
                'status'      => 'Belum Dibaca',
                'jenis_notif' => 'Posyandu',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Pemeriksaan berhasil {$request->status_validasi}.",
            'data'    => $pemeriksaan->fresh(['anak', 'kader', 'validator']),
        ]);
    }

    /**
     * Validasi imunisasi — setujui atau tolak
     */
    public function validasiImunisasi(Request $request, $id)
    {
        $request->validate([
            'status_validasi'  => 'required|in:Disetujui,Ditolak',
            'catatan_validasi' => 'nullable|string',
        ]);

        $imunisasi = Imunisasi::with('anak.orangTua.pengguna', 'jenisVaksin')->findOrFail($id);

        $imunisasi->update([
            'status_validasi'  => $request->status_validasi,
            'catatan_validasi' => $request->catatan_validasi,
        ]);

        // Notifikasi ke orang tua
        if ($imunisasi->anak->orangTua?->pengguna) {
            $status = $request->status_validasi === 'Disetujui' ? 'disetujui' : 'ditolak';
            Notifikasi::create([
                'id_user'     => $imunisasi->anak->orangTua->pengguna->id_user,
                'nik_anak'    => $imunisasi->nik_anak,
                'pesan'       => "Imunisasi {$imunisasi->jenisVaksin->nama_vaksin} untuk {$imunisasi->anak->nama_anak} telah {$status}.",
                'tgl_kirim'   => now(),
                'status'      => 'Belum Dibaca',
                'jenis_notif' => 'Imunisasi',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Imunisasi berhasil {$request->status_validasi}.",
            'data'    => $imunisasi->fresh(['anak', 'jenisVaksin']),
        ]);
    }
}