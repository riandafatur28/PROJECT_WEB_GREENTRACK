<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;

class ManajemenPohonBibitController extends Controller
{
    public function index(Request $request, FirestoreService $firestore)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = 10;

        // Ambil data bibit dan kayu dari Firestore
        $bibitResponse = $firestore->getCollection('bibit');
        $kayuResponse = $firestore->getCollection('kayu');

        $bibit = [];
        $kayu = [];

        // Proses data bibit
        if (isset($bibitResponse['documents'])) {
            foreach ($bibitResponse['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Filter pencarian
                if ($search && stripos($fields['nama_bibit']['stringValue'], $search) === false) {
                    continue;
                }

                // Memastikan kunci ada sebelum mengaksesnya
                $bibit[] = [
                    'id' => $id,
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'jenis_bibit' => $fields['jenis_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'status' => $fields['kondisi']['stringValue'] ?? 'Penyemaian',
                    'lokasi' => $fields['lokasi_tanam']['bkph']['stringValue'] ?? 'Tidak tersedia',
                    'media_tanam' => $fields['media_tanam']['stringValue'] ?? '-',
                    'gambar_image' => $fields['gambar_image'][0]['stringValue'] ?? 'https://via.placeholder.com/250',  // Ambil gambar pertama dari array
                    'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                    'varietas' => $fields['varietas']['stringValue'] ?? '-',
                ];
            }
        }

        // Proses data kayu
        if (isset($kayuResponse['documents'])) {
            foreach ($kayuResponse['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Filter pencarian
                if ($search && stripos($fields['nama_kayu']['stringValue'], $search) === false) {
                    continue;
                }

                $kayu[] = [
                    'id' => $id,
                    'nama_kayu' => $fields['nama_kayu']['stringValue'] ?? '-',
                    'jenis_kayu' => $fields['jenis_kayu']['stringValue'] ?? '-',
                    'status' => ($fields['jumlah_stok']['integerValue'] ?? 0) > 0 ? 'Tersedia' : 'Kosong',
                    'lokasi' => $fields['lokasi_tanam']['bkph']['stringValue'] ?? 'Tidak tersedia',
                    'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                    'gambar_image' => $fields['gambar_image'][0]['stringValue'] ?? 'https://via.placeholder.com/250',
                    'batch_panen' => $fields['batch_panen']['stringValue'] ?? '-',
                    'varietas' => $fields['varietas']['stringValue'] ?? '-',
                ];
            }
        }

        $totalBibit = count($bibit);
        $totalKayu = count($kayu);

        // Pagination
        $offsetBibit = ($page - 1) * $perPage;
        $offsetKayu = ($page - 1) * $perPage;

        $paginatedBibit = array_slice($bibit, $offsetBibit, $perPage);
        $paginatedKayu = array_slice($kayu, $offsetKayu, $perPage);

        return view('layouts.manajemenkayubibit', [
            'bibit' => $paginatedBibit,
            'kayu' => $paginatedKayu,
            'totalBibit' => $totalBibit,
            'totalKayu' => $totalKayu,
            'currentPage' => $page,
            'perPage' => $perPage,
        ]);
    }

    // Fungsi untuk memperbarui status bibit
    public function updateBibitStatus(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        // URL API Firestore untuk update status bibit
        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}?updateMask.fieldPaths=kondisi";
        $payload = [
            'fields' => [
                'kondisi' => ['stringValue' => $status]
            ]
        ];

        // Melakukan permintaan PATCH ke Firestore
        $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
            ->patch($url, $payload);

        return response()->json(['success' => $response->successful()]);
    }

    // Fungsi untuk memperbarui status kayu
    public function updateKayuStatus(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        // URL API Firestore untuk update status kayu
        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}?updateMask.fieldPaths=status";
        $payload = [
            'fields' => [
                'status' => ['stringValue' => $status]
            ]
        ];

        // Melakukan permintaan PATCH ke Firestore
        $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
            ->patch($url, $payload);

        return response()->json(['success' => $response->successful()]);
    }
}
