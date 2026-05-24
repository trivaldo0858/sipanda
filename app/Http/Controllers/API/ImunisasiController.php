<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Imunisasi;
use App\Models\JenisVaksin;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class ImunisasiController extends Controller
{
    /**
     * List imunisasi — filter by posyandu aktif
     */
    public function index(Request $request)
    {
        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $query = Imunisasi::with(['anak', 'bidan', 'jenisVaksin']);

        if (! $user->isOrangTua() && $idPosyandu) {
            $query->where('id_posyandu', $idPosyandu);
        }

        if ($user->isOrangTua()) {
            $nikList = $user->orangTua->anak->pluck('nik_anak');
            $query->whereIn('nik_anak', $nikList);
        }

        if ($request->filled('nik_anak')) {
            $query->where('nik_anak', $request->nik_anak);
        }

        if ($request->filled('id_vaksin')) {
            $query->where('id_vaksin', $request->id_vaksin);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('tgl_pemberian', 'desc')->paginate(15),
        ]);
    }

    /**
     * KF-006: Catat imunisasi baru (Bidan only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik_anak'      => 'required|exists:anak,nik_anak',
            'id_vaksin'     => 'required|exists:jenis_vaksin,id_vaksin',
            'tgl_pemberian' => 'required|date',
            'catatan'       => 'nullable|string',
        ]);

        $user       = $request->user();
        $bidan      = $user->bidan;
        $idPosyandu = $user->getPosyanduAktifId();

        $imunisasi = Imunisasi::create([
            'nik_anak'      => $request->nik_anak,
            'nip_bidan'     => $bidan?->nip,
            'id_vaksin'     => $request->id_vaksin,
            'tgl_pemberian' => $request->tgl_pemberian,
        ]);

        // Notifikasi ke OrangTua
        $anak   = $imunisasi->anak()->with('orangTua.pengguna')->first();
        $vaksin = $imunisasi->jenisVaksin;

        if ($anak->orangTua?->pengguna) {
            Notifikasi::create([
                'id_user'     => $anak->orangTua->pengguna->id_user,
                'nik_anak'    => $anak->nik_anak,
                'pesan'       => "Imunisasi {$vaksin->nama_vaksin} untuk {$anak->nama_anak} "
                               . "telah diberikan pada "
                               . $imunisasi->tgl_pemberian->format('d/m/Y') . ".",
                'tgl_kirim'   => now(),
                'status'      => 'Belum Dibaca',
                'jenis_notif' => 'Imunisasi',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imunisasi berhasil dicatat.',
            'data'    => $imunisasi->load(['anak', 'bidan', 'jenisVaksin']),
        ], 201);
    }

    /**
     * Detail imunisasi
     */
    public function show($id)
    {
        $imunisasi = Imunisasi::with([
            'anak.orangTua',
            'bidan',
            'jenisVaksin',
            'posyandu',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $imunisasi,
        ]);
    }

    /**
     * Update imunisasi
     */
    public function update(Request $request, $id)
    {
        $imunisasi = Imunisasi::findOrFail($id);

        $request->validate([
            'id_vaksin'     => 'sometimes|exists:jenis_vaksin,id_vaksin',
            'tgl_pemberian' => 'sometimes|date',
            'catatan'       => 'nullable|string',
        ]);

        $imunisasi->update($request->only([
            'id_vaksin', 'tgl_pemberian', 'catatan',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Imunisasi berhasil diperbarui.',
            'data'    => $imunisasi->fresh(['anak', 'bidan', 'jenisVaksin']),
        ]);
    }

    /**
     * Hapus imunisasi
     */
    public function destroy($id)
    {
        Imunisasi::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data imunisasi berhasil dihapus.',
        ]);
    }

    /**
     * List semua jenis vaksin (untuk dropdown)
     */
    public function jenisVaksin()
    {
        return response()->json([
            'success' => true,
            'data'    => JenisVaksin::all(),
        ]);
    }

    /**
     * Riwayat imunisasi anak (timeline)
     */
    public function riwayat($nik)
    {
        $imunisasi = Imunisasi::where('nik_anak', $nik)
            ->with(['jenisVaksin', 'bidan'])
            ->orderBy('tgl_pemberian', 'asc')
            ->get()
            ->map(fn ($i) => [
                'id_imunisasi'  => $i->id_imunisasi,
                'nama_vaksin'   => $i->jenisVaksin->nama_vaksin,
                'tgl_pemberian' => $i->tgl_pemberian->format('Y-m-d'),
                'nama_bidan'    => $i->bidan?->nama_bidan,
                'catatan'       => $i->catatan,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $imunisasi,
        ]);
    }
}