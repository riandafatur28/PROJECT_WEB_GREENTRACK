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

                $perawatan[] = [
                    'id' => $id,
                    'nama' => $fields['created_by_name']['stringValue'] ?? '-',
                    'userRole' => isset($fields['role']['arrayValue']['values'])
                                    ? $this->formatRole($fields['role']['arrayValue']['values'])
                                    : 'Tidak ada role',
                    'keterangan' => $fields['jenis_perawatan']['stringValue'] ?? '-',
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? '-',
                    'detail' => $fields['catatan']['stringValue'] ?? '-',
                    'waktu' => $this->getTanggalDariFields($fields),
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

    // Fungsi sederhana untuk mendapatkan tanggal dari berbagai kemungkinan field
    private function getTanggalDariFields($fields)
    {
        // Cek berbagai format dan field tanggal yang mungkin ada di Firestore

        // 1. Cek format timestampValue (format khusus Firestore)
        if (isset($fields['tanggal']['timestampValue'])) {
            return $this->konversiTanggal($fields['tanggal']['timestampValue']);
        }

        if (isset($fields['created_at']['timestampValue'])) {
            return $this->konversiTanggal($fields['created_at']['timestampValue']);
        }

        // 2. Cek format stringValue
        if (isset($fields['tanggal']['stringValue'])) {
            return $this->konversiTanggal($fields['tanggal']['stringValue']);
        }

        if (isset($fields['created_at']['stringValue'])) {
            return $this->konversiTanggal($fields['created_at']['stringValue']);
        }

        // 3. Cek format integerValue (unix timestamp)
        if (isset($fields['tanggal']['integerValue'])) {
            return date('d F Y', (int)$fields['tanggal']['integerValue']);
        }

        if (isset($fields['created_at']['integerValue'])) {
            return date('d F Y', (int)$fields['created_at']['integerValue']);
        }

        // 4. Cek format tanggal di field metadata lainnya
        $dateFields = ['timestamp', 'waktu', 'date', 'tanggal_perawatan'];
        foreach ($dateFields as $field) {
            if (isset($fields[$field]['stringValue'])) {
                $result = $this->konversiTanggal($fields[$field]['stringValue']);
                if ($result) return $result;
            }

            if (isset($fields[$field]['timestampValue'])) {
                $result = $this->konversiTanggal($fields[$field]['timestampValue']);
                if ($result) return $result;
            }

            if (isset($fields[$field]['integerValue'])) {
                return date('d F Y H:i', (int)$fields[$field]['integerValue']);
            }
        }

        // 5. Periksa struktur dokumen secara langsung
        // Dump seluruh struktur fields untuk debug
        ob_start();
        var_dump($fields);
        $dump = ob_get_clean();

        // Return dump data untuk debugging (tampilkan sementara untuk debugging)
        return "Tanggal tidak tersedia";
    }

    // Fungsi untuk mengkonversi tanggal ke format yang diinginkan
    private function konversiTanggal($dateString)
    {
        if (empty($dateString)) {
            return "Format tanggal kosong";
        }

        // Debug: tampilkan format tanggal yang diterima
        $originalDateString = $dateString;

        // 1. Handle format timestamp Firestore (ISO8601 / RFC3339)
        // Format: 2023-05-17T10:30:00Z atau 2023-05-17T10:30:00.123456Z
        if (strpos($dateString, 'T') !== false &&
            (strpos($dateString, 'Z') !== false || strpos($dateString, '+') !== false)) {
            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                return date('d F Y', $timestamp); // Hanya tanggal, tanpa jam
            }
        }

        // 2. Handle format numeric (UNIX timestamp)
        if (is_numeric($dateString)) {
            $timestamp = (int)$dateString;
            // Jika timestamp terlalu besar (dalam milidetik), konversi ke detik
            if ($timestamp > 10000000000) {
                $timestamp = floor($timestamp / 1000);
            }
            return date('d F Y', $timestamp); // Hanya tanggal, tanpa jam
        }

        // 3. Handle format dengan titik (Firestore native timestamp)
        // Format: 1621234567.123456 (seconds.microseconds)
        if (strpos($dateString, '.') !== false) {
            $parts = explode('.', $dateString);
            if (is_numeric($parts[0])) {
                return date('d F Y', (int)$parts[0]); // Hanya tanggal, tanpa jam
            }
        }

        // 4. Handle format tanggal standar dengan berbagai pemisah
        $dateString = trim($dateString);
        // Hapus microseconds jika ada
        $dateString = preg_replace('/\.\d+/', '', $dateString);

        // Coba dengan strtotime (menangani banyak format)
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('d F Y', $timestamp); // Hanya tanggal, tanpa jam
        }

        // 5. Coba berbagai format tanggal yang umum
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'd-m-Y H:i:s',
            'd-m-Y',
            'Y/m/d H:i:s',
            'Y/m/d',
            'd/m/Y H:i:s',
            'd/m/Y'
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('d F Y'); // Hanya tanggal, tanpa jam
            }
        }

        // Debug: Kembalikan string tanggal asli jika semua metode gagal
        return "Format tidak dikenali: " . $originalDateString;
    }
}
