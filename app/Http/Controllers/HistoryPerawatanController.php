<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryPerawatanController extends Controller
{
    protected $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 5; // Tentukan jumlah data per halaman

        // Ambil data perawatan bibit dari Firestore
        $response = $this->firestore->getCollection('perawatan_bibit', [
            'pageSize' => $perPage,
            'pageToken' => $page > 1 ? $page : null
        ]);

        $perawatan = [];
        if (isset($response['documents'])) {
            foreach ($response['documents'] as $document) {
                $fields = $document['fields'] ?? [];

                $perawatan[] = [
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? '',
                    'waktu' => Carbon::parse($fields['tanggal']['timestampValue'])->diffForHumans(),
                    'catatan' => $fields['catatan']['stringValue'] ?? '',
                    'created_by_name' => $fields['created_by_name']['stringValue'] ?? 'Admin',
                    'jenis_perawatan' => $fields['jenis_perawatan']['stringValue'] ?? '',
                ];
            }
        }

        // Pagination manual
        $paginatedPerawatan = new LengthAwarePaginator(
            $perawatan,
            count($perawatan),
            $perPage,
            $page,
            ['path' => url('/riwayat-perawatan')]
        );

        return view('layouts.historyperawatan', compact('paginatedPerawatan'));
    }
}
