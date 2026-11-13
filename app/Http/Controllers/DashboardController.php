<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::now(config('absensi.timezone'))->toDateString();

        $absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $stat = null;

        if ($user->isAdmin()) {
            $base = fn () => Absensi::whereDate('tanggal', $today)
                ->whereHas('user', fn ($q) => $q->where('role', 'pegawai'));

            $totalPegawai = User::where('role', 'pegawai')->count();
            $hadir = $base()->whereIn('status', ['hadir', 'dinas_luar'])->count();
            $terlambat = $base()->where('status', 'terlambat')->count();
            $izin = $base()->whereIn('status', ['izin', 'sakit'])->count();

            $stat = [
                'pegawai' => $totalPegawai,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'belum' => max(0, $totalPegawai - ($hadir + $terlambat + $izin)),
            ];
        }

        return view('dashboard', compact('absenHariIni', 'stat'));
    }
}
