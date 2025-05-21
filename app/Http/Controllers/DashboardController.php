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
                $dateA = $a['fields']['tanggal']['stringValue'] ?? '';
                $dateB = $b['fields']['tanggal']['stringValue'] ?? '';
                return strcmp($dateB, $dateA);
            });

            // Take only the first 10 documents
            $documents = array_slice($response['documents'], 0, 10);

            foreach ($documents as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Check for image
                $imageUrl = isset($fields['image']['stringValue']) ? $fields['image']['stringValue'] : null;

                // Get role from ID mapping
                $roles = isset($fields['role']['arrayValue']['values']) ? $this->formatRole($fields['role']['arrayValue']['values']) : 'Tidak ada role';

                $activities[] = [
                    'id' => $id,
                    'nama' => $fields['userName']['stringValue'] ?? '-',
                    'userRole' => $roles,
                    'keterangan' => $fields['description']['stringValue'] ?? '-',
                    'detail' => isset($fields['metadata']['fields']['catatan']['stringValue']) ? $fields['metadata']['fields']['catatan']['stringValue'] : 'Tidak ada catatan',
                    'waktu' => isset($fields['tanggal']['stringValue']) ? $this->formatDate($fields['tanggal']['stringValue']) : null,
                    'image' => $imageUrl,
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

    // Format the date
    private function formatDate($dateString)
    {
        try {
            $date = Carbon::parse($dateString);
            return $date->diffForHumans();
        } catch (\Exception $e) {
            return null;
        }
    }

    // Format roles into a string
    private function formatRole(array $roles): string
    {
        return implode(', ', array_map(function ($role) {
            return $role['stringValue'] ?? 'Unknown';
        }, $roles));
    }
}
