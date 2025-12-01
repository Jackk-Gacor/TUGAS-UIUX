<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'name' => 'Admin Pak Bi',
                'email' => 'admin@pakbi.test',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Pelanggan Contoh',
                'email' => 'customer@pakbi.test',
                'password' => Hash::make('secret123'),
            ],
        ] as $u) {
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => $u['password'],
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        }
    }
}
