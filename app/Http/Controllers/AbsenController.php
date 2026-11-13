<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AbsenController extends Controller
{
    private function sekarang(): Carbon
    {
        return Carbon::now(config('absensi.timezone'));
    }

    private function absenHariIni(Request $request, Carbon $now): ?Absensi
    {
        return Absensi::where('user_id', $request->user()->id)
            ->whereDate('tanggal', $now->toDateString())
            ->first();
    }

    public function masuk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:hadir,dinas_luar'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $now = $this->sekarang();

        if ($this->absenHariIni($request, $now)) {
            return back()->with('error', 'Anda sudah melakukan absen hari ini.');
        }

        $status = $data['status'];
        if ($status === 'hadir' && $now->format('H:i') > config('absensi.jam_masuk')) {
            $status = 'terlambat';
        }

        Absensi::create([
            'user_id' => $request->user()->id,
            'tanggal' => $now->toDateString(),
            'jam_masuk' => $now->format('H:i:s'),
            'status' => $status,
            'lokasi_masuk' => $data['lokasi'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return back()->with('success', 'Absen masuk berhasil pukul ' . $now->format('H:i') . '.');
    }

    public function pulang(Request $request): RedirectResponse
    {
        $request->validate(['lokasi' => ['nullable', 'string', 'max:100']]);

        $now = $this->sekarang();
        $absen = $this->absenHariIni($request, $now);

        if (! $absen || ! $absen->jam_masuk) {
            return back()->with('error', 'Anda belum absen masuk hari ini.');
        }

        if ($absen->jam_pulang) {
            return back()->with('error', 'Anda sudah absen pulang hari ini.');
        }

        $absen->update([
            'jam_pulang' => $now->format('H:i:s'),
            'lokasi_pulang' => $request->input('lokasi'),
        ]);

        return back()->with('success', 'Absen pulang berhasil pukul ' . $now->format('H:i') . '.');
    }

    public function izin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:izin,sakit'],
            'keterangan' => ['required', 'string', 'max:500'],
        ]);

        $now = $this->sekarang();

        if ($this->absenHariIni($request, $now)) {
            return back()->with('error', 'Anda sudah melakukan absen hari ini.');
        }

        Absensi::create([
            'user_id' => $request->user()->id,
            'tanggal' => $now->toDateString(),
            'status' => $data['status'],
            'keterangan' => $data['keterangan'],
        ]);

        return back()->with('success', 'Pengajuan ' . $data['status'] . ' berhasil dicatat.');
    }

    public function riwayat(Request $request)
    {
        $bulan = $request->input('bulan');
        if (! is_string($bulan) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
            $bulan = $this->sekarang()->format('Y-m');
        }

        [$tahun, $bln] = explode('-', $bulan);

        $data = Absensi::where('user_id', $request->user()->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->orderByDesc('tanggal')
            ->paginate(31)
            ->withQueryString();

        return view('absen.riwayat', compact('data', 'bulan'));
    }
}
