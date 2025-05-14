<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\View;

class HistoryBarcodeController extends Controller
{
    public function index(Request $request, FirestoreService $firestore)
    {
        $search = $request->input('search', '');
        $searchType = $request->input('search_type', 'description'); // Ambil jenis pencarian
        $page = $request->input('page', 1); // Halaman yang diminta
        $perPage = 10; // Jumlah data per halaman

        // Siapkan filter jika ada pencarian
        $filter = null;
        if (!empty($search)) {
            if ($searchType == 'description') {
                $filter = [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'description'],
                        'op' => 'ARRAY_CONTAINS', // Pastikan 'description' itu array
                        'value' => ['stringValue' => $search]
                    ]
                ];
            } elseif ($searchType == 'jenis_aktivitas') {
                $filter = [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'activityType'], // Field activityType
                        'op' => 'ARRAY_CONTAINS',
                        'value' => ['stringValue' => $search]
                    ]
                ];
            }
        }

        // Menyimpan pageToken
        $pageToken = null;
        if ($page > 1) {
            $pageToken = $request->session()->get('nextPageToken');
        }

        // Ambil data dari Firestore dengan pagination
        $response = $firestore->getCollection('activities', [
            'filter' => $filter,
            'pageSize' => $perPage, // Batasi jumlah data per halaman
            'pageToken' => $pageToken,  // Kirimkan pageToken ke Firestore
        ]);

        $activities = [];
        $dataForSorting = [];
        $total = 0;
        $nextPageToken = null; // Menyimpan token halaman berikutnya

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Ambil waktu untuk pengurutan
                $timestamp = $this->getTimestampValue($fields);

                // Ambil jenis aktivitas dari activityType
                $activityType = isset($fields['activityType']['stringValue'])
                    ? $fields['activityType']['stringValue']
                    : '-';

                // Ambil detail dari description
                $description = isset($fields['description']['stringValue'])
                    ? $fields['description']['stringValue']
                    : 'Tidak ada deskripsi';

                // Parse userRole untuk menampilkan yang lebih user-friendly
                $userRole = isset($fields['userRole']['stringValue'])
                    ? $this->parseRole($fields['userRole']['stringValue'])
                    : 'Tidak ada role';

                $dataForSorting[] = [
                    'id' => $id,
                    'nama' => $fields['userName']['stringValue'] ?? '-',
                    'userRole' => $userRole,
                    'keterangan' => $activityType,
                    'detail' => $description,
                    'waktu' => $this->formatDate($timestamp),
                    'timestamp' => $timestamp // untuk pengurutan
                ];

                $total++;  // Hitung total data yang diambil
            }

            // Urutkan berdasarkan timestamp terbaru
            usort($dataForSorting, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

            // Ambil data yang sudah diurutkan
            foreach ($dataForSorting as $item) {
                unset($item['timestamp']);
                $activities[] = $item;
            }

            // Mendapatkan token untuk halaman berikutnya
            $nextPageToken = $response['nextPageToken'] ?? null;
            if ($nextPageToken) {
                // Menyimpan token halaman berikutnya di session
                $request->session()->put('nextPageToken', $nextPageToken);
            }
        }

        // Hitung total halaman berdasarkan total data yang ada
        $totalPages = ceil($total / $perPage);

        return view('layouts.historyscan', [
            'activities' => $activities,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'searchType' => $searchType,
            'nextPageToken' => $nextPageToken,  // Token halaman berikutnya
            'totalPages' => $totalPages,  // Total halaman untuk pagination
        ]);
    }

    // Fungsi untuk memformat tanggal (hanya menampilkan tanggal tanpa jam)
    private function formatDate($timestamp)
    {
        if (!$timestamp) {
            return "Tanggal tidak tersedia";
        }

        return date('d F Y', $timestamp);
    }

    // Fungsi untuk mengambil nilai timestamp dari berbagai format
    private function getTimestampValue($fields)
    {
        // 1. Cek timestamp field (prioritas tertinggi)
        if (isset($fields['timestamp']['timestampValue'])) {
            $timestampStr = $fields['timestamp']['timestampValue'];
            return strtotime($timestampStr);
        }

        // 2. Cek field timestamp yang berformat string
        if (isset($fields['timestamp']['stringValue'])) {
            $timestampStr = $fields['timestamp']['stringValue'];
            return strtotime($timestampStr);
        }

        // 3. Cek createdAt field
        if (isset($fields['createdAt']['timestampValue'])) {
            $timestampStr = $fields['createdAt']['timestampValue'];
            return strtotime($timestampStr);
        }

        // 4. Cek tanggal field
        if (isset($fields['tanggal']['stringValue'])) {
            $dateStr = $fields['tanggal']['stringValue'];
            return strtotime($dateStr);
        }

        // 5. Default: gunakan waktu saat ini
        return time();
    }

    // Parse role menjadi format yang lebih user-friendly
    private function parseRole($roleString)
    {
        // Hapus prefix "UserRole." jika ada
        $roleString = str_replace('UserRole.', '', $roleString);

        // Convert camelCase atau snake_case menjadi kata normal dengan spasi
        $roleString = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $roleString); // camelCase to spaces
        $roleString = str_replace('_', ' ', $roleString); // snake_case to spaces

        // Uppercase words
        return ucwords($roleString);
    }
}
