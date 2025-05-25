<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(FirestoreService $firestore, Request $request)
    {
        // Set Carbon locale to Bahasa Indonesia
        Carbon::setLocale('id');

        // Handle filtering based on dates
        $filter = $request->input('filter', 'all');
        $dateFilter = $this->getDateFilter($filter);

        // Ambil data bibit dari Firestore
        $bibitResponse = $firestore->getCollection('bibit');

        // Initialize array untuk menyimpan jumlah bibit per bulan

$bibitCounts = array_fill(0, 12, 0);

if (isset($bibitResponse['documents'])) {
    foreach ($bibitResponse['documents'] as $document) {
        $fields = $document['fields'] ?? [];
        if (isset($fields['created_at']['timestampValue'])) {
            $timestamp = strtotime($fields['created_at']['timestampValue']);
            $month = (int)date('n', $timestamp) - 1;
        } else {
            // Jika tidak ada created_at, masukkan ke bulan sekarang
            $month = (int)date('n') - 1;
        }
        $bibitCounts[$month]++;
    }
}

        // Get total bibit count
        $totalBibit = isset($bibitResponse['documents']) ? count($bibitResponse['documents']) : 0;

        // Hitung total akun
        $totalAkun = $this->countTotalAkun($firestore);

        // Ambil total admin dari Firestore
        $totalAdmin = $this->countTotalAdmin($firestore);

        // Ambil data aktivitas dari Firestore
        $response = $firestore->getCollection('activities', $dateFilter);

        $activities = [];

        // Get total kayu count and process kayu data
        $kayuResponse = $firestore->getCollection('kayu');
        $totalKayu = 0;
        $kayuData = [
            'tersedia' => 0,
            'terjual' => 0,
            'rusak' => 0
        ];

        if (isset($kayuResponse['documents'])) {
            foreach ($kayuResponse['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $jumlahStok = $fields['jumlah_stok']['integerValue'] ?? 0;
                $totalKayu += $jumlahStok;

                // Assuming status is stored in the document, if not, you might need to adjust this
                $status = $fields['status']['stringValue'] ?? 'tersedia';

                switch (strtolower($status)) {
                    case 'terjual':
                        $kayuData['terjual'] += $jumlahStok;
                        break;
                    case 'rusak':
                        $kayuData['rusak'] += $jumlahStok;
                        break;
                    default:
                        $kayuData['tersedia'] += $jumlahStok;
                        break;
                }
            }
        }

        // Process activities data
        if (isset($response['documents'])) {
            // Sort the documents by date (newest first)
            usort($response['documents'], function($a, $b) {
                $dateA = $a['fields']['timestamp']['timestampValue'] ?? '';
                $dateB = $b['fields']['timestamp']['timestampValue'] ?? '';
                return strcmp($dateB, $dateA); // Descending order
            });

            // Take only the first 10 documents
            $documents = array_slice($response['documents'], 0, 10);

            foreach ($documents as $document) {
                $fields = $document['fields'] ?? [];
                Log::info('Isi fields:', $fields);
                $id = basename($document['name']);

                // Get userRole from document and format it
                $roles = isset($fields['userRole']['stringValue']) ? $this->formatRole($fields['userRole']['stringValue']) : 'Tidak ada role';

                // Get the timestamp and format the date
                $timestamp = $this->getTimestampValue($fields);
                $carbonDate = Carbon::createFromTimestamp($timestamp);

                // Cek userPhotoUrl di fields utama, jika tidak ada cek di metadata->fields
                if (
                    isset($fields['userPhotoUrl']['stringValue']) &&
                    !empty($fields['userPhotoUrl']['stringValue'])
                ) {
                    $userPhotoUrl = $fields['userPhotoUrl']['stringValue'];
                } elseif (
                    isset($fields['metadata']['mapValue']['fields']['userPhotoUrl']['stringValue']) &&
                    !empty($fields['metadata']['mapValue']['fields']['userPhotoUrl']['stringValue'])
                ) {
                    $userPhotoUrl = $fields['metadata']['mapValue']['fields']['userPhotoUrl']['stringValue'];
                } else {
                    $userPhotoUrl = 'https://via.placeholder.com/150'; // Default image if not available
                }
                Log::info('userPhoto:', ['userPhotoUrl' => $userPhotoUrl]);

                // Cek userName di fields utama, jika tidak ada cek di metadata->fields
                if (
                    isset($fields['userName']['stringValue']) &&
                    !empty($fields['userName']['stringValue'])
                ) {
                    $userName = $fields['userName']['stringValue'];
                } elseif (
                    isset($fields['metadata']['mapValue']['fields']['userName']['stringValue']) &&
                    !empty($fields['metadata']['mapValue']['fields']['userName']['stringValue'])
                ) {
                    $userName = $fields['metadata']['mapValue']['fields']['userName']['stringValue'];
                } else {
                    $userName = '-';
                }

                $activities[] = [
                    'id' => $id,
                    'nama' => $userName,
                    'userRole' => $roles,
                    'keterangan' => $fields['description']['stringValue'] ?? '-',
                    'detail' => $fields['description']['stringValue'] ?? '-',
                    'waktu' => $carbonDate->diffForHumans(), // Format waktu relatif
                    'image' => $userPhotoUrl,  // Use the user's photo URL from Firestore
                ];
            }
        }

        return view('layouts.dashboard', [
            'activities' => $activities,
            'totalActivities' => count($response['documents'] ?? []),
            'totalBibit' => $totalBibit,
            'totalKayu' => $totalKayu,
            'totalAkun' => $totalAkun,
            'totalAdmin' => $totalAdmin,
            'filter' => $filter,
            'bibitCounts' => array_values($bibitCounts), // Make sure we send the counts as array values
            'kayuData' => $kayuData
        ]);
    }

    // Count total admin from the 'akun' collection
    private function countTotalAdmin(FirestoreService $firestore)
    {
        try {
            $akunResponse = $firestore->getCollection('akun');
            $totalAdmin = 0;

            if (isset($akunResponse['documents'])) {
                foreach ($akunResponse['documents'] as $document) {
                    $fields = $document['fields'] ?? [];

                    // Check if the user has admin role (either admin_penyemaian or admin_tpk)
                    if (isset($fields['role']['arrayValue']['values'])) {
                        $roles = $fields['role']['arrayValue']['values'];
                        $isAdmin = false;
                        foreach ($roles as $role) {
                            $roleValue = $role['stringValue'] ?? '';
                            if (strpos($roleValue, 'admin_') === 0) {
                                $isAdmin = true;
                                break;
                            }
                        }

                        // Only count if they are admin and their status is active
                        if ($isAdmin && (!isset($fields['status']['stringValue']) || $fields['status']['stringValue'] === 'Aktif')) {
                        $totalAdmin++;
                        }
                    }
                }
            }

            return $totalAdmin;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Count total accounts from the 'akun' collection
    private function countTotalAkun(FirestoreService $firestore)
    {
        try {
            $akunResponse = $firestore->getCollection('akun');
            return count($akunResponse['documents'] ?? []);
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Get the date filter based on selected filter
    private function getDateFilter($filter)
    {
        $dateFilter = [];
        switch ($filter) {
            case '30_days':
                $dateFilter = ['start' => Carbon::now()->subDays(30)->toDateString(), 'end' => Carbon::now()->toDateString()];
                break;
            case '7_days':
                $dateFilter = ['start' => Carbon::now()->subDays(7)->toDateString(), 'end' => Carbon::now()->toDateString()];
                break;
            case 'today':
                $dateFilter = ['start' => Carbon::today()->toDateString(), 'end' => Carbon::today()->toDateString()];
                break;
            default:
                break;
        }
        return $dateFilter;
    }

    // Format the timestamp
    private function getTimestampValue($fields)
    {
        if (isset($fields['timestamp']['timestampValue'])) {
            return strtotime($fields['timestamp']['timestampValue']);
        }
        if (isset($fields['createdAt']['timestampValue'])) {
            return strtotime($fields['createdAt']['timestampValue']);
        }
        return time(); // Default to current time if timestamp is not found
    }

    // Format roles into a string
    private function formatRole($roleString): string
    {
        $roleString = str_replace('UserRole.', '', $roleString); // Remove "UserRole."
        $roleString = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $roleString); // camelCase to spaces
        return ucwords($roleString); // Capitalize the first letter of each word
    }
}
