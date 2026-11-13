<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Absensi Dinas')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">

<nav class="bg-slate-900 text-white">
    <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center gap-x-6 gap-y-2">
        <a href="{{ route('dashboard') }}" class="font-semibold tracking-tight">Absensi Dinas</a>

        <div class="flex flex-wrap gap-4 text-sm text-slate-300">
            <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
            <a href="{{ route('absen.riwayat') }}" class="hover:text-white">Riwayat Saya</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.pegawai.index') }}" class="hover:text-white">Data Pegawai</a>
                <a href="{{ route('admin.rekap') }}" class="hover:text-white">Rekap Absensi</a>
            @endif
        </div>

        <div class="ml-auto flex items-center gap-3 text-sm">
            <span class="text-slate-300">{{ auth()->user()->name }}
                <span class="text-xs text-slate-400">({{ auth()->user()->role }})</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded bg-slate-700 hover:bg-slate-600 px-3 py-1">Keluar</button>
            </form>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto px-4 py-6">
    @if(session('success'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
