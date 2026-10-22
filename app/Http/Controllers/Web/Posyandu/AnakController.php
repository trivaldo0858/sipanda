<?php

namespace App\Http\Controllers\Web\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AnakController extends Controller
{
    /**
     * Menampilkan daftar anak milik Posyandu yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Akun Posyandu harus terhubung ke suatu Posyandu.
        if (!$user->id_posyandu) {
            abort(403, 'Akun belum terhubung dengan Posyandu.');
        }

        $anak = Anak::where('id_posyandu', $user->id_posyandu)
            ->orderBy('nama_anak')
            ->paginate(10);

        return view('posyandu.anak.index', compact('anak'));
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

        // ID Posyandu berasal dari akun login, bukan formulir.
        $validated['id_posyandu'] = $user->id_posyandu;

        Anak::create($validated);

        return redirect()
            ->route('posyandu.anak.index')
            ->with('success', 'Data anak berhasil didaftarkan.');
    }
}
