<?php
namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;

class HistoryPerawatanController extends Controller
{
    public function index(Request $request, FirestoreService $firestore)
    {
        // Ambil parameter dari request
        $search = $request->input('search', '');
        $sortOrder = $request->input('sort', 'desc'); // Default: 'desc' untuk Terbaru, 'asc' untuk Terlama
        $page = $request->input('page', 1); // Halaman untuk pagination
        $perPage = 10; // Menampilkan 10 data per halaman

        // Siapkan filter pencarian
        $filter = null;
        if (!empty($search)) {
            // Pencarian di beberapa field: Nama Admin, Jenis Perawatan, Nama Bibit, Waktu, dan Detail
            $filter = [
                'fieldFilter' => [
                    'op' => 'OR', // Menggabungkan beberapa kondisi dengan OR
                    'filters' => [
                        [
                            'fieldFilter' => [
                                'field' => ['fieldPath' => 'created_by_name'], // Nama Admin
                                'op' => '==', // Gunakan kesamaan, bukan ARRAY_CONTAINS
                                'value' => ['stringValue' => $search]
                            ]
                        ],
                        [
                            'fieldFilter' => [
                                'field' => ['fieldPath' => 'jenis_perawatan'], // Jenis Perawatan
                                'op' => '==', // Gunakan kesamaan, bukan ARRAY_CONTAINS
                                'value' => ['stringValue' => $search]
                            ]
                        ],
                        [
                            'fieldFilter' => [
                                'field' => ['fieldPath' => 'nama_bibit'], // Nama Bibit
                                'op' => '==', // Gunakan kesamaan, bukan ARRAY_CONTAINS
                                'value' => ['stringValue' => $search]
                            ]
                        ],
                        [
                            'fieldFilter' => [
                                'field' => ['fieldPath' => 'waktu'], // Waktu
                                'op' => '==', // Gunakan kesamaan, bukan ARRAY_CONTAINS
                                'value' => ['stringValue' => $search]
                            ]
                        ],
                        [
                            'fieldFilter' => [
                                'field' => ['fieldPath' => 'catatan'], // Detail
                                'op' => '==', // Gunakan kesamaan, bukan ARRAY_CONTAINS
                                'value' => ['stringValue' => $search]
                            ]
                        ]
                    ]
                ]
            ];
        }

        // Set urutan berdasarkan 'created_at' field
        $orderBy = [
            'field' => ['fieldPath' => 'created_at'],
            'direction' => $sortOrder == 'desc' ? 'DESCENDING' : 'ASCENDING' // Urutkan berdasarkan 'created_at'
        ];

        // Ambil data dari Firestore
        $response = $firestore->getCollection('jadwal_perawatan', [
            'filter' => $filter,
            'orderBy' => $orderBy,
            'pageSize' => $perPage,
            'pageToken' => $page > 1 ? $this->getPageToken($page) : null
        ]);

        // Proses data yang diambil dan format waktu relatif
        $perawatan = [];
        $total = 0;

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Ambil timestamp untuk format waktu relatif
                $timestamp = $this->getTimestampValue($fields);

                $perawatan[] = [
                    'id' => $id,
                    'nama' => $fields['created_by_name']['stringValue'] ?? '-',
                    'userRole' => isset($fields['role']['arrayValue']['values'])
                                    ? $this->formatRole($fields['role']['arrayValue']['values'])
                                    : 'Tidak ada role',
                    'keterangan' => $fields['jenis_perawatan']['stringValue'] ?? '-',
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? '-',
                    'detail' => $fields['catatan']['stringValue'] ?? '-',
                    'waktu' => $this->getWaktuRelatif($timestamp),
                ];

                $total++;
            }
        }

        return view('layouts.historyperawatan', [
            'perawatan' => $perawatan,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'sortOrder' => $sortOrder // Mengirimkan parameter sort ke view
        ]);
    }

    // Format roles (jika ada)
    private function formatRole(array $roles): string
    {
        $roleNames = [];
        foreach ($roles as $role) {
            $roleNames[] = $role['stringValue'] ?? '';
        }
        return implode(', ', $roleNames);
    }

    // Format waktu relatif
    private function getWaktuRelatif($timestamp)
    {
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        if ($timestamp === false) {
            return "Waktu tidak valid";
        }

        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 0) {
            return date('d F Y', $timestamp);
        }

        $diffMinutes = floor($diff / 60);
        if ($diffMinutes < 1) {
            return "Baru saja";
        }

        if ($diffMinutes < 60) {
            return $diffMinutes . " menit yang lalu";
        }

        $diffHours = floor($diffMinutes / 60);
        if ($diffHours < 24) {
            return $diffHours . " jam yang lalu";
        }

        $diffDays = floor($diffHours / 24);
        if ($diffDays < 7) {
            return $diffDays . " hari yang lalu";
        }

        $diffWeeks = floor($diffDays / 7);
        if ($diffWeeks < 4) {
            return $diffWeeks . " minggu yang lalu";
        }

        $diffMonths = floor($diffDays / 30);
        if ($diffMonths < 12) {
            return $diffMonths . " bulan yang lalu";
        }

        $diffYears = floor($diffDays / 365);
        return $diffYears . " tahun yang lalu";
    }

    // Ambil timestamp dari beberapa format
    private function getTimestampValue($fields)
    {
        if (isset($fields['timestamp']['timestampValue'])) {
            return strtotime($fields['timestamp']['timestampValue']);
        }

        $timestampFields = ['createdAt', 'created_at', 'tanggal', 'waktu', 'date', 'tanggal_perawatan'];
        foreach ($timestampFields as $field) {
            if (isset($fields[$field]['timestampValue'])) {
                return strtotime($fields[$field]['timestampValue']);
            }

            if (isset($fields[$field]['stringValue'])) {
                return strtotime($fields[$field]['stringValue']);
            }
        }

        return time(); // Default waktu jika tidak ada timestamp
    }

    // Fungsi untuk menangani page token untuk pagination
    private function getPageToken($page)
    {
        return 'page_token_' . $page; // Sesuaikan jika Firestore menggunakan pagination dengan token
    }
}
