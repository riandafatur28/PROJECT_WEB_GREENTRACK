@extends('layouts.app')

@section('title', 'History Scan Barcode')

@section('content')
    <div class="w-full p-4 md:p-6 mt-8">
        <h1 class="text-lg md:text-2xl font-semibold text-gray-800 mb-4">Hello Fitri 👋,</h1>

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

            <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4">
                <div class="relative w-full md:w-64">
                    <input type="text" placeholder="Cari"
                        class="pl-10 pr-3 py-2 w-full border rounded-lg bg-gray-100 focus:outline-none">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                </div>

                <select class="ml-3 px-3 py-2 bg-gray-100 border rounded-lg">
                    <option value="30">30 hari terakhir</option>
                    <option value="7">7 hari terakhir</option>
                    <option value="1">Hari ini</option>
                </select>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 text-left">Profil Admin</th>
                            <th class="py-3 text-left">Jenis Aktivitas</th>
                            <th class="py-3 text-left">Waktu</th>
                            <th class="py-3 text-left">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedHistory as $data)
                            <tr class="border-b">
                                <td class="py-3 flex items-center">
                                    <img src="{{ $data['foto'] ?? asset('images/default.png') }}"
                                        class="w-10 h-10 rounded-full mr-3" alt="Profil">
                                    <div>
                                        <p class="font-semibold">{{ $data['nama'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $data['jabatan'] }}</p>
                                    </div>
                                </td>
                                <td class="py-3">{{ $data['aktivitas'] }}</td>
                                <td class="py-3">{{ \Carbon\Carbon::parse($data['waktu'])->diffForHumans() }}</td>
                                <td class="py-3">{{ $data['detail'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-5">
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
