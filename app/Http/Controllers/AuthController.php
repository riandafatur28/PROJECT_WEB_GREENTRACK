<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AuthController extends Controller
{
    private $apiKey = 'AIzaSyCNNPj9B7mBAZu0TucGaSIdjzOMbQWYh_4'; // Firebase Web API Key
    
    public function handleLogin(Request $request, FirestoreService $firestore)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // 1. Autentikasi dengan Firebase Auth
            $response = Http::withHeaders([
                'X-Firebase-Client' => 'greentrack-web-app',
                'Content-Type' => 'application/json'
            ])->post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=" . $this->apiKey, [
                'email' => $request->email,
                'password' => $request->password,
                'returnSecureToken' => true
            ]);
            
            if (!$response->successful()) {
                Log::error('Firebase Auth Login Error:', [
                    'error' => $response->json()['error']['message'] ?? 'Unknown error',
                    'email' => $request->email
                ]);
                return back()->withErrors(['email' => 'Email atau password salah.']);
            }
            
            $authData = $response->json();
            $uid = $authData['localId']; // Firebase UID
            
            // 2. Cek di koleksi akun_superadmin
            $superAdmins = $firestore->getCollection('akun_superadmin');
            $isSuperAdmin = false;
            $matchingUser = null;
            
            // Cari user dengan firebase_uid yang cocok (format baru)
            $matchingUser = collect($superAdmins['documents'] ?? [])->first(function ($doc) use ($uid) {
                if (!isset($doc['fields'])) return false;
                
                $fields = $doc['fields'];
                return isset($fields['firebase_uid']['stringValue']) && 
                       $fields['firebase_uid']['stringValue'] === $uid;
            });
            
            if ($matchingUser) {
                $isSuperAdmin = true;
                $fields = $matchingUser['fields'];
                
                // Debug log untuk super admin
                Log::debug('Super Admin Login Details:', [
                    'email' => $fields['email']['stringValue'],
                    'nama' => $fields['nama_lengkap']['stringValue'] ?? '(Tidak diketahui)',
                    'role' => $fields['role']['stringValue'] ?? 'super admin',
                    'firebase_uid' => $uid,
                    'all_fields' => $fields // Log semua field untuk debugging
                ]);
                
                // Simpan data user ke session
                session([
                    'email' => $fields['email']['stringValue'],
                    'user_nama' => $fields['nama_lengkap']['stringValue'] ?? '(Tidak diketahui)',
                    'role' => $fields['role']['stringValue'] ?? 'super admin',
                    'firebase_uid' => $uid,
                    'firebase_token' => $authData['idToken'] // Menyimpan token untuk API calls
                ]);
                
                // Update last_login di Firestore
                if (isset($matchingUser['name'])) {
                    $documentPath = $matchingUser['name'];
                    $pathParts = explode('/', $documentPath);
                    $documentId = end($pathParts);
                    
                    // Ambil data existing fields
                    $existingFields = $matchingUser['fields'];
                    
                    // Update hanya last_login dan last_login_ip
                    $existingFields['last_login'] = [
                        'timestampValue' => Carbon::now()->toRfc3339String()
                    ];
                    $existingFields['last_login_ip'] = [
                        'stringValue' => $request->ip()
                    ];
                    
                    $updateData = [
                        'fields' => $existingFields
                    ];
                    
                    // Format dokumen untuk update
                    $firestore->updateDocument('akun_superadmin', $documentId, $updateData);
                }
                
                return redirect('/dashboard');
            }
            
            // 3. Jika bukan super admin, cek di koleksi akun (admin biasa)
            $adminUsers = $firestore->getCollection('akun');
            $matchingAdmin = collect($adminUsers['documents'] ?? [])->first(function ($doc) use ($uid) {
                if (!isset($doc['fields'])) return false;
                
                $fields = $doc['fields'];
                return isset($fields['firebase_uid']['stringValue']) && 
                       $fields['firebase_uid']['stringValue'] === $uid;
            });
            
            if (!$matchingAdmin) {
                Log::warning('User authenticated via Firebase but not found in Firestore', [
                    'email' => $request->email,
                    'uid' => $uid
                ]);
                return back()->withErrors(['email' => 'Akun tidak ditemukan di database.']);
            }
            
            // Admin biasa ditemukan
            $fields = $matchingAdmin['fields'];
            
            // Debug log untuk data mentah
            Log::debug('Raw Admin Fields:', [
                'all_fields' => $fields
            ]);
            
            // Cek role dengan benar
            $role = null;
            if (isset($fields['role']['stringValue'])) {
                $role = $fields['role']['stringValue'];
            } elseif (isset($fields['role']['arrayValue']['values'])) {
                $roleArray = $fields['role']['arrayValue']['values'];
                $role = !empty($roleArray) ? $roleArray[0]['stringValue'] : null;
            }
            
            // Validasi role yang diizinkan
            if (!in_array($role, ['admin_penyemaian', 'admin_tpk'])) {
                Log::warning('Invalid admin role:', [
                    'email' => $request->email,
                    'role' => $role
                ]);
                return back()->withErrors(['email' => 'Anda tidak memiliki akses yang sesuai.']);
            }
            
            // Debug log untuk admin
            Log::debug('Admin Login Details:', [
                'email' => $fields['email']['stringValue'],
                'nama' => $fields['nama_lengkap']['stringValue'] ?? '(Tidak diketahui)',
                'role' => $role,
                'firebase_uid' => $uid
            ]);
            
            session([
                'email' => $fields['email']['stringValue'],
                'user_nama' => $fields['nama_lengkap']['stringValue'] ?? '(Tidak diketahui)',
                'role' => $role,
                'firebase_uid' => $uid,
                'firebase_token' => $authData['idToken']
            ]);
            
            // Update last_login untuk admin
            if (isset($matchingAdmin['name'])) {
                $documentPath = $matchingAdmin['name'];
                $pathParts = explode('/', $documentPath);
                $documentId = end($pathParts);
                
                // Ambil data existing fields
                $existingFields = $matchingAdmin['fields'];
                
                // Update hanya last_login dan last_login_ip
                $existingFields['last_login'] = [
                    'timestampValue' => Carbon::now()->toRfc3339String()
                ];
                $existingFields['last_login_ip'] = [
                    'stringValue' => $request->ip()
                ];
                
                $updateData = [
                    'fields' => $existingFields
                ];
                
                // Format dokumen untuk update
                $firestore->updateDocument('akun', $documentId, $updateData);
            }
            
            return redirect('/dashboard');
        }
        catch (\Exception $e) {
            Log::error('Login Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengirim email untuk reset password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Kirim permintaan reset password ke Firebase Auth
            $response = Http::withHeaders([
                'X-Firebase-Client' => 'greentrack-web-app',
                'Content-Type' => 'application/json'
            ])->post("https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key=" . $this->apiKey, [
                'email' => $request->email,
                'requestType' => 'PASSWORD_RESET',
            ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['error']['message'] ?? 'Unknown error';
                Log::error('Firebase Auth Forgot Password Error:', [
                    'error' => $errorMessage,
                    'email' => $request->email
                ]);
                
                if ($errorMessage === 'EMAIL_NOT_FOUND') {
                    return back()->withErrors(['email' => 'Email tidak terdaftar dalam sistem.']);
                }
                
                return back()->withErrors(['email' => 'Gagal mengirim email reset password: ' . $errorMessage]);
            }
            
            // Email telah terkirim - redirect ke halaman konfirmasi
            return redirect()->route('resendotp')->with('success', 'Tautan reset password telah dikirim ke email Anda.');
            
        } catch (\Exception $e) {
            Log::error('Forgot Password Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Menangani reset password setelah menerima kode OTP
     */
    public function handlePasswordReset(Request $request)
    {
        $request->validate([
            'oobCode' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            // Verifikasi kode OOB dan lakukan reset password
            $response = Http::withHeaders([
                'X-Firebase-Client' => 'greentrack-web-app',
                'Content-Type' => 'application/json'
            ])->post("https://identitytoolkit.googleapis.com/v1/accounts:resetPassword?key=" . $this->apiKey, [
                'oobCode' => $request->oobCode,
                'newPassword' => $request->password,
            ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['error']['message'] ?? 'Unknown error';
                Log::error('Firebase Auth Reset Password Error:', [
                    'error' => $errorMessage
                ]);
                return back()->withErrors(['oobCode' => 'Gagal reset password: ' . $errorMessage]);
            }
            
            // Password telah berhasil direset
            return redirect()->route('login')->with('success', 'Password Anda telah berhasil diperbarui. Silakan login dengan password baru.');
            
        } catch (\Exception $e) {
            Log::error('Reset Password Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['oobCode' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
