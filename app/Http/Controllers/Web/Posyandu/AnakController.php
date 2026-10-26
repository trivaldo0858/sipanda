<?php

namespace App\Http\Controllers\Web\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\OrangTua;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AnakController extends Controller
{
    /**
     * Menampilkan daftar anak milik Posyandu yang sedang login.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Akun Posyandu harus terhubung ke suatu Posyandu.
        if (!$user->id_posyandu) {
            abort(403, 'Akun belum terhubung dengan Posyandu.');
        }

        $ringkasanAnak = Anak::where('id_posyandu', $user->id_posyandu)
            ->selectRaw("COUNT(*) AS total, SUM(CASE WHEN jenis_kelamin = 'L' THEN 1 ELSE 0 END) AS laki_laki, SUM(CASE WHEN jenis_kelamin = 'P' THEN 1 ELSE 0 END) AS perempuan")
            ->first();

        $totalAnak = (int) $ringkasanAnak->total;
        $statistik = [
            'total' => $totalAnak,
            'laki_laki' => (int) $ringkasanAnak->laki_laki,
            'perempuan' => (int) $ringkasanAnak->perempuan,
            'persentase_laki_laki' => $totalAnak > 0
                ? round(((int) $ringkasanAnak->laki_laki / $totalAnak) * 100, 1)
                : 0,
            'persentase_perempuan' => $totalAnak > 0
                ? round(((int) $ringkasanAnak->perempuan / $totalAnak) * 100, 1)
                : 0,
        ];

        $query = Anak::with(['orangTua', 'posyandu'])
            ->where('id_posyandu', $user->id_posyandu);

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('nama_anak', 'like', '%' . $search . '%')
                    ->orWhere('nik_anak', 'like', '%' . $search . '%')
                    ->orWhereHas('orangTua', function ($query) use ($search) {
                        $query->where('nama_ibu', 'like', '%' . $search . '%');
                    });
            });
        }

        if (in_array($request->input('jenis_kelamin'), ['L', 'P'], true)) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }

        $anak = $query->orderBy('nama_anak')
            ->paginate(10)
            ->withQueryString();

        return view('posyandu.anak.index', compact('anak', 'statistik'));
    }

    /**
     * Menampilkan formulir pendaftaran anak.
     */
    public function create()
    {
        return view('posyandu.anak.create');
    }

    /**
     * Menyimpan data anak baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->id_posyandu) {
            abort(403, 'Akun belum terhubung dengan Posyandu.');
        }

        $validated = $request->validate([
            'nik_anak' => [
                'required',
                'digits:16',
                Rule::unique('anak', 'nik_anak'),
            ],
            'nama_anak' => [
                'required',
                'string',
                'max:100',
            ],
            'tgl_lahir' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],
            'nama_ayah' => [
                'nullable',
                'string',
                'max:100',
            ],
            'nama_ibu' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'nik_anak.required' => 'NIK anak wajib diisi.',
            'nik_anak.digits' => 'NIK anak harus terdiri dari 16 digit.',
            'nik_anak.unique' => 'NIK anak sudah terdaftar.',
            'nama_anak.required' => 'Nama anak wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'nama_ibu.required' => 'Nama ibu wajib diisi.',
        ]);

        DB::transaction(function () use ($validated, $user) {
            $akunOrangTua = Pengguna::create([
                'username' => null,
                'password' => null,
                'role' => 'OrangTua',
            ]);

            $orangTua = OrangTua::create([
                'nik_orang_tua' => 'NIK-' . Str::uuid(),
                'id_user' => $akunOrangTua->id_user,
                'nama_ibu' => $validated['nama_ibu'],
            ]);

            Anak::create([
                'nik_anak' => $validated['nik_anak'],
                'nik_orang_tua' => $orangTua->nik_orang_tua,
                'id_posyandu' => $user->id_posyandu,
                'nama_anak' => $validated['nama_anak'],
                'tgl_lahir' => $validated['tgl_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'nama_ayah' => $validated['nama_ayah'] ?? null,
            ]);
        });

        return redirect()
            ->route('posyandu.anak.index')
            ->with('success', 'Data anak berhasil didaftarkan.');
    }
}
