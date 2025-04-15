@extends('layouts.app')

@section('title', 'Riwayat Scan Barcode')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <!-- Hamburger dan teks Halo Fitri dalam satu baris -->
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Halo Fitri 👋</h1>
        </div>

        <div class="bg-white p-4 rounded-3xl shadow-md text-left w-56">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 3.75h4.5A2.25 2.25 0 0116.5 6v12a2.25 2.25 0 01-2.25 2.25h-4.5A2.25 2.25 0 017.5 18V6a2.25 2.25 0 012.25-2.25z" />
                    </svg>
                </div>

                <div class="ml-4 flex flex-col items-center">
                    <p class="text-gray-500 text-sm">Sedang Aktif</p>
                    <p class="text-3xl font-semibold text-center w-full">9</p>

                    <div class="flex mt-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2 first:ml-0"
                                src="https://randomuser.me/api/portraits/women/{{ $i }}.jpg"
                                alt="User {{ $i }}">
                        @endfor
                    </div>
                </div>
            </div>
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

            <!-- Pagination -->
            <div class="mt-5 flex justify-center">
                {{ $paginatedHistory->links() }}
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
