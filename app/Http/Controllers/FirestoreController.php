<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FirestoreController extends Controller
{
    private $apiKey = 'AIzaSyAqFuhrg16_t6qeY-0YgqLf_LSgWBPOIzA'; // Your Firebase Web API Key
    
    public function index(FirestoreService $firestore)
    {
        $documents = $firestore->getCollection('users');
        return response()->json($documents);
    }

    public function store(Request $request, FirestoreService $firestore)
    {
        $data = $request->only(['name', 'email']);
        $result = $firestore->createDocument('users', $data);
        return response()->json($result);
    }

    public function showForm()
    {
        return view('register');
    }

    public function handleForm(Request $request, FirestoreService $firestore)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $firestore->createDocument('users', $request->only('name', 'email'));

        return 'Registrasi berhasil!';
    }

    public function showSuperAdminForm()
    {
        return view('superadmin_register');
    }

    public function storeSuperAdmin(Request $request, FirestoreService $firestore)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $data = [
            'email' => $request->email,
            'nama_lengkap' => $request->nama_lengkap,
            'password' => $request->password,
            'kode_otp' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
            'role' => 'super admin',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'last_login' => null,
            'last_login_ip' => $request->ip(),
        ];

        $firestore->createDocument('akun_superadmin', $data);

        return 'Super admin berhasil terdaftar!';
    }

    public function registerAdmin(Request $request, FirestoreService $firestore)
    {
        try {
            // 1. Validate the request
            $request->validate([
                'nama_lengkap' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'role' => 'required|in:admin_penyemaian,admin_tpk',
            ]);

            // 2. Create user in Firebase Auth
            $response = Http::withHeaders([
                'X-Firebase-Client' => 'greentrack-web-app',
                'Content-Type' => 'application/json'
            ])->post("https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=" . $this->apiKey, [
                'email' => $request->email,
                'password' => $request->password,
                'returnSecureToken' => true
            ]);

            if (!$response->successful()) {
                $errorMessage = $response->json()['error']['message'] ?? 'Unknown error';
                Log::error('Firebase Auth Error:', [
                    'error' => $errorMessage,
                    'response' => $response->json()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat akun: ' . $errorMessage
                ], 400);
            }

            $userData = $response->json();
            $uid = $userData['localId']; // Firebase UID

            // 3. Create user document in Firestore
            $now = Carbon::now();
            $adminData = [
                'fields' => [
                    'email' => [
                        'stringValue' => $request->email
                    ],
                    'nama_lengkap' => [
                        'stringValue' => $request->nama_lengkap
                    ],
                    'role' => [
                        'arrayValue' => [
                            'values' => [
                                ['stringValue' => $request->role]
                            ]
                        ]
                    ],
                    'kode_otp' => [
                        'stringValue' => ''
                    ],
                    'last_login' => [
                        'nullValue' => null
                    ],
                    'status' => [
                        'stringValue' => 'Aktif'
                    ],
                    'created_at' => [
                        'timestampValue' => $now->toRfc3339String()
                    ],
                    'updated_at' => [
                        'timestampValue' => $now->toRfc3339String()
                    ],
                    'dashboard' => [
                        'mapValue' => [
                            'fields' => $request->role === 'admin_penyemaian' ? 
                                [
                                    'total_bibit_dipindai' => ['integerValue' => 0],
                                    'bibit_siap_tanam' => ['integerValue' => 0],
                                    'butuh_perhatian' => ['integerValue' => 0],
                                    'total_bibit' => ['integerValue' => 0],
                                    'last_updated' => ['timestampValue' => $now->toRfc3339String()]
                                ] : 
                                [
                                    'total_batch' => ['integerValue' => 0],
                                    'total_kayu' => ['integerValue' => 0],
                                    'total_kayu_dipindai' => ['integerValue' => 0],
                                    'last_updated' => ['timestampValue' => $now->toRfc3339String()]
                                ]
                        ]
                    ]
                ]
            ];

            // Create document with custom ID (Firebase UID)
            $result = $firestore->createDocumentWithId('akun', $uid, $adminData);

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil didaftarkan',
                'data' => [
                    'uid' => $uid,
                    'email' => $request->email,
                    'nama_lengkap' => $request->nama_lengkap,
                    'role' => $request->role
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Admin Registration Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

/*
untuk list bibit
sub colection bibit/id_bibit/document 
untuk list kayu
sub collection kayu/id kayu/document_kayu  

untuk pengguna:
sub collection akun/ id account/ document

untuk activities
sub colelction activites/id_activities/document

*/