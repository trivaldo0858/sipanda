<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    /**
     * Daftar pemeriksaan
     */
    public function index(Request $request)
    {
        $query = Pemeriksaan::with(['anak', 'kader', 'bidan', 'jadwal']);

        if ($request->filled('nik_anak')) {
            $query->where('nik_anak', $request->nik_anak);
        }

        if ($request->filled('id_jadwal')) {
            $query->where('id_jadwal', $request->id_jadwal);
        }

        if ($request->filled('tgl_dari') && $request->filled('tgl_sampai')) {
            $query->whereBetween('tgl_pemeriksaan', [$request->tgl_dari, $request->tgl_sampai]);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('tgl_pemeriksaan', 'desc')->paginate(15),
        ]);
    }

    /**
     * Catat pemeriksaan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik_anak'        => 'required|exists:anak,nik_anak',
            'id_kader'        => 'required|exists:kader,id_kader',
            'nip_bidan'       => 'nullable|exists:bidan,nip',
            'id_jadwal'       => 'nullable|exists:jadwal_posyandu,id_jadwal',
            'tgl_pemeriksaan' => 'required|date',
            'berat_badan'     => 'nullable|numeric|min:0|max:200',
            'tinggi_badan'    => 'nullable|numeric|min:0|max:300',
            'lingkar_kepala'  => 'nullable|numeric|min:0|max:100',
            'keluhan'         => 'nullable|string',
        ]);

        $pemeriksaan = Pemeriksaan::create($request->only([
            'nik_anak', 'id_kader', 'nip_bidan', 'id_jadwal',
            'tgl_pemeriksaan', 'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'keluhan',
        ]));

        // Kirim notifikasi ke orang tua
        $anak = $pemeriksaan->anak()->with('orangTua.pengguna')->first();
        if ($anak->orangTua?->pengguna) {
            Notifikasi::create([
                'id_user'    => $anak->orangTua->pengguna->id_user,
                'nik_anak'   => $anak->nik_anak,
                'pesan'      => "Hasil pemeriksaan {$anak->nama_anak} pada {$pemeriksaan->tgl_pemeriksaan->format('d/m/Y')} telah dicatat.",
                'tgl_kirim'  => now(),
                'status'     => 'Belum Dibaca',
                'jenis_notif'=> 'Posyandu',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil dicatat.',
            'data'    => $pemeriksaan->load(['anak', 'kader', 'bidan']),
        ], 201);
    }

    /**
     * Detail pemeriksaan
     */
    public function show($id)
    {
        $pemeriksaan = Pemeriksaan::with(['anak.orangTua', 'kader', 'bidan', 'jadwal'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $pemeriksaan]);
    }

    /**
     * Update pemeriksaan
     */
    public function update(Request $request, $id)
    {
        $pemeriksaan = Pemeriksaan::findOrFail($id);

        $request->validate([
            'nip_bidan'      => 'nullable|exists:bidan,nip',
            'berat_badan'    => 'nullable|numeric|min:0|max:200',
            'tinggi_badan'   => 'nullable|numeric|min:0|max:300',
            'lingkar_kepala' => 'nullable|numeric|min:0|max:100',
            'keluhan'        => 'nullable|string',
        ]);

        $pemeriksaan->update($request->only([
            'nip_bidan', 'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'keluhan',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan berhasil diperbarui.',
            'data'    => $pemeriksaan->fresh(['anak', 'kader', 'bidan']),
        ]);
    }

    /**
     * Hapus pemeriksaan
     */
    public function destroy($id)
    {
        Pemeriksaan::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Data pemeriksaan dihapus.']);
    }
}