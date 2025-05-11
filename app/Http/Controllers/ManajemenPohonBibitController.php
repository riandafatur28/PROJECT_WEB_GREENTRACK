<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Firestore\FirestoreClient;

class ManajemenPohonBibitController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        // Initialize Firestore client
        $this->firestore = new FirestoreClient([
            'projectId' => env('FIREBASE_PROJECT_ID'),
        ]);
    }

    public function index()
    {
        // Fetch bibit data from Firestore
        $bibitData = [];
        $bibitCollection = $this->firestore->collection('bibit');
        $bibitDocuments = $bibitCollection->documents();

        foreach ($bibitDocuments as $document) {
            $bibitItem = $document->data();
            if (isset($bibitItem['id_bibit'])) {
                $bibitData[] = [
                    'id' => $bibitItem['id_bibit'],
                    'jenis' => $bibitItem['jenis_bibit'] ?? 'Tidak tersedia',
                    'jumlah' => $bibitItem['tinggi'] ?? 0,
                    'lokasi' => isset($bibitItem['lokasi_tanam']['bkph']) ? $bibitItem['lokasi_tanam']['bkph'] : 'Tidak tersedia',
                    'status' => $bibitItem['kondisi'] ?? 'Penyemaian',
                    'nama' => $bibitItem['nama_bibit'] ?? '',
                    'varietas' => $bibitItem['varietas'] ?? '',
                    'usia' => $bibitItem['usia'] ?? 0,
                    'asal_bibit' => $bibitItem['asal_bibit'] ?? '',
                    'media_tanam' => $bibitItem['media_tanam'] ?? '',
                    'nutrisi' => $bibitItem['nutrisi'] ?? '',
                    'tanggal_pembibitan' => isset($bibitItem['tanggal_pembibitan']['_seconds'])
                        ? date('d/m/Y', $bibitItem['tanggal_pembibitan']['_seconds'])
                        : 'Tidak tersedia',
                    'gambar_image' => $bibitItem['gambar_image'][0] ?? 'https://via.placeholder.com/250',
                ];
            }
        }

        // Fetch kayu data from Firestore
        $kayuData = [];
        $kayuCollection = $this->firestore->collection('kayu');
        $kayuDocuments = $kayuCollection->documents();

        foreach ($kayuDocuments as $document) {
            $kayuItem = $document->data();
            if (isset($kayuItem['id_kayu'])) {
                $kayuData[] = [
                    'id' => $kayuItem['id_kayu'],
                    'jenis' => $kayuItem['jenis_kayu'] ?? 'Tidak tersedia',
                    'jumlah' => $kayuItem['jumlah_stok'] ?? 0,
                    'lokasi' => isset($kayuItem['lokasi_tanam']['bkph']) ? $kayuItem['lokasi_tanam']['bkph'] : 'Tidak tersedia',
                    'status' => isset($kayuItem['jumlah_stok']) && $kayuItem['jumlah_stok'] > 0 ? 'Tersedia' : 'Kosong',
                    'nama' => $kayuItem['nama_kayu'] ?? '',
                    'varietas' => $kayuItem['varietas'] ?? '',
                    'usia' => $kayuItem['usia'] ?? 0,
                    'batch_panen' => $kayuItem['batch_panen'] ?? '',
                    'barcode' => $kayuItem['barcode'] ?? '',
                    'tinggi' => $kayuItem['tinggi'] ?? 0,
                    'tanggal_lahir_pohon' => isset($kayuItem['tanggal_lahir_pohon'])
                        ? date('d/m/Y', $kayuItem['tanggal_lahir_pohon'] / 1000)
                        : 'Tidak tersedia',
                    'catatan' => $kayuItem['catatan'] ?? '',
                    'gambar_image' => $kayuItem['gambar_image'][0] ?? 'https://via.placeholder.com/250',
                ];
            }
        }

        return view('layouts.manajemenkayubibit', [
            'bibit' => $bibitData,
            'kayu' => $kayuData
        ]);
    }
}
