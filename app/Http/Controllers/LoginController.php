<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Memeriksa kredensial
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Jika login berhasil, arahkan ke halaman yang sebelumnya diminta
            return redirect()->intended(route('dashboard')); // Atau arahkan ke dashboard atau halaman yang sudah diinginkan
        } else {
            // Jika login gagal
            return back()->with('error', 'Email atau password tidak valid!');
        }
    }
}
