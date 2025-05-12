<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $role = $request->input('role');

        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/akun/{$id}?updateMask.fieldPaths=nama_lengkap&updateMask.fieldPaths=role";

        $payload = [
            'fields' => [
                'nama_lengkap' => ['stringValue' => $nama],
                'role' => [
                    'arrayValue' => [
                        'values' => [
                            ['stringValue' => $role]
                        ]
                    ]
                ]
            ]
        ];

        $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
            ->patch($url, $payload);

        return response()->json(['success' => $response->successful()]);
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

        $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
            ->patch($url, $payload);

        return response()->json(['success' => $response->successful()]);
    }

    // Fungsi untuk menambah data admin baru
    public function store(Request $request, FirestoreService $firestore)
    {
        $nama = $request->input('nama');
        $email = $request->input('email');
        $role = $request->input('role');
        $status = $request->input('status');

        // Format data yang akan disimpan
        $data = [
            'nama_lengkap' => $nama,
            'email' => $email,
            'role' => [
                'arrayValue' => [
                    'values' => [
                        ['stringValue' => $role]
                    ]
                ]
            ],
            'status' => $status
        ];

        $response = $firestore->createDocument('akun', $data);

        return response()->json(['success' => $response]);
    }

    // Fungsi untuk menghapus data admin berdasarkan ID
    public function delete(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/akun/{$id}";

        $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
            ->delete($url);

        return response()->json(['success' => $response->successful()]);
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
}
