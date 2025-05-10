@extends('layouts.app')

@section('title', 'Riwayat Perawatan Bibit')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">{{ session('user_nama') }} 👋</h1>
        </div>

        <div id="table-aktivitas" class="bg-white shadow-md rounded-3xl p-5 mt-5">
            <h2 class="text-xl font-semibold text-gray-800">Riwayat Perawatan</h2>

            <!-- Bagian Pencarian -->
            <form method="GET" action="{{ route('history.index') }} "
                class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 space-y-3 md:space-y-0">
                <div class="relative w-full md:w-auto">
                    <input type="text" name="search" placeholder="Cari deskripsi..." value="{{ $search }}"
                        class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                    <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                </div>

                <div class="flex items-center space-x-3">
                    <label class="text-gray-600 text-sm">Urutkan Berdasarkan:</label>
                    <select class="px-3 py-2 bg-gray-100 border rounded-lg">
                        <option value="30">30 hari terakhir</option>
                        <option value="7">7 hari terakhir</option>
                        <option value="1">Hari ini</option>
                    </select>
                </div>
            </form>

            <div class="overflow-x-auto mt-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-100">
                            <th class="py-3 text-left px-4">Nama Admin</th>
                            <th class="py-3 text-left px-4">Jenis Perawatan</th>
                            <th class="py-3 text-left px-4">Nama Bibit</th>
                            <th class="py-3 text-left px-4">Waktu</th>
                            <th class="py-3 text-left px-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perawatan as $data)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $data['nama'] }}</td>
                                <td class="py-3 px-4">{{ $data['keterangan'] }}</td>
                                <td class="py-3 px-4">{{ $data['nama_bibit'] }}</td>
                                <td class="py-3 px-4">{{ $data['waktu'] ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $data['detail'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-3 px-4 text-center text-gray-500">Tidak ada data ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-8">
                <p class="text-sm text-gray-500">
                    Menampilkan {{ count($perawatan) }} dari total {{ $total }} entri
                </p>
            </div>
        </div>
    </div>
@endsection
