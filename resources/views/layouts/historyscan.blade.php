@extends('layouts.app')

@section('title', 'Riwayat Scan Barcode')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">{{ session('user_nama') }} 👋</h1>
        </div>

        <!-- Start of the Table Section -->
        <div id="table-aktivitas" class="overflow-x-auto mt-6 bg-white shadow-md rounded-3xl p-5">
            <div id="search-sort-section"
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Aktivitas Terbaru</h2>

                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Pencarian -->
                    <div id="search-bar" class="relative w-full md:w-auto">
                        <input type="text" placeholder="Cari" id="searchActivities"
                            class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                    </div>

                    <!-- Dropdown Urutkan -->
                    <div id="sort-dropdown" class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <select id="sortKayu"
                            class="ml-1 text-gray-800 font-semibold border bg-gray-100 px-2 py-1 rounded-lg w-full md:w-auto">
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Table Header -->
            <table class="w-full text-sm text-left text-gray-600 mt-8">
                <thead class="bg-white">
                    <tr class="text-gray-600">
                        <th class="py-3 px-4 text-left">Profil Admin</th>
                        <th class="py-3 px-4 text-left">Jenis Aktivitas</th>
                        <th class="py-3 px-8 text-left">Waktu</th>
                        <th class="py-3 px-8 text-left">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/64?u={{ urlencode($activity['nama']) }}"
                                        alt="{{ $activity['nama'] }}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="font-semibold">{{ $activity['nama'] }}</div>
                                        <div class="text-sm text-gray-400">{{ $activity['userRole'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">{{ $activity['keterangan'] }}</td>
                            <td class="py-3 px-8 whitespace-nowrap">
                                {{ $activity['waktu'] ? \Carbon\Carbon::parse($activity['waktu'])->diffForHumans() : '-' }}
                            </td>
                            <td class="py-3 px-8">{{ $activity['detail'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-center text-gray-500">Tidak ada aktivitas terbaru</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- End of Table Section -->

        <!-- Start of Pagination Section -->
        <div class="flex justify-between items-center mt-6">
            <p class="text-sm text-gray-500">
                Menampilkan {{ count($activities) }} dari total {{ $total }} entri
            </p>
            <div>
                <button class="px-4 py-2 bg-gray-200 rounded-md text-sm text-gray-500 hover:bg-gray-300">«</button>
                <button class="px-4 py-2 bg-gray-200 rounded-md text-sm text-gray-500 hover:bg-gray-300">1</button>
                <button class="px-4 py-2 bg-gray-200 rounded-md text-sm text-gray-500 hover:bg-gray-300">2</button>
                <button class="px-4 py-2 bg-gray-200 rounded-md text-sm text-gray-500 hover:bg-gray-300">»</button>
            </div>
        </div>
        <!-- End of Pagination Section -->
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Page Loaded!");
        });
    </script>
@endsection
