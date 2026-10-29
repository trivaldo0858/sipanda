<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    /**
     * Menampilkan daftar pemeriksaan.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $query = Pemeriksaan::with(['anak', 'bidan', 'jadwal']);

        // Batasi data berdasarkan Posyandu aktif.
        if ($idPosyandu && !$user->isOrangTua()) {
            $query->where('id_posyandu', $idPosyandu);
        }

        // Orang Tua hanya dapat melihat pemeriksaan anaknya.
        if ($user->isOrangTua()) {
            $nikList = $user->orangTua->anak->pluck('nik_anak');
            $query->whereIn('nik_anak', $nikList);
        }

        if ($request->filled('nik_anak')) {
            $query->where('nik_anak', $request->nik_anak);
        }

        if ($request->filled('tgl_dari') && $request->filled('tgl_sampai')) {
            $query->whereBetween('tgl_periksa', [
                $request->tgl_dari,
                $request->tgl_sampai,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('tgl_periksa', 'desc')->paginate(15),
        ]);
    }

    /**
     * Mencatat pemeriksaan fisik baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik_anak' => 'required|exists:anak,nik_anak',
            'tgl_periksa' => 'required|date',
            'berat_badan' => 'nullable|numeric|min:0|max:200',
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'lingkar_kepala' => 'nullable|numeric|min:0|max:100',
            'keluhan' => 'nullable|string',
        ]);

        $user = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $pemeriksaan = Pemeriksaan::create([
            'nik_anak' => $request->nik_anak,
            'id_posyandu' => $idPosyandu,
            'nip_bidan' => null,
            'tgl_periksa' => $request->tgl_periksa,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'lingkar_kepala' => $request->lingkar_kepala,
            'keluhan' => $request->keluhan,
        ]);

        // Notifikasi kepada Orang Tua jika akun terkait tersedia.
        $anak = $pemeriksaan->anak()
            ->with('orangTua.pengguna')
            ->first();

        if ($anak?->orangTua?->pengguna) {
            Notifikasi::create([
                'id_user' => $anak->orangTua->pengguna->id_user,
                'nik_anak' => $anak->nik_anak,
                'pesan' => "Pemeriksaan {$anak->nama_anak} pada "
                    . $pemeriksaan->tgl_periksa->format('d/m/Y')
                    . ' telah dicatat.',
                'tgl_kirim' => now(),
                'status' => 'Belum Dibaca',
                'jenis_notif' => 'Posyandu',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil dicatat.',
            'data' => $pemeriksaan->load(['anak', 'bidan']),
        ], 201);
    }

    /**
     * Menampilkan detail pemeriksaan.
     */
    public function show($id)
    {
        $pemeriksaan = Pemeriksaan::with([
            'anak.orangTua',
            'bidan',
            'jadwal',
            'posyandu',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pemeriksaan,
        ]);
    }

    /**
     * Memperbarui data pemeriksaan.
     */
    public function update(Request $request, $id)
    {
        $pemeriksaan = Pemeriksaan::findOrFail($id);

        $validated = $request->validate([
            'berat_badan' => 'nullable|numeric|min:0|max:200',
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'lingkar_kepala' => 'nullable|numeric|min:0|max:100',
            'keluhan' => 'nullable|string',
        ]);

        $pemeriksaan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil diperbarui.',
            'data' => $pemeriksaan->fresh(['anak', 'bidan']),
        ]);
    }

    /**
     * Menghapus data pemeriksaan.
     */
    public function destroy($id)
    {
        $pemeriksaan = Pemeriksaan::findOrFail($id);
        $pemeriksaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil dihapus.',
        ]);
    }
}
