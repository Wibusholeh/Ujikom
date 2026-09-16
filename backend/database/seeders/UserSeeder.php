<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('Admin123'),
                'role' => 'admin',
                'no_hp' => '081234567890',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'Rasya Zicko',
                'email' => 'rasya@gmail.com',
                'password' => Hash::make('Rasya1809'),
                'role' => 'peminjam',
                'no_hp' => '084567890123',
                'alamat' => 'Munjul, Bandung',
            ],
            [
                'name' => 'petugas',
                'email' => 'petugas@gmail.com',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
                'no_hp' => '085678901234',
                'alamat' => 'Banjaran, Bandung',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}