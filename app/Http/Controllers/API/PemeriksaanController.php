<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    /**
     * List pemeriksaan — filter by posyandu aktif
     */
    public function index(Request $request)
    {
        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $query = Pemeriksaan::with(['anak', 'bidan', 'jadwal']);

        // Filter by posyandu
        if ($idPosyandu && ! $user->isOrangTua()) {
            $query->where('id_posyandu', $idPosyandu);
        }

        // OrangTua hanya lihat pemeriksaan anaknya
        if ($user->isOrangTua()) {
            $nikList = $user->orangTua->anak->pluck('nik_anak');
            $query->whereIn('nik_anak', $nikList);
        }

        if ($request->filled('nik_anak')) {
            $query->where('nik_anak', $request->nik_anak);
        }

        if ($request->filled('status_validasi')) {
            $query->where('status_validasi', $request->status_validasi);
        }

        if ($request->filled('tgl_dari') && $request->filled('tgl_sampai')) {
            $query->whereBetween('tgl_periksa', [
                $request->tgl_dari, $request->tgl_sampai,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('tgl_periksa', 'desc')->paginate(15),
        ]);
    }

    /**
     * KF-005: Catat pemeriksaan fisik baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik_anak'      => 'required|exists:anak,nik_anak',
            'tgl_periksa'   => 'required|date',
            'berat_badan'   => 'nullable|numeric|min:0|max:200',
            'tinggi_badan'  => 'nullable|numeric|min:0|max:300',
            'lingkar_kepala'=> 'nullable|numeric|min:0|max:100',
            'keluhan'       => 'nullable|string',
            'id_jadwal'     => 'nullable|exists:jadwal_posyandu,id_jadwal',
        ]);

        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $pemeriksaan = Pemeriksaan::create([
            'nik_anak'       => $request->nik_anak,
            'id_posyandu'    => $idPosyandu,
            'nip_bidan'      => null, // Bidan yang validasi nanti
            'id_jadwal'      => $request->id_jadwal,
            'tgl_periksa'    => $request->tgl_periksa,
            'berat_badan'    => $request->berat_badan,
            'tinggi_badan'   => $request->tinggi_badan,
            'lingkar_kepala' => $request->lingkar_kepala,
            'keluhan'        => $request->keluhan,
            'status_validasi'=> 'Menunggu',
        ]);

        // Notifikasi ke OrangTua
        $anak = $pemeriksaan->anak()->with('orangTua.pengguna')->first();
        if ($anak->orangTua?->pengguna) {
            Notifikasi::create([
                'id_user'     => $anak->orangTua->pengguna->id_user,
                'nik_anak'    => $anak->nik_anak,
                'pesan'       => "Pemeriksaan {$anak->nama_anak} pada "
                               . $pemeriksaan->tgl_periksa->format('d/m/Y')
                               . " telah dicatat dan menunggu validasi Bidan.",
                'tgl_kirim'   => now(),
                'status'      => 'Belum Dibaca',
                'jenis_notif' => 'Posyandu',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil dicatat.',
            'data'    => $pemeriksaan->load(['anak', 'bidan']),
        ], 201);
    }

    /**
     * Detail pemeriksaan
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
            'data'    => $pemeriksaan,
        ]);
    }

    /**
     * Update pemeriksaan (hanya jika masih Menunggu)
     */
    public function update(Request $request, $id)
    {
        $pemeriksaan = Pemeriksaan::findOrFail($id);

        if ($pemeriksaan->status_validasi !== 'Menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Pemeriksaan yang sudah divalidasi tidak dapat diubah.',
            ], 422);
        }

        $request->validate([
            'berat_badan'   => 'nullable|numeric|min:0|max:200',
            'tinggi_badan'  => 'nullable|numeric|min:0|max:300',
            'lingkar_kepala'=> 'nullable|numeric|min:0|max:100',
            'keluhan'       => 'nullable|string',
        ]);

        $pemeriksaan->update($request->only([
            'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'keluhan',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil diperbarui.',
            'data'    => $pemeriksaan->fresh(['anak', 'bidan']),
        ]);
    }

    /**
     * Hapus pemeriksaan (hanya jika masih Menunggu)
     */
    public function destroy($id)
    {
        $pemeriksaan = Pemeriksaan::findOrFail($id);

        if ($pemeriksaan->status_validasi !== 'Menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Pemeriksaan yang sudah divalidasi tidak dapat dihapus.',
            ], 422);
        }

        $pemeriksaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil dihapus.',
        ]);
    }

    /**
     * Validasi pemeriksaan oleh Bidan
     */
    public function validasi(Request $request, $id)
    {
        $request->validate([
            'status_validasi'  => 'required|in:Disetujui,Ditolak',
            'catatan_validasi' => 'nullable|string',
        ]);

        $pemeriksaan = Pemeriksaan::with('anak.orangTua.pengguna')->findOrFail($id);
        $bidan       = $request->user()->bidan;

        $pemeriksaan->update([
            'status_validasi'  => $request->status_validasi,
            'catatan_validasi' => $request->catatan_validasi,
            'nip_bidan'        => $bidan?->nip,
        ]);

        // Notifikasi ke OrangTua
        if ($pemeriksaan->anak->orangTua?->pengguna) {
            $status = $request->status_validasi === 'Disetujui' ? 'disetujui' : 'ditolak';
            Notifikasi::create([
                'id_user'     => $pemeriksaan->anak->orangTua->pengguna->id_user,
                'nik_anak'    => $pemeriksaan->nik_anak,
                'pesan'       => "Hasil pemeriksaan {$pemeriksaan->anak->nama_anak} telah {$status} oleh Bidan."
                               . ($request->catatan_validasi ? " Catatan: {$request->catatan_validasi}" : ''),
                'tgl_kirim'   => now(),
                'status'      => 'Belum Dibaca',
                'jenis_notif' => 'Posyandu',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Pemeriksaan berhasil {$request->status_validasi}.",
            'data'    => $pemeriksaan->fresh(['anak', 'bidan']),
        ]);
    }
}