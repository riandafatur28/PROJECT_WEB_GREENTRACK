<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;

class AuthController extends Controller
{
    public function handleLogin(Request $request, FirestoreService $firestore)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Ambil semua dokumen dari koleksi akun_superadmin
        $users = $firestore->getCollection('akun_superadmin');

        // Cari user dengan email & password yang cocok
        $matchingUser = collect($users['documents'] ?? [])->first(function ($doc) use ($request) {
            $fields = $doc['fields'] ?? [];

            return isset($fields['email']['stringValue'], $fields['password']['stringValue']) &&
                $fields['email']['stringValue'] === $request->email &&
                $fields['password']['stringValue'] === $request->password; // plain text cocok
        });

        if (!$matchingUser) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        // Ambil data user
        $fields = $matchingUser['fields'];

        // Simpan data user ke session
        session([
            'user_email' => $fields['email']['stringValue'],
            'user_nama' => $fields['nama_lengkap']['stringValue'] ?? '(Tidak diketahui)',
            'role' => $fields['role']['stringValue'] ?? 'super admin',
        ]);

        return redirect('/dashboard');
    }
}
