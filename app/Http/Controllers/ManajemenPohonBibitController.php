<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Log;

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
                if ($search && stripos($fields['nama_bibit']['stringValue'] ?? '', $search) === false) {
                    continue;
                }

                // Debug untuk melihat struktur data gambar
                if (isset($fields['gambar_image'])) {
                    Log::info('Struktur gambar untuk bibit ID ' . $id . ': ' . json_encode($fields['gambar_image']));
                }

                // Penanganan gambar yang lebih baik
                $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

                $bibit[] = [
                    'id' => $id,
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'jenis_bibit' => $fields['jenis_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'status' => $fields['kondisi']['stringValue'] ?? 'Penyemaian',
                    'lokasi' => $fields['lokasi_tanam']['bkph']['stringValue'] ?? 'Tidak tersedia',
                    'media_tanam' => $fields['media_tanam']['stringValue'] ?? '-',
                    'gambar_image' => $gambarUrl,
                    'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                    'varietas' => $fields['varietas']['stringValue'] ?? '-',
                    // Tambahkan semua field lain yang mungkin diperlukan untuk detail
                    'tanggal_tanam' => $fields['tanggal_tanam']['timestampValue'] ?? '-',
                    'deskripsi' => $fields['deskripsi']['stringValue'] ?? '-',
                    // Simpan struktur asli gambar untuk debugging
                    'raw_gambar' => $fields['gambar_image'] ?? null,
                ];
            }
        }

        // Proses data kayu
        if (isset($kayuResponse['documents'])) {
            foreach ($kayuResponse['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Filter pencarian
                if ($search && stripos($fields['nama_kayu']['stringValue'] ?? '', $search) === false) {
                    continue;
                }

                // Debug untuk melihat struktur data gambar
                if (isset($fields['gambar_image'])) {
                    Log::info('Struktur gambar untuk kayu ID ' . $id . ': ' . json_encode($fields['gambar_image']));
                }

                // Penanganan gambar yang lebih baik
                $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

                $kayu[] = [
                    'id' => $id,
                    'nama_kayu' => $fields['nama_kayu']['stringValue'] ?? '-',
                    'jenis_kayu' => $fields['jenis_kayu']['stringValue'] ?? '-',
                    'status' => ($fields['jumlah_stok']['integerValue'] ?? 0) > 0 ? 'Tersedia' : 'Kosong',
                    'lokasi' => $fields['lokasi_tanam']['bkph']['stringValue'] ?? 'Tidak tersedia',
                    'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                    'gambar_image' => $gambarUrl,
                    'batch_panen' => $fields['batch_panen']['stringValue'] ?? '-',
                    'varietas' => $fields['varietas']['stringValue'] ?? '-',
                    // Tambahkan semua field lain yang mungkin diperlukan untuk detail
                    'tanggal_panen' => $fields['tanggal_panen']['timestampValue'] ?? '-',
                    'deskripsi' => $fields['deskripsi']['stringValue'] ?? '-',
                    // Simpan struktur asli gambar untuk debugging
                    'raw_gambar' => $fields['gambar_image'] ?? null,
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

    /**
     * Fungsi helper untuk mengekstrak URL gambar dari berbagai kemungkinan struktur data
     */
    private function extractImageUrl($imageField)
    {
        // Default image
        $defaultImage = 'https://via.placeholder.com/250';

        if (empty($imageField)) {
            return $defaultImage;
        }

        // Jika gambar_image adalah array values
        if (isset($imageField['arrayValue']) && isset($imageField['arrayValue']['values'])) {
            $values = $imageField['arrayValue']['values'];
            if (!empty($values) && isset($values[0]['stringValue'])) {
                return $values[0]['stringValue'];
            }
        }

        // Jika gambar_image langsung berisi array dengan indeks numerik
        if (is_array($imageField) && isset($imageField[0]['stringValue'])) {
            return $imageField[0]['stringValue'];
        }

        // Jika gambar_image adalah string langsung
        if (isset($imageField['stringValue'])) {
            return $imageField['stringValue'];
        }

        // Log struktur gambar yang tidak dikenali untuk debugging
        Log::warning('Struktur gambar tidak dikenali: ' . json_encode($imageField));

        return $defaultImage;
    }

    // Tambahkan method untuk mengambil detail bibit untuk modal
    public function getBibitDetail(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        // Ambil detail bibit dari Firestore
        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}";
        $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
            ->get($url);

        if ($response->successful()) {
            $document = $response->json();
            $fields = $document['fields'] ?? [];

            // Debug untuk melihat struktur data gambar
            if (isset($fields['gambar_image'])) {
                Log::info('Detail Bibit - Struktur gambar: ' . json_encode($fields['gambar_image']));
            }

            // Ekstrak URL gambar
            $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

            $detail = [
                'id' => $id,
                'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? 'Tidak tersedia',
                'jenis_bibit' => $fields['jenis_bibit']['stringValue'] ?? 'Tidak tersedia',
                'status' => $fields['kondisi']['stringValue'] ?? 'Penyemaian',
                'lokasi' => $fields['lokasi_tanam']['bkph']['stringValue'] ?? 'Tidak tersedia',
                'media_tanam' => $fields['media_tanam']['stringValue'] ?? '-',
                'gambar_image' => $gambarUrl,
                'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                'varietas' => $fields['varietas']['stringValue'] ?? '-',
                'tanggal_tanam' => $fields['tanggal_tanam']['timestampValue'] ?? '-',
                'deskripsi' => $fields['deskripsi']['stringValue'] ?? '-',
            ];

            return response()->json(['success' => true, 'data' => $detail]);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
    }

    // Tambahkan method untuk mengambil detail kayu untuk modal
    public function getKayuDetail(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        // Ambil detail kayu dari Firestore
        $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}";
        $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
            ->get($url);

        if ($response->successful()) {
            $document = $response->json();
            $fields = $document['fields'] ?? [];

            // Ekstrak URL gambar
            $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

            $detail = [
                'id' => $id,
                'nama_kayu' => $fields['nama_kayu']['stringValue'] ?? '-',
                'jenis_kayu' => $fields['jenis_kayu']['stringValue'] ?? '-',
                'status' => ($fields['jumlah_stok']['integerValue'] ?? 0) > 0 ? 'Tersedia' : 'Kosong',
                'lokasi' => $fields['lokasi_tanam']['bkph']['stringValue'] ?? 'Tidak tersedia',
                'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                'gambar_image' => $gambarUrl,
                'batch_panen' => $fields['batch_panen']['stringValue'] ?? '-',
                'varietas' => $fields['varietas']['stringValue'] ?? '-',
                'tanggal_panen' => $fields['tanggal_panen']['timestampValue'] ?? '-',
                'jumlah_stok' => $fields['jumlah_stok']['integerValue'] ?? 0,
                'deskripsi' => $fields['deskripsi']['stringValue'] ?? '-',
            ];

            return response()->json(['success' => true, 'data' => $detail]);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
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
