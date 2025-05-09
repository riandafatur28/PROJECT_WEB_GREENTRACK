<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;

class PerawatanController extends Controller
{
    public function index(FirestoreService $firestore)
    {
        // Ambil semua dokumen dari koleksi jadwal_perawatan
        $perawatanData = $firestore->getCollection('jadwal_perawatan');

        // Ambil hanya field yang dibutuhkan
        $jadwal = collect($perawatanData['documents'] ?? [])->map(function ($doc) {
            $fields = $doc['fields'] ?? [];

            return [
                'created_by_name' => $fields['created_by_name']['stringValue'] ?? '(Tidak diketahui)',
                'jenis_perawatan' => $fields['jenis_perawatan']['stringValue'] ?? '-',
                'tanggal' => $fields['tanggal']['timestampValue'] ?? '-',
                'detail' => $fields['catatan']['stringValue'] ?? '-', // 'catatan' saya anggap sebagai detail
            ];
        });

        // Kirim ke view atau response
        return view('perawatan.index', ['jadwal' => $jadwal]);
    }
}
