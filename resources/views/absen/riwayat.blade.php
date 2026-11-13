@extends('layouts.app')
@section('title', 'Riwayat Absensi')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-4">
    <h1 class="text-lg font-semibold">Riwayat Absensi Saya</h1>
    <form method="GET" class="flex items-end gap-2">
        <div>
            <label class="block text-xs text-slate-500 mb-1">Bulan</label>
            <input type="month" name="bulan" value="{{ $bulan }}" class="rounded border border-slate-300 px-3 py-1.5 text-sm">
        </div>
        <button class="rounded bg-slate-900 text-white px-4 py-1.5 text-sm">Tampilkan</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Masuk</th>
                <th class="px-4 py-3">Pulang</th>
                <th class="px-4 py-3">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($data as $a)
                <tr>
                    <td class="px-4 py-3">{{ $a->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded px-2 py-0.5 text-xs font-medium {{ $a->badge }}">{{ $a->status_label }}</span>
                    </td>
                    <td class="px-4 py-3 tabular-nums">{{ $a->jam('jam_masuk') }}</td>
                    <td class="px-4 py-3 tabular-nums">{{ $a->jam('jam_pulang') }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $a->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data pada bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $data->links() }}</div>
@endsection
