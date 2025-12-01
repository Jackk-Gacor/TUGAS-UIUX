<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Nasi Goreng', 'description' => 'Berbagai macam nasi goreng khas Pak Bi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mie', 'description' => 'Bakmie goreng dan kuah', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Minuman', 'description' => 'Es teh, es jeruk, dan minuman lainnya', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
