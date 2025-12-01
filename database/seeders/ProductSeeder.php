<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $nasi = DB::table('categories')->where('name', 'Nasi Goreng')->first();
        $mie = DB::table('categories')->where('name', 'Mie')->first();
        $minum = DB::table('categories')->where('name', 'Minuman')->first();

        DB::table('products')->insert([
            [
                'category_id' => $nasi->id,
                'name' => 'Nasi Goreng Original',
                'description' => 'Nasi goreng sederhana dengan rasa khas',
                'price' => 15000,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $nasi->id,
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng spesial dengan telur dan ayam suwir',
                'price' => 22000,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $mie->id,
                'name' => 'Bakmie Goreng',
                'description' => 'Mie goreng ala Pak Bi',
                'price' => 18000,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $minum->id,
                'name' => 'Es Teh Manis',
                'description' => 'Es teh manis segar',
                'price' => 5000,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
