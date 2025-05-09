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
        $response = $firestore->getCollection('activities', [
            'filter' => $filter,
            'pageSize' => $perPage,
            'pageToken' => $page > 1 ? $page : null
        ]);

        $activities = [];
        $total = 0;

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                $activities[] = [
                    'id' => $id,
                    'nama' => $fields['userName']['stringValue'] ?? '-',
                    // Menambahkan pengecekan untuk memformat userRole
                    'userRole' => isset($fields['role']['arrayValue']['values'])
                                    ? $this->formatRole($fields['role']['arrayValue']['values'])
                                    : 'Tidak ada role', // Default jika tidak ada role
                    'keterangan' => $fields['description']['stringValue'] ?? '-',
                    'detail' => isset($fields['metadata']['fields']['catatan']['stringValue'])
                                ? $fields['metadata']['fields']['catatan']['stringValue']
                                : 'Tidak ada catatan',
                    'waktu' => isset($fields['tanggal']['stringValue'])
                                ? $this->formatDate($fields['tanggal']['stringValue'])
                                : null,
                ];

                $total++;
            }
        }

        return view('layouts.historyscan', [
            'activities' => $activities,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'searchType' => $searchType // Menambahkan searchType agar bisa dipakai di tampilan
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

    // Fungsi untuk memformat tanggal agar sesuai dengan format yang diinginkan
    private function formatDate($dateString)
    {
        try {
            // Menghapus microdetik untuk memastikan format tanggal bisa dikenali
            $dateString = preg_replace('/\.\d+$/', '', $dateString);

            // Coba parse tanggal dengan format yang sudah diubah
            $date = \Carbon\Carbon::parse($dateString);

            return $date->format('Y-m-d H:i:s');  // Format yang diinginkan
        } catch (\Exception $e) {
            // Jika gagal parse, kembalikan null atau nilai default
            return null;
        }
    }
}
