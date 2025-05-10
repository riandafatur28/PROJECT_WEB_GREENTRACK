<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;

class ManajemenPenggunaController extends Controller
{
    public function updateAdmin(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $nama = $request->input('nama');
        $role = $request->input('role'); // 'admin_penyemaian' atau 'admin_tpk'

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

    public function index(Request $request, FirestoreService $firestore)
    {
        // Ambil query pencarian dari input
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = 10; // Tentukan jumlah data per halaman

        // Ambil semua data dulu
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
                    continue; // Skip kalau nama tidak mengandung kata pencarian
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

    private function formatRole(array $roles): string
    {
        if (empty($roles)) return '-';

        // Ambil role pertama, bisa dimodifikasi kalau ingin gabungan
        $role = $roles[0]['stringValue'] ?? '';
        return match ($role) {
            'admin_penyemaian' => 'Admin Persemaian',
            'admin_tpk' => 'Admin TPK',
            default => ucfirst(str_replace('_', ' ', $role)),
        };
    }
}
