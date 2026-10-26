<?php

namespace App\Http\Controllers\Web\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Bidan;
use App\Models\JadwalPosyandu;
use App\Models\Kader;
use App\Models\Notifikasi;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->id_posyandu, 403, 'Akun belum terhubung dengan Posyandu.');

        $monthInput = $request->input('month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            abort(404);
        }

        $monthNumber = (int) substr($monthInput, 5, 2);
        if ($monthNumber < 1 || $monthNumber > 12) {
            abort(404);
        }

        $month = Carbon::createFromFormat('!Y-m', $monthInput);

        $idPosyandu = $user->id_posyandu;
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $schedules = JadwalPosyandu::where('id_posyandu', $idPosyandu)
            ->whereBetween('tgl_kegiatan', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('tgl_kegiatan')
            ->orderBy('id_jadwal')
            ->get();
        $schedulesByDate = $schedules->groupBy(fn ($schedule) => $schedule->tgl_kegiatan->toDateString());

        $selectedDateInput = $request->input('date', now()->toDateString());
        try {
            $selectedDate = Carbon::parse($selectedDateInput)->startOfDay();
        } catch (\Throwable $exception) {
            $selectedDate = now()->startOfDay();
        }
        $selectedDate = $selectedDate->between($monthStart, $monthEnd)
            ? $selectedDate
            : $monthStart->copy();
        $selectedSchedules = $schedulesByDate->get($selectedDate->toDateString(), collect());
        $editingSchedule = null;
        if ($request->filled('edit')) {
            $editingSchedule = JadwalPosyandu::where('id_posyandu', $idPosyandu)
                ->where('id_jadwal', $request->integer('edit'))
                ->first();
            abort_if(! $editingSchedule, 404);
        }

        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $calendarDays = collect();
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $date = $day->toDateString();
            $calendarDays->push([
                'date' => $day->copy(),
                'in_month' => $day->month === $month->month,
                'is_today' => $date === today()->toDateString(),
                'is_selected' => $date === $selectedDate->toDateString(),
                'events' => $schedulesByDate->get($date, collect()),
            ]);
        }

        $monthCount = $schedules->count();
        $completedSchedules = $schedules->filter(fn ($schedule) => $schedule->tgl_kegiatan->lt(today()));
        $todaySchedules = $schedules->filter(fn ($schedule) => $schedule->tgl_kegiatan->isToday());
        $upcomingSchedules = JadwalPosyandu::where('id_posyandu', $idPosyandu)
            ->whereDate('tgl_kegiatan', '>=', today())
            ->orderBy('tgl_kegiatan')
            ->limit(5)
            ->get();
        $unreadNotifications = Notifikasi::where('id_user', $user->id_user)
            ->where('status', 'Belum Dibaca')
            ->count();

        $totalChildren = Anak::where('id_posyandu', $idPosyandu)->count();
        $completedDates = $completedSchedules->map(fn ($schedule) => $schedule->tgl_kegiatan->toDateString());
        $attendanceRows = $completedDates->isEmpty()
            ? collect()
            : Pemeriksaan::where('id_posyandu', $idPosyandu)
                ->whereIn('tgl_periksa', $completedDates)
                ->select('tgl_periksa', DB::raw('COUNT(DISTINCT nik_anak) AS hadir'))
                ->groupBy('tgl_periksa')
                ->get();
        $averageAttendance = $attendanceRows->isNotEmpty() && $totalChildren > 0
            ? round($attendanceRows->avg(fn ($row) => min(100, ($row->hadir / $totalChildren) * 100)), 1)
            : null;

        $staffQuery = Kader::query()
            ->join('pengguna', 'pengguna.id_user', '=', 'kader.id_user')
            ->where('pengguna.id_posyandu', $idPosyandu);
        $kader = (clone $staffQuery)->orderBy('kader.nama_kader')->get(['kader.id_kader', 'kader.nama_kader']);
        $bidan = Bidan::where('id_posyandu', $idPosyandu)->orderBy('nama_bidan')->get(['nip', 'nama_bidan']);
        $unit = $user->posyandu;

        return view('posyandu.anak.jadwal', compact(
            'month',
            'calendarDays',
            'schedules',
            'selectedDate',
            'selectedSchedules',
            'editingSchedule',
            'monthCount',
            'completedSchedules',
            'todaySchedules',
            'upcomingSchedules',
            'unreadNotifications',
            'totalChildren',
            'averageAttendance',
            'kader',
            'bidan',
            'unit'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->id_posyandu, 403, 'Akun belum terhubung dengan Posyandu.');

        $kaderIds = Kader::query()
            ->join('pengguna', 'pengguna.id_user', '=', 'kader.id_user')
            ->where('pengguna.id_posyandu', $user->id_posyandu)
            ->select('kader.id_kader');

        $validated = $request->validate([
            'tgl_kegiatan' => ['required', 'date', 'after_or_equal:today'],
            'lokasi' => ['required', 'string', 'max:255'],
            'agenda' => ['nullable', 'string', 'max:2000'],
            'id_kader' => ['required', 'integer', Rule::in($kaderIds->pluck('id_kader')->all())],
            'id_jadwal' => [
                'nullable',
                'integer',
                Rule::exists('jadwal_posyandu', 'id_jadwal')->where('id_posyandu', $user->id_posyandu),
            ],
        ]);

        $scheduleData = [
            'id_kader' => $validated['id_kader'],
            'tgl_kegiatan' => $validated['tgl_kegiatan'],
            'lokasi' => $validated['lokasi'],
            'agenda' => $validated['agenda'] ?? null,
        ];

        if (! empty($validated['id_jadwal'])) {
            JadwalPosyandu::where('id_posyandu', $user->id_posyandu)
                ->where('id_jadwal', $validated['id_jadwal'])
                ->firstOrFail()
                ->update($scheduleData);
        } else {
            JadwalPosyandu::create([
                ...$scheduleData,
                'id_posyandu' => $user->id_posyandu,
            ]);
        }

        return redirect()
            ->route('posyandu.jadwal.index', ['month' => Carbon::parse($validated['tgl_kegiatan'])->format('Y-m'), 'date' => $validated['tgl_kegiatan']])
            ->with('success', ! empty($validated['id_jadwal'])
                ? 'Jadwal Posyandu berhasil diperbarui.'
                : 'Jadwal Posyandu berhasil ditambahkan.');
    }

    public function linkCurrentUser()
    {
        $user = Auth::user();
        abort_unless($user->id_posyandu, 403, 'Akun belum terhubung dengan Posyandu.');

        $kader = Kader::firstOrCreate(
            ['id_user' => $user->id_user],
            ['nama_kader' => $user->username]
        );

        return redirect()
            ->route('posyandu.jadwal.index')
            ->with('success', $kader->wasRecentlyCreated
                ? 'Akun Anda berhasil dihubungkan sebagai kader penanggung jawab.'
                : 'Profil kader untuk akun Anda sudah terhubung.');
    }
}
