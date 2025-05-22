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
        $sortOrder = $request->input('sort', 'desc'); // 'desc' untuk Terbaru, 'asc' untuk Terlama
        $page = (int) $request->input('page', 1);
        $perPage = 10;

        // Ambil semua data dari Firestore (tanpa filter, filter di PHP)
        $response = $firestore->getCollection('jadwal_perawatan', [
            'orderBy' => [
                'field' => ['fieldPath' => 'created_at'],
                'direction' => $sortOrder == 'desc' ? 'DESCENDING' : 'ASCENDING'
            ],
            // Tidak pakai pageSize agar bisa filter multi-field di PHP
        ]);

        $allPerawatan = [];
        if (isset($response['documents'])) {
            foreach ($response['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);
                $timestamp = $this->getTimestampValue($fields);

                $item = [
                    'id' => $id,
                    'nama' => $fields['created_by_name']['stringValue'] ?? '-',
                    'userRole' => isset($fields['role']['arrayValue']['values'])
                        ? $this->formatRole($fields['role']['arrayValue']['values'])
                        : 'Tidak ada role',
                    'keterangan' => $fields['jenis_perawatan']['stringValue'] ?? '-',
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? '-',
                    'detail' => $fields['catatan']['stringValue'] ?? '-',
                    'waktu' => $this->getWaktuRelatif($timestamp),
                    'timestamp' => $timestamp,
                ];

                // Multi-field search (case-insensitive)
                if ($search) {
                    $searchLower = strtolower($search);
                    $match = false;
                    foreach (['created_by_name', 'jenis_perawatan', 'nama_bibit', 'waktu', 'catatan'] as $field) {
                        $value = strtolower($fields[$field]['stringValue'] ?? '');
                        if (strpos($value, $searchLower) !== false) {
                            $match = true;
                            break;
                        }
                    }
                    if (!$match) continue;
                }

                $allPerawatan[] = $item;
            }
        }

        // Sorting ulang di PHP jika perlu (jaga-jaga)
        usort($allPerawatan, function ($a, $b) use ($sortOrder) {
            if ($sortOrder == 'desc') {
                return $b['timestamp'] <=> $a['timestamp'];
            } else {
                return $a['timestamp'] <=> $b['timestamp'];
            }
        });

        // Pagination manual
        $total = count($allPerawatan);
        $offset = ($page - 1) * $perPage;
        $perawatan = array_slice($allPerawatan, $offset, $perPage);

        return view('layouts.historyperawatan', [
            'perawatan' => $perawatan,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'sortOrder' => $sortOrder
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
}
