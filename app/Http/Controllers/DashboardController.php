<?php
namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index(FirestoreService $firestore, Request $request)
{
    // Handle filtering based on dates
    $filter = $request->input('filter', 'all');
    $dateFilter = $this->getDateFilter($filter);

    // Ambil data bibit dari Firestore
    $bibitResponse = $firestore->getCollection('bibit');
    $bibitCounts = [];

    if (isset($bibitResponse['documents'])) {
        // Asumsikan data bibit ada dalam dokumen yang memiliki data yang sesuai
        foreach ($bibitResponse['documents'] as $document) {
            $fields = $document['fields'] ?? [];
            $jumlah = $fields['jumlah']['integerValue'] ?? 0;  // Mengambil jumlah bibit
            $bibitCounts[] = $jumlah;
        }
    }

    // Ambil data aktivitas dari Firestore
    $response = $firestore->getCollection('activities', $dateFilter);

    $activities = [];

    // Get total bibit count (no need for ID filtering)
    $totalBibit = count($bibitCounts);  // Counting total bibit from Firestore data

    // Get total kayu count (no need for ID filtering)
    $kayuResponse = $firestore->getCollection('kayu');
    $totalKayu = count($kayuResponse['documents'] ?? []);  // Counting total kayu

    // Process activities data
    if (isset($response['documents'])) {
        // Sort the documents by date (newest first)
        usort($response['documents'], function($a, $b) {
            $dateA = $a['fields']['tanggal']['stringValue'] ?? '';
            $dateB = $b['fields']['tanggal']['stringValue'] ?? '';
            return strcmp($dateB, $dateA); // Descending order
        });

        // Take only the first 10 documents
        $documents = array_slice($response['documents'], 0, 10);

        foreach ($documents as $document) {
            $fields = $document['fields'] ?? [];
            $id = basename($document['name']);

            // Check for image
            $imageUrl = isset($fields['image']['stringValue']) ? $fields['image']['stringValue'] : null;

            // Get role from ID mapping (remove the ID mapping, assuming role exists in the document)
            $roles = isset($fields['role']['arrayValue']['values']) ? $this->formatRole($fields['role']['arrayValue']['values']) : 'Tidak ada role';

            $activities[] = [
                'id' => $id,
                'nama' => $fields['userName']['stringValue'] ?? '-',
                'userRole' => $roles,
                'keterangan' => $fields['description']['stringValue'] ?? '-',
                'detail' => isset($fields['metadata']['fields']['catatan']['stringValue'])
                            ? $fields['metadata']['fields']['catatan']['stringValue']
                            : 'Tidak ada catatan',
                'waktu' => isset($fields['tanggal']['stringValue'])
                            ? $this->formatDate($fields['tanggal']['stringValue'])
                            : null,
                'image' => $imageUrl,
            ];
        }
    }


    // Return view without pagination
    return view('layouts.dashboard', [
        'activities' => $activities,
        'totalActivities' => count($response['documents'] ?? []),
        'totalBibit' => $totalBibit,
        'totalKayu' => $totalKayu,
        'filter' => $filter,
        'bibitCounts' => $bibitCounts
    ]);
}


    // Helper function to get the date filter based on the selected filter
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
                // No date filter, get all records
                break;
        }

        return $dateFilter;
    }

    // Format the date string into a shorter format without day information
    private function formatDate($dateString)
    {
        try {
            $dateString = preg_replace('/\.\d+$/', '', $dateString); // Removing any microseconds part
            $date = Carbon::parse($dateString);
            return $date->diffForHumans(); // Format like "1 minute ago", "1 hour ago", etc.
        } catch (\Exception $e) {
            return null;
        }
    }

    // Format roles into a comma-separated string
    private function formatRole(array $roles): string
    {
        return implode(', ', array_map(function ($role) {
            return $role['stringValue'] ?? 'Unknown';
        }, $roles));
    }
}
