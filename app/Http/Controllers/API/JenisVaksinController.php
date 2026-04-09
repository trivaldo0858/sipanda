<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisVaksin;
use Illuminate\Http\Request;

class JenisVaksinController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => JenisVaksin::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_vaksin' => 'required|string|unique:jenis_vaksin,nama_vaksin',
            'deskripsi'   => 'nullable|string',
        ]);

        $vaksin = JenisVaksin::create($request->only(['nama_vaksin', 'deskripsi']));

        return response()->json([
            'success' => true,
            'message' => 'Jenis vaksin berhasil ditambahkan.',
            'data'    => $vaksin,
        ], 201);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data'    => JenisVaksin::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $vaksin = JenisVaksin::findOrFail($id);

        $request->validate([
            'nama_vaksin' => 'sometimes|string|unique:jenis_vaksin,nama_vaksin,' . $id . ',id_vaksin',
            'deskripsi'   => 'nullable|string',
        ]);

        $vaksin->update($request->only(['nama_vaksin', 'deskripsi']));

        return response()->json([
            'success' => true,
            'message' => 'Jenis vaksin berhasil diperbarui.',
            'data'    => $vaksin,
        ]);
    }

    public function destroy($id)
    {
        JenisVaksin::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Jenis vaksin berhasil dihapus.']);
    }
}