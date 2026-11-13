@extends('layouts.app')
@section('title', 'Data Pegawai')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-4">
    <h1 class="text-lg font-semibold">Data Pegawai</h1>
    <div class="flex items-end gap-2">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIP / email"
                   class="rounded border border-slate-300 px-3 py-1.5 text-sm">
            <button class="rounded bg-slate-200 px-4 py-1.5 text-sm">Cari</button>
        </form>
        <a href="{{ route('admin.pegawai.create') }}" class="rounded bg-slate-900 text-white px-4 py-1.5 text-sm">+ Tambah</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">NIP</th>
                <th class="px-4 py-3">Jabatan</th>
                <th class="px-4 py-3">Unit Kerja</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($pegawai as $p)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $p->name }}</div>
                        <div class="text-xs text-slate-500">{{ $p->email }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $p->nip ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $p->jabatan ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $p->unit_kerja ?: '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded px-2 py-0.5 text-xs {{ $p->role === 'admin' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $p->role }}</span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.pegawai.edit', $p) }}" class="text-blue-600 hover:underline">Edit</a>
                        @if($p->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.pegawai.destroy', $p) }}" class="inline"
                                  onsubmit="return confirm('Hapus {{ addslashes($p->name) }}? Seluruh data absensinya ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline ml-3">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pegawai->links() }}</div>
@endsection
