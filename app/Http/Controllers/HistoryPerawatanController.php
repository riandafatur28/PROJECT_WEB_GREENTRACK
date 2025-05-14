<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\View;

class HistoryPerawatanController extends Controller
{
    public function index(Request $request, FirestoreService $firestore)
    {
        $search = $request->input('search', '');
        $searchType = $request->input('search_type', 'description'); // Ambil jenis pencarian
        $page = $request->input('page', 1);
        $perPage = 10;

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
                        'field' => ['fieldPath' => 'keterangan'], // Ganti dengan field yang relevan
                        'op' => 'ARRAY_CONTAINS',
                        'value' => ['stringValue' => $search]
                    ]
                ];
            }
        }

        // Ambil data dari Firestore
        $response = $firestore->getCollection('jadwal_perawatan', [
            'filter' => $filter,
            'pageSize' => $perPage,
            'pageToken' => $page > 1 ? $page : null
        ]);

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
            'searchType' => $searchType
        ]);
    }

    // Fungsi tambahan untuk format role (asumsi sudah ada)
    private function formatRole(array $roles): string
    {
        $roleNames = [];
        foreach ($roles as $role) {
            $roleNames[] = $role['stringValue'] ?? '';
        }
        return implode(', ', $roleNames);
    }

    // Fungsi untuk menampilkan waktu relatif (misal: 1 jam yang lalu)
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

        // Jika perbedaan negatif (tanggal di masa depan), kembalikan format tanggal biasa
        if ($diff < 0) {
            return date('d F Y', $timestamp);
        }

        // Konversi ke menit
        $diffMinutes = floor($diff / 60);

        // Kurang dari 1 menit
        if ($diffMinutes < 1) {
            return "Baru saja";
        }

        // Kurang dari 1 jam
        if ($diffMinutes < 60) {
            return $diffMinutes . " menit yang lalu";
        }

        // Konversi ke jam
        $diffHours = floor($diffMinutes / 60);

        // Kurang dari 24 jam
        if ($diffHours < 24) {
            return $diffHours . " jam yang lalu";
        }

        // Konversi ke hari
        $diffDays = floor($diffHours / 24);

        // Kurang dari 7 hari
        if ($diffDays < 7) {
            return $diffDays . " hari yang lalu";
        }

        // Konversi ke minggu
        $diffWeeks = floor($diffDays / 7);

        // Kurang dari 4 minggu
        if ($diffWeeks < 4) {
            return $diffWeeks . " minggu yang lalu";
        }

        // Konversi ke bulan
        $diffMonths = floor($diffDays / 30);

        // Kurang dari 12 bulan
        if ($diffMonths < 12) {
            return $diffMonths . " bulan yang lalu";
        }

        // Konversi ke tahun
        $diffYears = floor($diffDays / 365);
        return $diffYears . " tahun yang lalu";
    }

    // Fungsi untuk mengambil nilai timestamp dari berbagai format untuk digunakan di waktu relatif
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

        // 4. Cek created_at field
        if (isset($fields['created_at']['timestampValue'])) {
            $timestampStr = $fields['created_at']['timestampValue'];
            return strtotime($timestampStr);
        }

        // 5. Cek tanggal field
        if (isset($fields['tanggal']['stringValue'])) {
            $dateStr = $fields['tanggal']['stringValue'];
            return strtotime($dateStr);
        }

        // 6. Cek format timestampValue lainnya
        if (isset($fields['tanggal']['timestampValue'])) {
            $timestampStr = $fields['tanggal']['timestampValue'];
            return strtotime($timestampStr);
        }

        // 7. Cek format integerValue
        if (isset($fields['tanggal']['integerValue'])) {
            return (int)$fields['tanggal']['integerValue'];
        }

        if (isset($fields['created_at']['integerValue'])) {
            return (int)$fields['created_at']['integerValue'];
        }

        // 8. Cek format tanggal di field metadata lainnya
        $dateFields = ['waktu', 'date', 'tanggal_perawatan'];
        foreach ($dateFields as $field) {
            if (isset($fields[$field]['timestampValue'])) {
                $timestampStr = $fields[$field]['timestampValue'];
                return strtotime($timestampStr);
            }

            if (isset($fields[$field]['stringValue'])) {
                $dateStr = $fields[$field]['stringValue'];
                return strtotime($dateStr);
            }

            if (isset($fields[$field]['integerValue'])) {
                return (int)$fields[$field]['integerValue'];
            }
        }

        // 9. Default: gunakan waktu saat ini
        return time();
    }
}
