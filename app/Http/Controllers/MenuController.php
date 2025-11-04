<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = [
            [
                'name' => 'Nasi Goreng',
                'description' => 'Nasi goreng adalah salah satu makanan khas indonesia yang sangat populer dan digemari oleh berbagai kalangan. Hidangan ini terbuat dari nasi putih yang digoreng bersama bumbu-bumbu dasar seperti bawang merah, bawang putih, kecap manis, garam, dan sering ditambah cabai untuk rasa pedas.',
                'price' => 'Rp 12.000',
                'image' => 'images/nasi-goreng.png',
                'variant' => 'Varian Nasi',
            ],
            [
                'name' => 'Bakmie Goreng',
                'description' => 'Bakmie Goreng adalah hidangan mi yang lezat dengan bumbu khas.',
                'price' => 'Rp 12.000',
                'image' => 'images/bakmie-goreng.png',
                'variant' => 'Varian Mie',
            ],
            [
                'name' => 'Capjay Kuah',
                'description' => 'Capjay Kuah adalah sup sayuran dengan berbagai isian.',
                'price' => 'Rp 10.000',
                'image' => 'images/capjay-kuah.png',
                'variant' => 'Varian Sayuran',
            ],
            [
                'name' => 'Mie Pangsit',
                'description' => 'Mie Pangsit adalah hidangan mie dengan pangsit renyah.',
                'price' => 'Rp 10.000',
                'image' => 'images/mie-pangsit.png',
                'variant' => 'Varian Mie',
            ],
            [
                'name' => 'Mie Ayam',
                'description' => 'Mie Ayam adalah hidangan mie dengan potongan ayam lezat.',
                'price' => 'Rp 10.000',
                'image' => 'images/mie-ayam.png',
                'variant' => 'Varian Mie',
            ],
        ];

        return view('menu', compact('menus'));
    }
}