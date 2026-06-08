<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Notifikasi;
use App\Models\OrangTua;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnakController extends Controller
{
    /**
     * List anak — filter by posyandu aktif
     * Kader: semua anak di posyanduny
     * OrangTua: hanya anaknya sendiri
     */
    public function index(Request $request)
    {
        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $query = Anak::with(['orangTua', 'posyandu']);

        if ($user->isOrangTua()) {
            // OrangTua hanya lihat anaknya sendiri
            $query->where('nik_orang_tua', $user->orangTua->nik_orang_tua);
        } elseif ($user->isKader() || $user->isBidan()) {
            if ($idPosyandu) {
                $nikOrtu = \App\Models\OrangTua::whereHas('pengguna', fn($q) =>
                    $q->where('id_posyandu', $idPosyandu)
                )->pluck('nik_orang_tua');
                $query->whereIn('nik_orang_tua', $nikOrtu);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_anak', 'like', '%' . $request->search . '%')
                  ->orWhere('nik_anak', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $anak = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $anak,
        ]);
    }

    /**
     * KF-004: Tambah balita baru
     * Secara otomatis mengaktifkan akses login Orang Tua
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik_anak'      => 'required|string|size:16|unique:anak,nik_anak',
            'nama_anak'     => 'required|string|max:100',
            'tgl_lahir'     => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            // Data Orang Tua
            'nik_orang_tua' => 'nullable|string|size:16',
            'nama_ibu'      => 'required|string|max:100',
            'no_telp_ibu'   => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
        ]);

        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $anak = DB::transaction(function () use ($request, $idPosyandu) {

            // ── 1. Buat atau ambil data OrangTua ──────────────────────
            $orangTua = OrangTua::firstOrNew(
                ['nik_orang_tua' => $request->nik_orang_tua ?? ('NIK-' . time())]
            );

            // Jika OrangTua belum punya akun → buat akun otomatis
            if (! $orangTua->exists || ! $orangTua->id_user) {
                // Buat akun Pengguna untuk OrangTua
                // Kredensial: NIK Balita + Tanggal Lahir (dihandle di loginOrangTua)
                // Akun ini tidak butuh username/password karena login pakai NIK+TglLahir
                $penggunaOrtu = Pengguna::create([
                    'username'    => 'ortu_' . time(),
                    'password'    => bcrypt('ortu_' . time()),
                    'role'        => 'OrangTua',
                    'id_posyandu' => $idPosyandu,
                ]);

                $orangTua->fill([
                    'id_user'  => $penggunaOrtu->id_user,
                    'nama_ibu' => $request->nama_ibu,
                    'alamat'   => $request->alamat,
                ]);
                $orangTua->save();
            } else {
                // Update data orang tua jika sudah ada
                $orangTua->update([
                    'nama_ibu' => $request->nama_ibu,
                    'alamat'   => $request->alamat ?? $orangTua->alamat,
                ]);
            }

            // ── 2. Buat data Anak ─────────────────────────────────────
            $anak = Anak::create([
                'nik_anak'      => $request->nik_anak,
                'nik_orang_tua' => $orangTua->nik_orang_tua,
                'nama_anak'     => $request->nama_anak,
                'tgl_lahir'     => $request->tgl_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
            ]);

            // ── 3. Kirim notifikasi sambutan ke OrangTua ──────────────
            if ($orangTua->id_user) {
                Notifikasi::create([
                    'id_user'     => $orangTua->id_user,
                    'nik_anak'    => $anak->nik_anak,
                    'pesan'       => "Selamat! Data {$anak->nama_anak} telah terdaftar di Posyandu. "
                                   . "Anda dapat login menggunakan NIK Balita dan Tanggal Lahir.",
                    'tgl_kirim'   => now(),
                    'status'      => 'Belum Dibaca',
                    'jenis_notif' => 'Umum',
                ]);
            }

            return $anak;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data balita berhasil didaftarkan. Akun Orang Tua otomatis aktif.',
            'data'    => $anak->load(['orangTua', 'posyandu']),
        ], 201);
    }

    /**
     * Detail anak + riwayat pemeriksaan & imunisasi
     */
    public function show(Request $request, $nik)
    {
        $user = $request->user();
        $anak = Anak::with([
            'orangTua',
            'posyandu',
            'pemeriksaan' => fn ($q) => $q->orderBy('tgl_periksa', 'desc'),
            'imunisasi.jenisVaksin',
        ])->findOrFail($nik);

        // OrangTua hanya bisa lihat anaknya sendiri
        if ($user->isOrangTua()) {
            abort_unless(
                $anak->nik_orang_tua === $user->orangTua->nik_orang_tua,
                403,
                'Akses ditolak.'
            );
        }

        return response()->json([
            'success' => true,
            'data'    => array_merge($anak->toArray(), [
                'umur_bulan'  => $anak->umur_bulan,
                'umur_format' => $anak->umur_format,
            ]),
        ]);
    }

    /**
     * Update data anak
     */
    public function update(Request $request, $nik)
    {
        $anak = Anak::findOrFail($nik);

        $request->validate([
            'nama_anak'     => 'sometimes|string|max:100',
            'tgl_lahir'     => 'sometimes|date',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'nama_ayah'     => 'nullable|string|max:100',
            'nama_ibu'      => 'sometimes|string|max:100',
            'no_telp_ibu'   => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $anak) {
            $anak->update($request->only([
                'nama_anak', 'tgl_lahir', 'jenis_kelamin', 'nama_ayah',
            ]));

            // Update data orang tua jika ada
            if ($anak->orangTua && $request->hasAny(['nama_ibu', 'no_telp_ibu', 'alamat'])) {
                $anak->orangTua->update(array_filter([
                    'nama_ibu' => $request->nama_ibu,
                    'no_telp'  => $request->no_telp_ibu,
                    'alamat'   => $request->alamat,
                ]));
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil diperbarui.',
            'data'    => $anak->fresh(['orangTua', 'posyandu']),
        ]);
    }

    /**
     * Hapus data anak
     */
    public function destroy($nik)
    {
        $anak = Anak::findOrFail($nik);
        $anak->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil dihapus.',
        ]);
    }

    /**
     * KF-007: Data perkembangan untuk grafik KMS
     */
    public function perkembangan(Request $request, $nik)
    {
        $user = $request->user();
        $anak = Anak::findOrFail($nik);

        if ($user->isOrangTua()) {
            abort_unless(
                $anak->nik_orang_tua === $user->orangTua->nik_orang_tua,
                403, 'Akses ditolak.'
            );
        }

        $pemeriksaan = $anak->pemeriksaan()
            ->select('tgl_periksa', 'berat_badan', 'tinggi_badan', 'lingkar_kepala')
            ->where('status_validasi', 'Disetujui')
            ->orderBy('tgl_periksa', 'asc')
            ->get()
            ->map(fn ($p) => [
                'tgl_periksa'   => $p->tgl_periksa->format('Y-m-d'),
                'umur_bulan'    => $anak->tgl_lahir->diffInMonths($p->tgl_periksa),
                'berat_badan'   => $p->berat_badan,
                'tinggi_badan'  => $p->tinggi_badan,
                'lingkar_kepala'=> $p->lingkar_kepala,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'anak'        => [
                    'nik_anak'    => $anak->nik_anak,
                    'nama_anak'   => $anak->nama_anak,
                    'tgl_lahir'   => $anak->tgl_lahir->format('Y-m-d'),
                    'jenis_kelamin' => $anak->jenis_kelamin,
                    'umur_bulan'  => $anak->umur_bulan,
                    'umur_format' => $anak->umur_format,
                ],
                'pemeriksaan' => $pemeriksaan,
                'total_data'  => $pemeriksaan->count(),
            ],
        ]);
    }
}