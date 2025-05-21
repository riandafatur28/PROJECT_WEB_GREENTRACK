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
        $bibitCounts = [];

        if (isset($bibitResponse['documents'])) {
            foreach ($bibitResponse['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $jumlah = $fields['jumlah']['integerValue'] ?? 0;
                $bibitCounts[] = $jumlah;
            }
        }

        // Hitung total akun
        $totalAkun = $this->countTotalAkun($firestore);

        // Ambil total admin dari Firestore
        $totalAdmin = $this->countTotalAdmin($firestore);

        // Ambil data aktivitas dari Firestore
        $response = $firestore->getCollection('activities', $dateFilter);

        $activities = [];

        // Get total bibit count (no need for ID filtering)
        $totalBibit = count($bibitCounts);

        // Get total kayu count (no need for ID filtering)
        $kayuResponse = $firestore->getCollection('kayu');
        $totalKayu = count($kayuResponse['documents'] ?? []);

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
            'bibitCounts' => $bibitCounts
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
                    $role = $fields['role']['stringValue'] ?? '';
                    if (strtolower($role) === 'admin') {
                        $totalAdmin++;
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
