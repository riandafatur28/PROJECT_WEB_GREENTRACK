@extends('layouts.app')

@section('title', 'Riwayat Scan Barcode')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <!-- Hamburger dan teks Halo Fitri dalam satu baris -->
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Halo Fitri 👋</h1>
        </div>

        <div id="table-aktivitas" class="bg-white shadow-md rounded-3xl p-5 mt-5">
            <h2 class="text-xl font-semibold text-gray-800">Aktivitas Terbaru</h2>

            <!-- Bagian Pencarian & Filter -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 space-y-3 md:space-y-0">
                <!-- Input Pencarian -->
                <div class="relative w-full md:w-auto transition-all duration-300 peer-checked:hidden">
                    <label for="sidebarToggle" class="absolute"></label> <!-- Tambahkan peer -->
                    <input type="text" placeholder="Cari"
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
            </div>

            <!-- Tabel Aktivitas -->
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-100">
                            <th class="py-3 text-left px-4">Profil Admin</th>
                            <th class="py-3 text-left px-4">Jenis Aktivitas</th>
                            <th class="py-3 text-left px-4">Waktu</th>
                            <th class="py-3 text-left px-4">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedHistory as $data)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 flex items-center">
                                    <img src="{{ $data['foto'] ?? asset('images/default.png') }}"
                                        class="w-10 h-10 rounded-full mr-3" alt="Profil">
                                    <div>
                                        <p class="font-semibold">{{ $data['nama'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $data['jabatan'] }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4">{{ $data['aktivitas'] }}</td>
                                <td class="py-3 px-4">{{ \Carbon\Carbon::parse($data['waktu'])->diffForHumans() }}</td>
                                <td class="py-3 px-4">{{ $data['detail'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-8">
                <p class="text-sm text-gray-500">Menampilkan data 1 hingga 8 dari 256 entri</p>
                <div class="flex space-x-1">
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&lt;</button>
                    <button
                        class="px-2 py-0.5 bg-green-500 text-white border border-green-500 rounded-md text-xs">1</button>
                    <button class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">2</button>
                    <button class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">3</button>
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">...</button>
                    <button class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">40</button>
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&gt;</button>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Page Loaded!");
        });
    </script>
@endsection
