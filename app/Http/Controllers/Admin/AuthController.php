<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
   public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // 1️⃣ Coba login sebagai ADMIN dulu
    if (Auth::guard('admin')->attempt($credentials)) {
        return redirect()->route('admin.dashboard');
    }

    // 2️⃣ Kalau bukan admin → coba USER biasa
    if (Auth::guard('web')->attempt($credentials)) {
        return redirect()->route('home');
    }

    // 3️⃣ Kalau dua-duanya gagal
    return back()->withErrors([
        'email' => 'Email atau password salah',
    ]);
}

}
