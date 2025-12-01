<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua produk dari database
        $products = Product::with('category')->get();

        // Kirim ke view home.blade.php
        return view('home', compact('products'));
    }
}
