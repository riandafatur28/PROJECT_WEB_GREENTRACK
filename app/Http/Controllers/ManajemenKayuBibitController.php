<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManajemenKayuBibitController extends Controller
{
    private function countActiveAdmin(FirestoreService $firestore)
    {
        try {
            $akunResponse = $firestore->getCollection('akun');
            $totalActiveAdmin = 0;

            if (isset($akunResponse['documents'])) {
                foreach ($akunResponse['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    
                    // Check if the user has admin role (either admin_penyemaian or admin_tpk)
                    if (isset($fields['role']['arrayValue']['values'])) {
                        $roles = $fields['role']['arrayValue']['values'];
                        foreach ($roles as $role) {
                            $roleValue = $role['stringValue'] ?? '';
                            if (strpos($roleValue, 'admin_') === 0) {
                                // Check if admin is active
                                $status = $fields['status']['stringValue'] ?? 'Aktif';
                                if ($status === 'Aktif') {
                                    $totalActiveAdmin++;
                                }
                                break;
                            }
                        }
                    }
                }
            }

            return $totalActiveAdmin;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function index(Request $request, FirestoreService $firestore)
    {
        // Get the active tab from the request
        $activeTab = $request->query('tab', 'bibit');
        
        // Get search and sort parameters
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'terbaru');
        
        // Get the current page for pagination
        $currentPage = $request->input('page', 1);
        $perPage = 10;

        // Get total active admin count
        $totalActiveAdmin = $this->countActiveAdmin($firestore);

        // Get bibit data
        $bibitResponse = $firestore->getCollection('bibit');
        $bibit = [];
        $totalBibit = 0;

        if (isset($bibitResponse['documents'])) {
            foreach ($bibitResponse['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Get lokasi_tanam data safely
                $lokasiTanam = $fields['lokasi_tanam']['mapValue']['fields'] ?? [];
                $lokasi = '';
                
                // Build lokasi string if the data exists
                if (!empty($lokasiTanam)) {
                    $kph = $lokasiTanam['kph']['stringValue'] ?? '';
                    $bkph = $lokasiTanam['bkph']['stringValue'] ?? '';
                    $rkph = $lokasiTanam['rkph']['stringValue'] ?? '';
                    $luasPetak = $lokasiTanam['luas_petak']['stringValue'] ?? '';
                    
                    $lokasiParts = array_filter([$kph, $bkph, $rkph, $luasPetak]);
                    $lokasi = !empty($lokasiParts) ? implode(' - ', $lokasiParts) : 'Lokasi tidak tersedia';
                } else {
                    $lokasi = 'Lokasi tidak tersedia';
                }

                // Process bibit data
                $bibitData = [
                    'id' => $id,
                    'id_bibit' => $fields['id_bibit']['stringValue'] ?? $id,
                    'jenis_bibit' => $fields['jenis_bibit']['stringValue'] ?? '-',
                    'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                    'lokasi' => $lokasi,
                    'status' => $fields['status']['stringValue'] ?? 'Penyemaian',
                    'usia' => $fields['usia']['integerValue'] ?? 0,
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? '-',
                    'varietas' => $fields['varietas']['stringValue'] ?? '-',
                    'produktivitas' => $fields['produktivitas']['stringValue'] ?? '-',
                    'asal_bibit' => $fields['asal_bibit']['stringValue'] ?? '-',
                    'nutrisi' => $fields['nutrisi']['stringValue'] ?? '-',
                    'media_tanam' => $fields['media_tanam']['stringValue'] ?? '-',
                    'status_hama' => $fields['status_hama']['stringValue'] ?? '-',
                    'catatan' => $fields['catatan']['stringValue'] ?? '-',
                ];

                // Handle gambar_image array safely
                if (isset($fields['gambar_image']['arrayValue']['values'][0]['stringValue'])) {
                    $bibitData['gambar_image'] = $fields['gambar_image']['arrayValue']['values'][0]['stringValue'];
                } else {
                    $bibitData['gambar_image'] = 'https://via.placeholder.com/150';
                }

                $bibit[] = $bibitData;
                $totalBibit++;
            }
        }

        // Get kayu data
        $kayuResponse = $firestore->getCollection('kayu');
        $kayu = [];
        $totalKayu = 0;

        if (isset($kayuResponse['documents'])) {
            foreach ($kayuResponse['documents'] as $document) {
                $fields = $document['fields'] ?? [];
                $id = basename($document['name']);

                // Get lokasi_tanam data safely
                $lokasiTanam = $fields['lokasi_tanam']['mapValue']['fields'] ?? [];
                $lokasi = '';
                
                // Build lokasi string if the data exists
                if (!empty($lokasiTanam)) {
                    $kph = $lokasiTanam['kph']['stringValue'] ?? '';
                    $bkph = $lokasiTanam['bkph']['stringValue'] ?? '';
                    $rkph = $lokasiTanam['rkph']['stringValue'] ?? '';
                    $luasPetak = $lokasiTanam['luas_petak']['stringValue'] ?? '';
                    
                    $lokasiParts = array_filter([$kph, $bkph, $rkph, $luasPetak]);
                    $lokasi = !empty($lokasiParts) ? implode(' - ', $lokasiParts) : 'Lokasi tidak tersedia';
                } else {
                    $lokasi = 'Lokasi tidak tersedia';
                }

                // Process kayu data
                $kayuData = [
                    'id' => $id,
                    'id_kayu' => $fields['id_kayu']['stringValue'] ?? $id,
                    'jenis_kayu' => $fields['jenis_kayu']['stringValue'] ?? '-',
                    'tinggi' => $fields['tinggi']['integerValue'] ?? 0,
                    'lokasi' => $lokasi,
                    'batch_panen' => $fields['batch_panen']['stringValue'] ?? '-',
                    'status' => $fields['status']['stringValue'] ?? 'Tersedia',
                    'nama_kayu' => $fields['nama_kayu']['stringValue'] ?? '-',
                    'barcode' => $fields['barcode']['stringValue'] ?? '-',
                    'jumlah_stok' => $fields['jumlah_stok']['integerValue'] ?? 0,
                    'varietas' => $fields['varietas']['stringValue'] ?? '-',
                    'usia' => $fields['usia']['integerValue'] ?? 0,
                    'catatan' => $fields['catatan']['stringValue'] ?? '-',
                ];

                // Handle gambar_image array safely
                if (isset($fields['gambar_image']['arrayValue']['values'][0]['stringValue'])) {
                    $kayuData['gambar_image'] = $fields['gambar_image']['arrayValue']['values'][0]['stringValue'];
                } else {
                    $kayuData['gambar_image'] = 'https://via.placeholder.com/150';
                }

                $kayu[] = $kayuData;
                $totalKayu++;
            }
        }

        // Return view with all necessary data
        return view('layouts.manajemenkayubibit', [
            'bibit' => $bibit,
            'kayu' => $kayu,
            'totalBibit' => $totalBibit,
            'totalKayu' => $totalKayu,
            'currentPage' => $currentPage,
            'lastPage' => ceil(max(count($bibit), count($kayu)) / $perPage),
            'search' => $search,
            'sort' => $sort,
            'totalActiveAdmin' => $totalActiveAdmin
        ]);
    }
} 