<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryPerawatanController extends Controller
{
    public function index(Request $request)
    {
        $history = collect([
            [
                'foto' => asset('images/admin1.jpg'),
                'nama' => 'Admin 1',
                'jabatan' => 'Super Admin',
                'aktivitas' => 'Mengedit data pengguna',
                'waktu' => Carbon::now()->subHours(2),
                'detail' => 'Mengubah data pengguna ID 102'
            ],
            [
                'foto' => asset('images/admin2.jpg'),
                'nama' => 'Admin 2',
                'jabatan' => 'Moderator',
                'aktivitas' => 'Menambahkan postingan baru',
                'waktu' => Carbon::now()->subHours(5),
                'detail' => 'Menambahkan artikel tentang keamanan sistem'
            ],
        ]);

        // Pagination manual
        $perPage = 5;
        $currentPage = $request->query('page', 1);
        $paginatedHistory = new LengthAwarePaginator(
            $history->forPage($currentPage, $perPage),
            $history->count(),
            $perPage,
            $currentPage,
            ['path' => url('/history-perawatan-page')]
        );

        return view('layouts.historyperawatan', compact('paginatedHistory'));
    }
}
