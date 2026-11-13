@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@if($stat)
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        @foreach([
            ['Total Pegawai', $stat['pegawai'], 'text-slate-900'],
            ['Hadir / Dinas Luar', $stat['hadir'], 'text-green-600'],
            ['Terlambat', $stat['terlambat'], 'text-amber-600'],
            ['Izin / Sakit', $stat['izin'], 'text-purple-600'],
            ['Belum Absen', $stat['belum'], 'text-red-600'],
        ] as [$label, $nilai, $warna])
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-xs text-slate-500">{{ $label }}</div>
                <div class="text-2xl font-semibold {{ $warna }}">{{ $nilai }}</div>
            </div>
        @endforeach
    </div>
@endif

<div class="grid md:grid-cols-3 gap-6">
    {{-- Panel jam & status hari ini --}}
    <section class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-slate-500" id="tanggal"></div>
        <div class="text-4xl font-semibold tabular-nums mt-1" id="jam">--:--:--</div>
        <div class="text-xs text-slate-400 mt-1">
            Masuk paling lambat {{ config('absensi.jam_masuk') }} · Pulang {{ config('absensi.jam_pulang') }} WIB
        </div>

        <div class="mt-6 border-t pt-4 text-sm space-y-2">
            <div class="font-medium">Status hari ini</div>
            @if($absenHariIni)
                <span class="inline-block rounded px-2 py-0.5 text-xs font-medium {{ $absenHariIni->badge }}">
                    {{ $absenHariIni->status_label }}
                </span>
                <div class="flex justify-between"><span class="text-slate-500">Jam masuk</span><span>{{ $absenHariIni->jam('jam_masuk') }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Jam pulang</span><span>{{ $absenHariIni->jam('jam_pulang') }}</span></div>
                @if($absenHariIni->keterangan)
                    <div class="text-slate-500">Ket: {{ $absenHariIni->keterangan }}</div>
                @endif
            @else
                <p class="text-slate-500">Belum absen.</p>
            @endif
        </div>
    </section>

    {{-- Panel aksi absen --}}
    <section class="md:col-span-2 space-y-6">
        @if(! $absenHariIni)
            <form method="POST" action="{{ route('absen.masuk') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                @csrf
                <h2 class="font-semibold">Absen Masuk</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Jenis kehadiran</label>
                        <select name="status" class="w-full rounded border border-slate-300 px-3 py-2">
                            <option value="hadir">Hadir di kantor</option>
                            <option value="dinas_luar">Dinas luar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Keterangan (opsional)</label>
                        <input type="text" name="keterangan" maxlength="500" placeholder="Mis. rapat di kantor gubernur"
                               class="w-full rounded border border-slate-300 px-3 py-2">
                    </div>
                </div>
                <input type="hidden" name="lokasi" class="input-lokasi">
                <div class="flex items-center gap-3">
                    <button class="rounded bg-green-600 hover:bg-green-700 text-white px-5 py-2 font-medium">Absen Masuk</button>
                    <span class="text-xs text-slate-500 info-lokasi">Mengambil lokasi…</span>
                </div>
            </form>

            <form method="POST" action="{{ route('absen.izin') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                @csrf
                <h2 class="font-semibold">Izin / Sakit</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Jenis</label>
                        <select name="status" class="w-full rounded border border-slate-300 px-3 py-2">
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Alasan</label>
                        <input type="text" name="keterangan" required maxlength="500"
                               class="w-full rounded border border-slate-300 px-3 py-2">
                    </div>
                </div>
                <button class="rounded bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 font-medium">Kirim</button>
            </form>
        @elseif($absenHariIni->jam_masuk && ! $absenHariIni->jam_pulang)
            <form method="POST" action="{{ route('absen.pulang') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                @csrf
                <h2 class="font-semibold">Absen Pulang</h2>
                <input type="hidden" name="lokasi" class="input-lokasi">
                <div class="flex items-center gap-3">
                    <button class="rounded bg-slate-900 hover:bg-slate-800 text-white px-5 py-2 font-medium">Absen Pulang</button>
                    <span class="text-xs text-slate-500 info-lokasi">Mengambil lokasi…</span>
                </div>
            </form>
        @else
            <div class="bg-white rounded-xl shadow-sm p-6 text-sm text-slate-600">
                Absensi Anda hari ini sudah lengkap. Terima kasih.
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Jam digital (WIB)
    function tick() {
        const now = new Date();
        document.getElementById('jam').textContent =
            now.toLocaleTimeString('id-ID', {timeZone: 'Asia/Jakarta', hour12: false}).replace(/\./g, ':');
        document.getElementById('tanggal').textContent =
            now.toLocaleDateString('id-ID', {timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'});
    }
    tick(); setInterval(tick, 1000);

    // Ambil koordinat GPS (opsional, absen tetap bisa tanpa lokasi)
    const inputs = document.querySelectorAll('.input-lokasi');
    const infos = document.querySelectorAll('.info-lokasi');
    if (inputs.length) {
        if (!navigator.geolocation) {
            infos.forEach(i => i.textContent = 'Lokasi tidak didukung browser.');
        } else {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const v = pos.coords.latitude.toFixed(6) + ',' + pos.coords.longitude.toFixed(6);
                    inputs.forEach(i => i.value = v);
                    infos.forEach(i => i.textContent = 'Lokasi: ' + v);
                },
                () => infos.forEach(i => i.textContent = 'Lokasi tidak diizinkan (absen tetap bisa).')
            );
        }
    }
</script>
@endpush
