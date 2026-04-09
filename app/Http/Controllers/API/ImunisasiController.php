<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Imunisasi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class ImunisasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Imunisasi::with(['anak', 'bidan', 'jenisVaksin']);

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

    public function store(Request $request)
    {
        $request->validate([
            'nik_anak'      => 'required|exists:anak,nik_anak',
            'nip_bidan'     => 'nullable|exists:bidan,nip',
            'id_vaksin'     => 'required|exists:jenis_vaksin,id_vaksin',
            'tgl_pemberian' => 'required|date',
        ]);

        $imunisasi = Imunisasi::create($request->only([
            'nik_anak', 'nip_bidan', 'id_vaksin', 'tgl_pemberian',
        ]));

        // Notifikasi ke orang tua
        $anak = $imunisasi->anak()->with(['orangTua.pengguna', ])->first();
        $vaksin = $imunisasi->jenisVaksin;

        if ($anak->orangTua?->pengguna) {
            Notifikasi::create([
                'id_user'     => $anak->orangTua->pengguna->id_user,
                'nik_anak'    => $anak->nik_anak,
                'pesan'       => "Imunisasi {$vaksin->nama_vaksin} untuk {$anak->nama_anak} telah diberikan pada {$imunisasi->tgl_pemberian->format('d/m/Y')}.",
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

    public function show($id)
    {
        $imunisasi = Imunisasi::with(['anak.orangTua', 'bidan', 'jenisVaksin'])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $imunisasi]);
    }

    public function update(Request $request, $id)
    {
        $imunisasi = Imunisasi::findOrFail($id);

        $request->validate([
            'nip_bidan'     => 'nullable|exists:bidan,nip',
            'id_vaksin'     => 'sometimes|exists:jenis_vaksin,id_vaksin',
            'tgl_pemberian' => 'sometimes|date',
        ]);

        $imunisasi->update($request->only(['nip_bidan', 'id_vaksin', 'tgl_pemberian']));

        return response()->json([
            'success' => true,
            'message' => 'Imunisasi berhasil diperbarui.',
            'data'    => $imunisasi->fresh(['anak', 'bidan', 'jenisVaksin']),
        ]);
    }

    public function destroy($id)
    {
        Imunisasi::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Data imunisasi dihapus.']);
    }
}