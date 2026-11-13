<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['admin@dinas.com', 'Administrator', '199001010000000001', 'Admin Sistem', 'Sekretariat', 'admin'],
            ['nizam@dinas.com', 'Nizam Fauzan', '199205050000000002', 'Staf Pelaksana', 'Bidang Prodatin', 'pegawai'],
            ['rafly@dinas.com', 'Rafly Rodiansyah', '199308080000000003', 'Analis Kepegawaian', 'Bidang PM', 'pegawai'],
        ];

        foreach ($akun as [$email, $nama, $nip, $jabatan, $unit, $role]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'nip' => $nip,
                    'jabatan' => $jabatan,
                    'unit_kerja' => $unit,
                    'role' => $role,
                    'password' => Hash::make('1'),
                ]
            );
        }
    }
}
