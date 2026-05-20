<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PosyanduController extends Controller
{
    /**
     * List posyandu (untuk dropdown login Kader)
     * Public endpoint
     */
    public function list()
    {
        $posyandu = Posyandu::select(
            'id_posyandu',
            'nama_posyandu',
            'desa_kelurahan',
            'kecamatan',
            'kabupaten_kota'
        )->orderBy('nama_posyandu')->get();

        return response()->json([
            'success' => true,
            'data'    => $posyandu,
        ]);
    }

    /**
     * KF-006: Detail profil posyandu aktif (untuk Kader)
     */
    public function profil(Request $request)
    {
        $idPosyandu = $request->user()->getPosyanduAktifId();

        $posyandu = Posyandu::withCount(['anak', 'bidan', 'kader'])
            ->findOrFail($idPosyandu);

        return response()->json([
            'success' => true,
            'data'    => [
                'id_posyandu'    => $posyandu->id_posyandu,
                'nama_posyandu'  => $posyandu->nama_posyandu,
                'kecamatan'      => $posyandu->kecamatan,
                'desa_kelurahan' => $posyandu->desa_kelurahan,
                'alamat'         => $posyandu->alamat,
                'kabupaten_kota' => $posyandu->kabupaten_kota,
                'total_anak'     => $posyandu->anak_count,
                'total_bidan'    => $posyandu->bidan_count,
                'total_kader'    => $posyandu->kader_count,
            ],
        ]);
    }

    /**
     * KF-006: Update profil posyandu (Kader)
     */
    public function updateProfil(Request $request)
    {
        $request->validate([
            'alamat'         => 'nullable|string',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
        ]);

        $idPosyandu = $request->user()->getPosyanduAktifId();
        $posyandu   = Posyandu::findOrFail($idPosyandu);

        $posyandu->update($request->only([
            'alamat', 'desa_kelurahan', 'kecamatan',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profil posyandu berhasil diperbarui.',
            'data'    => $posyandu,
        ]);
    }

    /**
     * Detail posyandu by ID (public)
     */
    public function show($id)
    {
        $posyandu = Posyandu::select(
            'id_posyandu',
            'nama_posyandu',
            'desa_kelurahan',
            'kecamatan',
            'kabupaten_kota',
            'alamat'
        )->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $posyandu,
        ]);
    }
}