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
        // Ambil query pencarian dan urutan dari input
        $search = $request->input('search', '');
        $sortOrder = $request->input('sort', 'terbaru'); // Default ke 'terbaru' jika tidak ada
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

                // Format peran admin
                $peranAdmin = $this->formatRole($fields['role']['arrayValue']['values'] ?? []);

                // Ambil photo_url dari Firestore
                $photoUrl = $fields['photo_url']['stringValue'] ?? 'https://randomuser.me/api/portraits/lego/2.jpg';

                // Filter berdasarkan nama, email, dan peran admin
                if ($search) {
                    $searchLower = strtolower($search);
                    $namaLower = strtolower($nama);
                    $emailLower = strtolower($email);
                    $peranLower = strtolower($peranAdmin);

                    // Skip jika tidak ada kata pencarian yang cocok di nama, email, ATAU peran
                    if (stripos($namaLower, $searchLower) === false &&
                        stripos($emailLower, $searchLower) === false &&
                        stripos($peranLower, $searchLower) === false) {
                        continue;
                    }
                }

                // Ekstrak timestamp untuk sorting
                $timestamp = 0;
                if (isset($fields['created_at']['timestampValue'])) {
                    $timestamp = strtotime($fields['created_at']['timestampValue']);
                }

                // Menambahkan data admin ke array
                $admins[] = [
                    'id' => $id,
                    'nama' => $nama,
                    'email' => $email,
                    'peran_admin' => $peranAdmin,
                    'status' => $fields['status']['stringValue'] ?? 'Aktif',
                    'photo_url' => $photoUrl, // Tambahkan photo_url ke data
                    'created_at' => isset($fields['created_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['created_at']['timestampValue']) : '-',
                    'updated_at' => isset($fields['updated_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['updated_at']['timestampValue']) : '-',
                    'timestamp' => $timestamp, // Store timestamp for sorting
                ];
            }

            // Hitung total setelah filter
            $total = count($admins);
        }

        // Sort the admins based on the sort order ('terbaru' or 'terlama')
        if ($sortOrder === 'terbaru') {
            usort($admins, function ($a, $b) {
                return $b['timestamp'] - $a['timestamp']; // Urutkan berdasarkan timestamp terbaru
            });
        } else if ($sortOrder === 'terlama') {
            usort($admins, function ($a, $b) {
                return $a['timestamp'] - $b['timestamp']; // Urutkan berdasarkan timestamp terlama
            });
        }

        // Pagination manual (slice array)
        $offset = ($page - 1) * $perPage;
        $paginatedAdmins = array_slice($admins, $offset, $perPage);

        // Calculate total pages based on total data
        $totalPages = ceil($total / $perPage);

        // Ensure page is within bounds
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));

        // Return view with data
        return view('layouts.manajemenpengguna', [
            'admin' => $paginatedAdmins,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages, // Pass totalPages to the view
            'sortOrder' => $sortOrder, // Pass sortOrder to the view
            'search' => $search // Pass search to the view
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
    public function store(Request $request)
    {
        try {
            // Redirect the request to the registerAdmin endpoint
            $response = app(FirestoreController::class)->registerAdmin($request, app(FirestoreService::class));
            
            // Return the response directly
            return $response;
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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
