<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * List notifikasi milik user yang login
     */
    public function index(Request $request)
    {
        $query = Notifikasi::where('id_user', $request->user()->id_user)
            ->with('anak');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_notif')) {
            $query->where('jenis_notif', $request->jenis_notif);
        }

        $notifikasi  = $query->orderBy('tgl_kirim', 'desc')->paginate(20);
        $unreadCount = Notifikasi::where('id_user', $request->user()->id_user)
            ->where('status', 'Belum Dibaca')
            ->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $unreadCount,
            'data'         => $notifikasi,
        ]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca
     */
    public function markRead(Request $request, $id)
    {
        $notif = Notifikasi::findOrFail($id);

        abort_unless(
            $notif->id_user === $request->user()->id_user,
            403, 'Akses ditolak.'
        );

        $notif->update(['status' => 'Sudah Dibaca']);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    /**
     * Tandai semua notifikasi sudah dibaca
     */
    public function markAllRead(Request $request)
    {
        Notifikasi::where('id_user', $request->user()->id_user)
            ->where('status', 'Belum Dibaca')
            ->update(['status' => 'Sudah Dibaca']);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy(Request $request, $id)
    {
        $notif = Notifikasi::findOrFail($id);

        abort_unless(
            $notif->id_user === $request->user()->id_user,
            403, 'Akses ditolak.'
        );

        $notif->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi dihapus.',
        ]);
    }

    /**
     * Jumlah notifikasi belum dibaca (untuk badge)
     */
    public function unreadCount(Request $request)
    {
        $count = Notifikasi::where('id_user', $request->user()->id_user)
            ->where('status', 'Belum Dibaca')
            ->count();

        return response()->json([
            'success' => true,
            'data'    => ['unread_count' => $count],
        ]);
    }
}