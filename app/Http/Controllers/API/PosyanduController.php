<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $query = Posyandu::withCount(['kader', 'bidan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('nama_posyandu', 'like', '%' . $request->search . '%');
        }

        return response()->json([
            'success' => true,
            'data'    => $query->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_posyandu' => 'required|string|max:100',
            'alamat'        => 'nullable|string',
            'wilayah'       => 'nullable|string|max:100',
            'no_telp'       => 'nullable|string|max:20',
            'status'        => 'in:Aktif,Tidak Aktif',
        ]);

        $posyandu = Posyandu::create($request->only([
            'nama_posyandu', 'alamat', 'wilayah', 'no_telp', 'status',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Posyandu berhasil ditambahkan.',
            'data'    => $posyandu,
        ], 201);
    }

    public function show($id)
    {
        $posyandu = Posyandu::with(['kader.pengguna', 'bidan.pengguna'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $posyandu,
        ]);
    }

    public function update(Request $request, $id)
    {
        $posyandu = Posyandu::findOrFail($id);

        $request->validate([
            'nama_posyandu' => 'sometimes|string|max:100',
            'alamat'        => 'nullable|string',
            'wilayah'       => 'nullable|string|max:100',
            'no_telp'       => 'nullable|string|max:20',
            'status'        => 'in:Aktif,Tidak Aktif',
        ]);

        $posyandu->update($request->only([
            'nama_posyandu', 'alamat', 'wilayah', 'no_telp', 'status',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Posyandu berhasil diperbarui.',
            'data'    => $posyandu,
        ]);
    }

    public function destroy($id)
    {
        Posyandu::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Posyandu berhasil dihapus.',
        ]);
    }
}