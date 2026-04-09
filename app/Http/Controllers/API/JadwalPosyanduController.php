<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JadwalPosyandu;
use App\Models\Notifikasi;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class JadwalPosyanduController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPosyandu::with('kader');

        if ($request->filled('id_kader')) {
            $query->where('id_kader', $request->id_kader);
        }

        // Filter upcoming / past
        if ($request->filled('filter')) {
            match ($request->filter) {
                'upcoming' => $query->where('tgl_kegiatan', '>=', today()),
                'past'     => $query->where('tgl_kegiatan', '<', today()),
                default    => null,
            };
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('tgl_kegiatan', 'asc')->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kader'     => 'required|exists:kader,id_kader',
            'tgl_kegiatan' => 'required|date|after_or_equal:today',
            'lokasi'       => 'required|string|max:255',
            'agenda'       => 'nullable|string',
        ]);

        $jadwal = JadwalPosyandu::create($request->only([
            'id_kader', 'tgl_kegiatan', 'lokasi', 'agenda',
        ]));

        // Broadcast notifikasi ke semua OrangTua
        $orangTuaUsers = Pengguna::where('role', 'OrangTua')->get();
        $notifikasi = $orangTuaUsers->map(fn ($u) => [
            'id_user'     => $u->id_user,
            'nik_anak'    => null,
            'pesan'       => "Jadwal posyandu baru: {$jadwal->tgl_kegiatan->format('d/m/Y')} di {$jadwal->lokasi}.",
            'tgl_kirim'   => now(),
            'status'      => 'Belum Dibaca',
            'jenis_notif' => 'Posyandu',
            'created_at'  => now(),
            'updated_at'  => now(),
        ])->toArray();

        if (! empty($notifikasi)) {
            Notifikasi::insert($notifikasi);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal posyandu berhasil dibuat.',
            'data'    => $jadwal->load('kader'),
        ], 201);
    }

    public function show($id)
    {
        $jadwal = JadwalPosyandu::with(['kader', 'pemeriksaan.anak'])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPosyandu::findOrFail($id);

        $request->validate([
            'tgl_kegiatan' => 'sometimes|date',
            'lokasi'       => 'sometimes|string|max:255',
            'agenda'       => 'nullable|string',
        ]);

        $jadwal->update($request->only(['tgl_kegiatan', 'lokasi', 'agenda']));

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui.',
            'data'    => $jadwal->fresh('kader'),
        ]);
    }

    public function destroy($id)
    {
        JadwalPosyandu::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus.']);
    }
}