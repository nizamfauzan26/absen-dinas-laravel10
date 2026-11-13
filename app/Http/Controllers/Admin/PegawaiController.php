<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = User::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $kata = '%' . $request->q . '%';
                $q->where(function ($w) use ($kata) {
                    $w->where('name', 'like', $kata)
                        ->orWhere('nip', 'like', $kata)
                        ->orWhere('email', 'like', $kata);
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('admin.pegawai.form', ['pegawai' => new User(['role' => 'pegawai'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:30', 'unique:users,nip'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'unit_kerja' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'in:admin,pegawai'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(User $pegawai)
    {
        return view('admin.pegawai.form', compact('pegawai'));
    }

    public function update(Request $request, User $pegawai): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($pegawai->id)],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'unit_kerja' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'in:admin,pegawai'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pegawai->id)],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Cegah admin menurunkan role dirinya sendiri
        if ($pegawai->id === $request->user()->id) {
            $data['role'] = 'admin';
        }

        $pegawai->update($data);

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai diperbarui.');
    }

    public function destroy(Request $request, User $pegawai): RedirectResponse
    {
        if ($pegawai->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $pegawai->delete();

        return back()->with('success', 'Pegawai dihapus.');
    }
}
