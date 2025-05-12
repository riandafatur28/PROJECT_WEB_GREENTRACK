<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(FirestoreService $firestore)
    {
        // Ambil semua data activities dari koleksi "activities"
        $response = $firestore->getCollection('activities');

        $activities = [];
        $totalActivities = 0;

        // Ambil total bibit
        $bibitResponse = $firestore->getCollection('bibit');
        $totalBibit = count($bibitResponse['documents'] ?? []);  // Menghitung total bibit

        // Ambil total kayu
        $kayuResponse = $firestore->getCollection('kayu');
        $totalKayu = count($kayuResponse['documents'] ?? []);  // Menghitung total kayu

        // Proses data activities
        if (isset($response['documents'])) {
            foreach ($response['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                $activities[] = [
                    'id' => $id,
                    'nama' => $fields['userName']['stringValue'] ?? '-',
                    'userRole' => isset($fields['role']['arrayValue']['values'])
                                    ? $this->formatRole($fields['role']['arrayValue']['values'])
                                    : 'Tidak ada role',
                    'keterangan' => $fields['description']['stringValue'] ?? '-',
                    'detail' => isset($fields['metadata']['fields']['catatan']['stringValue'])
                                ? $fields['metadata']['fields']['catatan']['stringValue']
                                : 'Tidak ada catatan',
                    'waktu' => isset($fields['tanggal']['stringValue'])
                                ? $this->formatDate($fields['tanggal']['stringValue'])
                                : null,
                ];

                $totalActivities++;
            }
        }

        // Return view dengan data total bibit dan kayu
        return view('layouts.dashboard', [
            'activities' => $activities,
            'totalActivities' => $totalActivities,
            'totalBibit' => $totalBibit,
            'totalKayu' => $totalKayu,
        ]);
    }

    // Fungsi tambahan untuk format role
    private function formatRole(array $roles): string
    {
        $roleNames = [];
        foreach ($roles as $role) {
            $roleNames[] = $role['stringValue'] ?? '';
        }
        return implode(', ', $roleNames);
    }

    // Fungsi untuk memformat tanggal
    private function formatDate($dateString)
    {
        try {
            $dateString = preg_replace('/\.\d+$/', '', $dateString);
            $date = Carbon::parse($dateString);
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
