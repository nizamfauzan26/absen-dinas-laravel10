@extends('layouts.app')
@section('title', $pegawai->exists ? 'Edit Pegawai' : 'Tambah Pegawai')

@section('content')
<h1 class="text-lg font-semibold mb-4">{{ $pegawai->exists ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h1>

<form method="POST"
      action="{{ $pegawai->exists ? route('admin.pegawai.update', $pegawai) : route('admin.pegawai.store') }}"
      class="bg-white rounded-xl shadow-sm p-6 max-w-2xl space-y-4">
    @csrf
    @if($pegawai->exists) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">Nama lengkap</label>
            <input type="text" name="name" value="{{ old('name', $pegawai->name) }}" required
                   class="w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">NIP</label>
            <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Jabatan</label>
            <input type="text" name="jabatan" value="{{ old('jabatan', $pegawai->jabatan) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Unit kerja</label>
            <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $pegawai->unit_kerja) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $pegawai->email) }}" required
                   class="w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Role</label>
            <select name="role" class="w-full rounded border border-slate-300 px-3 py-2">
                @foreach(['pegawai' => 'Pegawai', 'admin' => 'Admin'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('role', $pegawai->role) === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm mb-1">
                Password @if($pegawai->exists)<span class="text-slate-400">(kosongkan bila tidak diubah)</span>@endif
            </label>
            <input type="password" name="password" {{ $pegawai->exists ? '' : 'required' }} minlength="6"
                   class="w-full rounded border border-slate-300 px-3 py-2">
        </div>
    </div>

    <div class="flex gap-3 pt-2">
        <button class="rounded bg-slate-900 text-white px-5 py-2 font-medium">Simpan</button>
        <a href="{{ route('admin.pegawai.index') }}" class="rounded bg-slate-200 px-5 py-2">Batal</a>
    </div>
</form>
@endsection
