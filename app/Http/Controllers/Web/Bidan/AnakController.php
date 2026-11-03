<?php

namespace App\Http\Controllers\Web\Bidan;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;
use App\Models\Pemeriksaan;

class AnakController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = $request->user();

        abort_unless($pengguna && $pengguna->isBidan(), 403);

        $bidan = $pengguna->bidan;

        abort_unless(
            $bidan,
            403,
            'Profil Bidan belum terhubung dengan akun.'
        );

        $query = Anak::where('id_posyandu', $bidan->id_posyandu);

        if ($request->filled('q')) {
            $keyword = $request->q;

            $query->where(function ($q) use ($keyword) {
                $q->where('nama_anak', 'like', "%{$keyword}%")
                    ->orWhere('nik_anak', 'like', "%{$keyword}%");
            });
        }

        $anak = $query->orderBy('nama_anak', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('bidan.anak.index', compact('bidan', 'anak'));
    }
    public function show(Request $request, string $nik)
{
    $pengguna = $request->user();

    abort_unless($pengguna && $pengguna->isBidan(), 403);

    $bidan = $pengguna->bidan;

    abort_unless(
        $bidan,
        403,
        'Profil Bidan belum terhubung dengan akun.'
    );

    // Ambil data balita hanya dari wilayah Posyandu Bidan.
    $anak = Anak::where('id_posyandu', $bidan->id_posyandu)
        ->findOrFail($nik);

    // Ambil riwayat pemeriksaan balita tersebut.
    $riwayat = Pemeriksaan::where('nik_anak', $anak->nik_anak)
        ->where('id_posyandu', $bidan->id_posyandu)
        ->orderBy('tgl_periksa', 'desc')
        ->paginate(10);

    return view('bidan.anak.detail', compact(
        'bidan',
        'anak',
        'riwayat'
    ));
}
}