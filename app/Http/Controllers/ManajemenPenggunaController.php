<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class ManajemenPenggunaController extends Controller
{
    // Fungsi untuk menampilkan daftar admin dengan pagination dan pencarian
    public function index(Request $request, FirestoreService $firestore)
    {
        // Ambil query pencarian dari input
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = 10; // Tentukan jumlah data per halaman

        // Ambil semua data akun dari koleksi "akun"
        $response = $firestore->getCollection('akun');

        $admins = [];
        $total = 0;

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']); // Ambil ID dokumen dari URL

                $nama = $fields['nama_lengkap']['stringValue'] ?? '-';
                $email = $fields['email']['stringValue'] ?? '-';

                // Ambil photo_url dari Firestore
                $photoUrl = $fields['photo_url']['stringValue'] ?? 'https://randomuser.me/api/portraits/lego/2.jpg';

                // Filter manual pakai PHP (case-insensitive)
                if ($search && stripos($nama, $search) === false) {
                    continue; // Skip jika nama tidak mengandung kata pencarian
                }

                $admins[] = [
                    'id' => $id,
                    'nama' => $nama,
                    'email' => $email,
                    'peran_admin' => $this->formatRole($fields['role']['arrayValue']['values'] ?? []),
                    'status' => $fields['status']['stringValue'] ?? 'Aktif',
                    'photo_url' => $photoUrl, // Tambahkan photo_url ke data
                    'created_at' => isset($fields['created_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['created_at']['timestampValue']) : '-',
                    'updated_at' => isset($fields['updated_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['updated_at']['timestampValue']) : '-',
                ];
            }

            // Hitung total setelah filter
            $total = count($admins);
        }

        // Pagination manual (slice array)
        $offset = ($page - 1) * $perPage;
        $paginatedAdmins = array_slice($admins, $offset, $perPage);

        // Return view dengan data
        return view('layouts.manajemenpengguna', [
            'admin' => $paginatedAdmins,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage
        ]);
    }

    // Fungsi untuk mengupdate data admin
    public function updateAdmin(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $nama = $request->input('nama');
        $peran = $request->input('peran');
        $photoUrl = $request->input('photo_url'); // Tambahan untuk menerima photo_url

        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/akun/{$id}?updateMask.fieldPaths=nama_lengkap&updateMask.fieldPaths=role" . ($photoUrl ? "&updateMask.fieldPaths=photo_url" : "");

        $payload = [
            'fields' => [
                'nama_lengkap' => ['stringValue' => $nama],
                'role' => [
                    'arrayValue' => [
                        'values' => [
                            ['stringValue' => $peran]
                        ]
                    ]
                ]
            ]
        ];

        // Tambahkan photo_url ke payload jika ada
        if ($photoUrl) {
            $payload['fields']['photo_url'] = ['stringValue' => $photoUrl];
        }

        $response = Http::withToken($firestore->getAccessToken())
            ->patch($url, $payload);

        return response()->json(['success' => $response->successful(), 'message' => 'Admin berhasil diperbarui']);
    }

    // Fungsi untuk mengupdate status admin
    public function updateStatus(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/akun/{$id}?updateMask.fieldPaths=status";
        $payload = [
            'fields' => [
                'status' => ['stringValue' => $status]
            ]
        ];

        $response = Http::withToken($firestore->getAccessToken())
            ->patch($url, $payload);

        return response()->json(['success' => $response->successful()]);
    }

    // Fungsi untuk menambah data admin baru dengan password
    public function store(Request $request, FirestoreService $firestore)
    {
        $nama = $request->input('nama');
        $email = $request->input('email');
        $peran = $request->input('peran');
        $status = $request->input('status');
        $password = $request->input('password'); // Ambil password dari request
        $photoUrl = $request->input('photo_url', 'https://randomuser.me/api/portraits/lego/2.jpg'); // Default image if not provided

        // Hash password untuk keamanan
        $hashedPassword = Hash::make($password);

        // Format data yang akan disimpan
        $fields = [
            'nama_lengkap' => ['stringValue' => $nama],
            'email' => ['stringValue' => $email],
            'role' => [
                'arrayValue' => [
                    'values' => [
                        ['stringValue' => $peran]
                    ]
                ]
            ],
            'status' => ['stringValue' => $status],
            'photo_url' => ['stringValue' => $photoUrl],
            'password' => ['stringValue' => $hashedPassword], // Tambahkan password yang sudah di-hash
            'created_at' => ['timestampValue' => date('c')], // Format ISO 8601
            'updated_at' => ['timestampValue' => date('c')], // Format ISO 8601
            'kode_otp' => ['stringValue' => ''],
            'last_login' => ['nullValue' => null]
        ];

        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/akun";
        $payload = ['fields' => $fields];

        $response = Http::withToken($firestore->getAccessToken())
            ->post($url, $payload);

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->successful() ? 'Admin berhasil ditambahkan' : 'Gagal menambahkan admin'
        ]);
    }

    // Fungsi untuk menghapus data admin berdasarkan ID
    public function delete(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/akun/{$id}";

        $response = Http::withToken($firestore->getAccessToken())
            ->delete($url);

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->successful() ? 'Admin berhasil dihapus' : 'Gagal menghapus admin'
        ]);
    }

    // Fungsi untuk mengupload gambar (jika dibutuhkan)
    public function uploadImage(Request $request)
    {
        // Ini hanya contoh implementasi sederhana
        // Dalam implementasi sebenarnya, Anda akan mengupload ke penyimpanan cloud seperti Firebase Storage

        if ($request->hasFile('image')) {
            // Untuk tujuan demonstrasi, kita hanya mengembalikan URL gambar
            // Di aplikasi nyata, gunakan Firebase Storage atau layanan penyimpanan cloud lainnya
            return response()->json([
                'success' => true,
                'url' => $request->input('temp_url', 'https://randomuser.me/api/portraits/lego/2.jpg')
            ]);
        }

        return response()->json(['success' => false]);
    }

    // Fungsi tambahan untuk format role
    private function formatRole(array $roles): string
    {
        if (empty($roles)) return '-';

        $roleNames = [];
        foreach ($roles as $role) {
            $roleNames[] = $role['stringValue'] ?? '';
        }
        return implode(', ', $roleNames);
    }

    // Fungsi untuk format timestamp
    private function formatTimestamp(string $timestamp): string
    {
        try {
            $date = new \DateTime($timestamp);
            return $date->format('d M Y H:i:s');
        } catch (\Exception $e) {
            return $timestamp;
        }
    }
}
