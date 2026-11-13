@extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')
<h1 class="text-lg font-semibold mb-4">Rekap Absensi</h1>

<form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-4 grid sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
    <div>
        <label class="block text-xs text-slate-500 mb-1">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm">
    </div>
    <div>
        <label class="block text-xs text-slate-500 mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm">
    </div>
    <div>
        <label class="block text-xs text-slate-500 mb-1">Pegawai</label>
        <select name="user_id" class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm">
            <option value="">Semua</option>
            @foreach($daftarPegawai as $p)
                <option value="{{ $p->id }}" @selected(request('user_id') == $p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-slate-500 mb-1">Status</label>
        <select name="status" class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm">
            <option value="">Semua</option>
            @foreach(\App\Models\Absensi::STATUS as $val => $label)
                <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button class="rounded bg-slate-900 text-white px-4 py-1.5 text-sm">Filter</button>
        <a href="{{ route('admin.rekap.export', request()->query()) }}"
           class="rounded bg-green-600 text-white px-4 py-1.5 text-sm">Export CSV</a>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Pegawai</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Masuk</th>
                <th class="px-4 py-3">Pulang</th>
                <th class="px-4 py-3">Lokasi masuk</th>
                <th class="px-4 py-3">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($data as $a)
                <tr>
                    <td class="px-4 py-3">{{ $a->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $a->user?->name ?? '-' }}</div>
                        <div class="text-xs text-slate-500">{{ $a->user?->nip }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="rounded px-2 py-0.5 text-xs font-medium {{ $a->badge }}">{{ $a->status_label }}</span>
                    </td>
                    <td class="px-4 py-3 tabular-nums">{{ $a->jam('jam_masuk') }}</td>
                    <td class="px-4 py-3 tabular-nums">{{ $a->jam('jam_pulang') }}</td>
                    <td class="px-4 py-3 text-xs">
                        @if($a->lokasi_masuk)
                            <a class="text-blue-600 hover:underline" target="_blank" rel="noopener"
                               href="https://www.google.com/maps?q={{ $a->lokasi_masuk }}">{{ $a->lokasi_masuk }}</a>
                        @else - @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $a->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Tidak ada data pada rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $data->links() }}</div>
@endsection
