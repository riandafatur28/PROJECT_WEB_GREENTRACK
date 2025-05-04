<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function doRegister(Request $request, FirebaseService $firebase)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            $user = $firebase->registerUser($request->email, $request->password);

            // Optional: Simpan ke Firestore
            $firebase->getFirestore()
                ->collection('users')
                ->document($user->uid)
                ->set([
                    'email' => $request->email,
                    'created_at' => now(),
                ]);

            return "User berhasil didaftarkan. UID: " . $user->uid;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return "Gagal registrasi: " . $e->getMessage();
        }
    }
}
