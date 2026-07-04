<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'super_admin','guard_name' => 'web',]);
        $customerRole = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        $OwnerRole = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $KurirRole = Role::firstOrCreate(['name' => 'Kurir', 'guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin'
            ]
        );

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole($adminRole);
        }

        $users = [
            ['name' => 'Rafli', 'email' => 'rafli@gmail.com', 'address' => 'Jl. Merdeka RT 01/02, No. 10, Jakarta', 'no_tlp' => '0812328149'],
            ['name' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'address' => 'Jl. Mawar No. 45, Bandung', 'no_tlp' => '081299887766'],
            ['name' => 'Siti Aminah', 'email' => 'siti@gmail.com', 'address' => 'Komp. Hijau Blok C, Surabaya', 'no_tlp' => '085711223344'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@gmail.com', 'address' => 'Jl. Jendral Sudirman No. 12, Semarang', 'no_tlp' => '081344556677'],
            ['name' => 'Rina Permata', 'email' => 'rina@gmail.com', 'address' => 'Perum Indah Permai Gg. 4, Yogyakarta', 'no_tlp' => '081900112233'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@gmail.com', 'address' => 'Jl. Melati RT 05 RW 01, Malang', 'no_tlp' => '082188776655'],
            ['name' => 'Fajar Ramadhan', 'email' => 'fajar@gmail.com', 'address' => 'Desa Sukamaju No. 88, Bogor', 'no_tlp' => '085233445566'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@gmail.com', 'address' => 'Jl. Pahlawan No. 21, Solo', 'no_tlp' => '081266778899'],
            ['name' => 'Maya Saputri', 'email' => 'maya@gmail.com', 'address' => 'Apartemen Gading Lt. 5, Jakarta Utara', 'no_tlp' => '087855443322'],
            ['name' => 'Hendra Kusuma', 'email' => 'hendra@gmail.com', 'address' => 'Jl. Ahmad Yani No. 15, Medan', 'no_tlp' => '081199001122'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'address'  => $userData['address'],
                    'no_tlp'   => $userData['no_tlp'],
                    'password' => Hash::make('admin123'),
                ]
            );

            if (!$user->hasRole('customer')) {
                $user->assignRole($customerRole);
            }
        }

        $this->command->info('Berhasil! 1 Admin & 10 Customer telah dibuat.');
    }
}
