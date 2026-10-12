<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Bidan;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = Pengguna::with(['bidan', 'posyandu'])
            ->where('role', 'Bidan')
            ->when($request->filled('id_posyandu'), function ($query) use ($request) {
                $query->where('id_posyandu', $request->id_posyandu);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', $search)
                        ->orWhereHas('bidan', function ($bidanQuery) use ($search) {
                            $bidanQuery->where('nama_bidan', 'like', $search);
                        });
                });
            })
            ->orderBy('username')
            ->paginate(15)
            ->withQueryString();

        $posyanduList = Posyandu::orderBy('nama_posyandu')->get();

        return view('superadmin.pengguna.index', compact(
            'pengguna',
            'posyanduList'
        ));
    }

    public function create()
    {
        $posyanduList = Posyandu::orderBy('nama_posyandu')->get();

        return view('superadmin.pengguna.form', [
            'posyanduList' => $posyanduList,
            'pengguna_single' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:pengguna,username',
            ],
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:bidan,nip',
            'id_posyandu' => 'required|exists:posyandu,id_posyandu',
            'no_telp' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($data) {
            $user = Pengguna::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'role' => 'Bidan',
                'id_posyandu' => $data['id_posyandu'],
                'id_posyandu_aktif' => $data['id_posyandu'],
            ]);

            Bidan::create([
                'nip' => $data['nip'] ?: ('NIP-' . uniqid()),
                'id_user' => $user->id_user,
                'id_posyandu' => $data['id_posyandu'],
                'nama_bidan' => $data['nama'],
                'no_telp' => $data['no_telp'] ?? null,
            ]);
        });

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with('success', 'Akun Bidan berhasil dibuat.');
    }

    public function edit($id)
    {
        $pengguna_single = Pengguna::with(['bidan', 'posyandu'])
            ->where('role', 'Bidan')
            ->findOrFail($id);

        $posyanduList = Posyandu::orderBy('nama_posyandu')->get();

        return view('superadmin.pengguna.form', compact(
            'pengguna_single',
            'posyanduList'
        ));
    }

    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::with('bidan')
            ->where('role', 'Bidan')
            ->findOrFail($id);

        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pengguna', 'username')
                    ->ignore($pengguna->id_user, 'id_user'),
            ],
            'password' => 'nullable|string|min:6',
            'nama' => 'required|string|max:255',
            'id_posyandu' => 'required|exists:posyandu,id_posyandu',
            'no_telp' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($data, $pengguna) {
            $userData = [
                'username' => $data['username'],
                'id_posyandu' => $data['id_posyandu'],
                'id_posyandu_aktif' => $data['id_posyandu'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $pengguna->update($userData);

            if ($pengguna->bidan) {
                $pengguna->bidan->update([
                    'nama_bidan' => $data['nama'],
                    'id_posyandu' => $data['id_posyandu'],
                    'no_telp' => $data['no_telp'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with('success', 'Akun Bidan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengguna = Pengguna::where('role', 'Bidan')->findOrFail($id);

        // Penghapusan perlu memperhatikan foreign key dan riwayat pemeriksaan.
        // Jangan menghapus riwayat layanan secara tidak sengaja.
        return back()->with(
            'error',
            'Penghapusan akun belum dijalankan. Periksa relasi database terlebih dahulu.'
        );
    }
}