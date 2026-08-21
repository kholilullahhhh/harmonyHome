<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();
        $pemilikRole = Role::where('slug', 'pemilik')->first();
        $penyewaRole = Role::where('slug', 'penyewa')->first();

        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
            ]
        );

        // 2. Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // 3. Regular User
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role_id' => $userRole->id,
            ]
        );

        // 4. Pemilik Kost
        $pemilik = [
            ['name' => 'Hj. Nurhayati', 'email' => 'pemilik1@gmail.com', 'phone' => '081234567801', 'gender' => 'P'],
            ['name' => 'Andi Saputra', 'email' => 'pemilik2@gmail.com', 'phone' => '081234567802', 'gender' => 'L'],
        ];
        foreach ($pemilik as $i => $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $pemilikRole->id,
                    'phone' => $data['phone'],
                    'gender' => $data['gender'],
                    'address' => 'Jl. Perjuangan No. '.($i + 5).', Makassar',
                ]
            );
        }

        // 5. Penyewa
        $namaPenyewa = [
            ['Rizky Ramadhan', 'L'], ['Siti Aisyah', 'P'], ['Muhammad Fajar', 'L'],
            ['Dewi Lestari', 'P'], ['Ahmad Yani', 'L'], ['Nurul Hidayah', 'P'],
            ['Fahmi Amiruddin', 'L'], ['Ratna Sari', 'P'], ['Ilham Maulana', 'L'],
            ['Fitriani Abidin', 'P'],
        ];
        foreach ($namaPenyewa as $i => [$nama, $gender]) {
            $urut = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            User::updateOrCreate(
                ['email' => "penyewa{$urut}@gmail.com"],
                [
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'role_id' => $penyewaRole->id,
                    'phone' => '0812345679'.$urut,
                    'gender' => $gender,
                    'birthdate' => now()->subYears(rand(19, 28))->subDays(rand(1, 300)),
                    'address' => 'Jl. Contoh Alamat No. '.($i + 1).', Sulawesi Selatan',
                    'identity_number' => '73'.rand(10, 99).rand(10000000000000, 99999999999999),
                ]
            );
        }

        $this->command->info('Users created with password: password');
    }
}
