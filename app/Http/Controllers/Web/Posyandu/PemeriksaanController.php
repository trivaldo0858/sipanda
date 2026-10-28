<?php

namespace App\Http\Controllers\Web\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PemeriksaanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->id_posyandu) {
            abort(403, 'Akun belum terhubung dengan Posyandu.');
        }

        $idPosyandu = $user->id_posyandu;
        $anak = Anak::with('orangTua')
            ->where('id_posyandu', $idPosyandu)
            ->orderBy('nama_anak')
            ->get();
        $selectedAnak = null;
        if ($request->filled('nik_anak')) {
            $selectedAnak = Anak::with('orangTua')
                ->where('id_posyandu', $idPosyandu)
                ->where('nik_anak', $request->input('nik_anak'))
                ->first();
        }

        $pemeriksaanHariIni = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->whereDate('tgl_periksa', today())
            ->distinct()
            ->count('nik_anak');
        $riwayatHariIni = Pemeriksaan::with('anak')
            ->where('id_posyandu', $idPosyandu)
            ->whereDate('tgl_periksa', today())
            ->orderByDesc('id_periksa')
            ->limit(8)
            ->get();

        $riwayatAnak = $selectedAnak
            ? $selectedAnak->pemeriksaan()
                ->where('id_posyandu', $idPosyandu)
                ->limit(12)
                ->get()
                ->reverse()
                ->values()
            : collect();

        $chartRecords = $riwayatAnak
            ->filter(fn($record) => $record->berat_badan !== null)
            ->values();
        $chartPoints = '';

        if ($chartRecords->count() > 1) {
            $minimumWeight = (float) $chartRecords->min('berat_badan');
            $weightRange = max(1, (float) $chartRecords->max('berat_badan') - $minimumWeight);
            $lastIndex = $chartRecords->count() - 1;

            $chartPoints = $chartRecords->map(function ($record, $index) use ($minimumWeight, $weightRange, $lastIndex) {
                $x = 8 + (($index / $lastIndex) * 84);
                $y = 88 - (((float) $record->berat_badan - $minimumWeight) / $weightRange * 68);

                return number_format($x, 1, '.', '') . ',' . number_format($y, 1, '.', '');
            })->implode(' ');
        }

        return view('posyandu.pemeriksaan.index', compact(
            'anak',
            'selectedAnak',
            'pemeriksaanHariIni',
            'riwayatHariIni',
            'riwayatAnak',
            'chartRecords',
            'chartPoints'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->id_posyandu) {
            abort(403, 'Akun belum terhubung dengan Posyandu.');
        }

        $validated = $request->validate([
            'nik_anak' => [
                'required',
                'string',
                Rule::exists('anak', 'nik_anak')->where('id_posyandu', $user->id_posyandu),
            ],
            'tgl_periksa' => ['required', 'date', 'before_or_equal:today'],
            'berat_badan' => ['required', 'numeric', 'min:0', 'max:200'],
            'tinggi_badan' => ['required', 'numeric', 'min:0', 'max:300'],
            'lingkar_kepala' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'keluhan' => ['nullable', 'string', 'max:2000'],
        ]);

        Pemeriksaan::create([
            ...$validated,
            'id_posyandu' => $user->id_posyandu,
            'nip_bidan' => null,
            'status_validasi' => 'Menunggu',
        ]);

        return redirect()
            ->route('posyandu.pemeriksaan.index', ['nik_anak' => $validated['nik_anak']])
            ->with('success', 'Data pemeriksaan berhasil disimpan dan menunggu validasi bidan.');
    }
}