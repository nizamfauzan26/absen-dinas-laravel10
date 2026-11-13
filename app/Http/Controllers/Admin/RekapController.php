<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    private function query(Request $request): array
    {
        $tz = config('absensi.timezone');
        $dari = $request->input('dari', Carbon::now($tz)->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', Carbon::now($tz)->toDateString());

        $q = Absensi::with('user')->whereBetween('tanggal', [$dari, $sampai]);

        if ($request->filled('user_id')) {
            $q->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        return [$q->orderByDesc('tanggal')->orderBy('user_id'), $dari, $sampai];
    }

    public function index(Request $request)
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        [$q, $dari, $sampai] = $this->query($request);

        $data = $q->paginate(25)->withQueryString();
        $daftarPegawai = User::orderBy('name')->get(['id', 'name']);

        return view('admin.rekap', compact('data', 'dari', 'sampai', 'daftarPegawai'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        [$q, $dari, $sampai] = $this->query($request);
        $rows = $q->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM supaya terbaca benar di Excel
            fputcsv($out, ['Tanggal', 'NIP', 'Nama', 'Jabatan', 'Unit Kerja', 'Status', 'Jam Masuk', 'Jam Pulang', 'Keterangan']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->tanggal->format('Y-m-d'),
                    $r->user?->nip,
                    $r->user?->name,
                    $r->user?->jabatan,
                    $r->user?->unit_kerja,
                    $r->status_label,
                    $r->jam('jam_masuk'),
                    $r->jam('jam_pulang'),
                    $r->keterangan,
                ]);
            }

            fclose($out);
        }, "rekap-absensi-{$dari}-sd-{$sampai}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
