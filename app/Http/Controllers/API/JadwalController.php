<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JadwalPosyandu;
use App\Models\Notifikasi;
use App\Models\OrangTua;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * List jadwal — filter by posyandu aktif
     */
    public function index(Request $request)
    {
        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $query = JadwalPosyandu::with('posyandu');

        if ($idPosyandu) {
            $query->where('id_posyandu', $idPosyandu);
        }

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

    /**
     * KF-008: Buat jadwal baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tgl_kegiatan' => 'required|date|after_or_equal:today',
            'lokasi'       => 'required|string|max:255',
            'agenda'       => 'nullable|string',
        ]);

        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $jadwal = JadwalPosyandu::create([
            'id_posyandu'  => $idPosyandu,
            'tgl_kegiatan' => $request->tgl_kegiatan,
            'lokasi'       => $request->lokasi,
            'agenda'       => $request->agenda,
        ]);

        // KF-009: Broadcast notifikasi ke semua OrangTua di posyandu ini
        $this->kirimNotifikasiJadwal($jadwal, $idPosyandu);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat dan notifikasi dikirim.',
            'data'    => $jadwal->load('posyandu'),
        ], 201);
    }

    /**
     * Detail jadwal
     */
    public function show($id)
    {
        $jadwal = JadwalPosyandu::with('posyandu')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $jadwal,
        ]);
    }

    /**
     * Update jadwal
     */
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
            'data'    => $jadwal->fresh('posyandu'),
        ]);
    }

    /**
     * Hapus jadwal
     */
    public function destroy($id)
    {
        JadwalPosyandu::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus.',
        ]);
    }

    // ── Helper: kirim notifikasi ke semua OrangTua posyandu ───────────
    private function kirimNotifikasiJadwal(JadwalPosyandu $jadwal, ?int $idPosyandu): void
    {
        if (! $idPosyandu) return;

        // Ambil semua OrangTua yang punya anak di posyandu ini
        $orangTuaUsers = OrangTua::whereHas('anak', fn ($q) =>
            $q->where('id_posyandu', $idPosyandu)
        )->with('pengguna')->get();

        $tgl    = $jadwal->tgl_kegiatan->format('d/m/Y');
        $notifs = $orangTuaUsers
            ->filter(fn ($ot) => $ot->pengguna)
            ->map(fn ($ot) => [
                'id_user'     => $ot->pengguna->id_user,
                'nik_anak'    => null,
                'pesan'       => "Jadwal Posyandu: {$tgl} di {$jadwal->lokasi}."
                               . ($jadwal->agenda ? " Agenda: {$jadwal->agenda}" : ''),
                'tgl_kirim'   => now(),
                'status'      => 'Belum Dibaca',
                'jenis_notif' => 'Posyandu',
                'created_at'  => now(),
                'updated_at'  => now(),
            ])->toArray();

        if (! empty($notifs)) {
            Notifikasi::insert($notifs);
        }
    }
}