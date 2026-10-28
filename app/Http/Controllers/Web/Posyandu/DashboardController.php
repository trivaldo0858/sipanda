<?php

namespace App\Http\Controllers\Web\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Pemeriksaan;
use App\Models\JadwalPosyandu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil ID unit Posyandu dari akun yang login.
        $posyandu = $user?->posyandu;
        $idPosyandu = $posyandu?->id_posyandu
            ?? $posyandu?->id
            ?? $user?->id_posyandu
            ?? $user?->posyandu_id;

        $hitungData = function ($modelClass) use ($idPosyandu) {
            $model = new $modelClass();
            $query = $modelClass::query();

            if (
                $idPosyandu &&
                Schema::hasColumn($model->getTable(), 'id_posyandu')
            ) {
                $query->where('id_posyandu', $idPosyandu);
            }

            return $query->count();
        };

        $totalAnak = $hitungData(Anak::class);
        $totalPemeriksaan = $hitungData(Pemeriksaan::class);
        $totalJadwal = $hitungData(JadwalPosyandu::class);

        return view('posyandu.dashboard.index', compact(
            'user',
            'posyandu',
            'totalAnak',
            'totalPemeriksaan',
            'totalJadwal'
        ));
    }
}
