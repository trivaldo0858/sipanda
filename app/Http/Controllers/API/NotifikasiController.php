<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Notifikasi milik user yang sedang login
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

        return response()->json([
            'success'          => true,
            'unread_count'     => Notifikasi::where('id_user', $request->user()->id_user)
                                            ->where('status', 'Belum Dibaca')->count(),
            'data'             => $query->orderBy('tgl_kirim', 'desc')->paginate(20),
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     */
    public function markRead($id)
    {
        $notif = Notifikasi::findOrFail($id);

        abort_unless($notif->id_user === request()->user()->id_user, 403, 'Akses ditolak.');

        $notif->update(['status' => 'Sudah Dibaca']);

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca
     */
    public function markAllRead(Request $request)
    {
        Notifikasi::where('id_user', $request->user()->id_user)
            ->where('status', 'Belum Dibaca')
            ->update(['status' => 'Sudah Dibaca']);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $notif = Notifikasi::findOrFail($id);
        abort_unless($notif->id_user === request()->user()->id_user, 403, 'Akses ditolak.');

        $notif->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi dihapus.']);
    }
}