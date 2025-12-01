<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = DB::table('users')->where('email', 'customer@pakbi.test')->first();
        $product = DB::table('products')->first();

        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_phone' => '081234567890',
            'note' => 'Ambil di warung',
            'total_price' => $product->price,
            'payment_method' => 'COD',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $product->id,
            'quantity' => 1,
            'subtotal' => $product->price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('payments')->insert([
            'order_id' => $orderId,
            'type' => 'COD',
            'amount' => $product->price,
            'status' => 'pending',
            'transaction_ref' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('transaction_logs')->insert([
            'order_id' => $orderId,
            'action' => 'created',
            'note' => 'Order dibuat oleh pelanggan contoh',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
