<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Absensi Dinas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm bg-white rounded-xl shadow p-8">
        <h1 class="text-xl font-semibold text-slate-900">Absensi Dinas</h1>
        <p class="text-sm text-slate-500 mt-1 mb-6">Masuk dengan akun pegawai Anda.</p>

        <form method="POST" action="{{ route('login.proses') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400">
                @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="password">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full rounded border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded"> Ingat saya
            </label>
            <button class="w-full rounded bg-slate-900 hover:bg-slate-800 text-white py-2 font-medium">Masuk</button>
        </form>
    </div>
</body>
</html>
