@extends('layouts.app')

@section('content')
    <div class="w-full p-4 md:p-6 mt-8">
        <!-- Header -->
        <h1 class="text-lg md:text-2xl font-semibold text-gray-800 mb-4">Hello Fitri 👋,</h1>

        <div class="bg-white p-3 rounded-lg shadow-md text-center">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">Total Bibit</p>
                    <h2 class="text-xl font-bold text-green-600">{{ number_format(count($bibit)) }}</h2>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Kayu</p>
                    <h2 class="text-xl font-bold text-red-600">{{ number_format(count($kayu)) }}</h2>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Admin Aktif</p>
                    <h2 class="text-xl font-bold text-blue-600">9</h2>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex flex-row border-b mb-4">
            <button class="tab-btn px-3 py-2 text-gray-500 hover:text-gray-800 border-b-2 border-gray-800"
                data-tab="bibit">Bibit</button>
            <button class="tab-btn px-3 py-2 text-gray-500 hover:text-gray-800" data-tab="kayu">Kayu</button>
        </div>

        <!-- Table Bibit -->
        <div id="table-bibit" class="bg-white shadow-md rounded-lg p-3">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Data Bibit</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-xs md:text-sm">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-2 py-1 text-left">ID Bibit</th>
                            <th class="px-2 py-1 text-left">Jenis Bibit</th>
                            <th class="px-2 py-1 text-left">Jumlah</th>
                            <th class="px-2 py-1 text-left">Lokasi</th>
                            <th class="px-2 py-1 text-left">Status</th>
                            <th class="px-2 py-1 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bibit as $item)
                            <tr class="border-b">
                                <td class="px-2 py-1">{{ $item['id'] }}</td>
                                <td class="px-2 py-1">{{ $item['jenis'] }}</td>
                                <td class="px-2 py-1">{{ $item['jumlah'] }} Hari</td>
                                <td class="px-2 py-1">{{ $item['lokasi'] }}</td>
                                <td class="px-2 py-1">
                                    <select class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm"
                                        data-id="{{ $item['id'] }}">
                                        <option value="Tersedia" {{ $item['status'] == 'Tersedia' ? 'selected' : '' }}>
                                            Tersedia</option>
                                        <option value="Siap Potong"
                                            {{ $item['status'] == 'Siap Potong' ? 'selected' : '' }}>Siap Potong</option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <a href="/detail-kayu/{{ $item['id'] }}" class="ml-1 text-blue-600 underline">Lihat
                                        Selengkapnya</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Kayu -->
        <div id="table-kayu" class="bg-white shadow-md rounded-lg p-3 hidden">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Data Kayu</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-xs md:text-sm">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-2 py-1 text-left">ID Kayu</th>
                            <th class="px-2 py-1 text-left">Jenis Kayu</th>
                            <th class="px-2 py-1 text-left">Jumlah</th>
                            <th class="px-2 py-1 text-left">Lokasi</th>
                            <th class="px-2 py-1 text-left">Status</th>
                            <th class="px-2 py-1 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kayu as $item)
                            <tr class="border-b">
                                <td class="px-2 py-1">{{ $item['id'] }}</td>
                                <td class="px-2 py-1">{{ $item['jenis'] }}</td>
                                <td class="px-2 py-1">{{ $item['jumlah'] }} Batang</td>
                                <td class="px-2 py-1">{{ $item['lokasi'] }}</td>
                                <td class="px-2 py-1">
                                    <select class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm"
                                        data-id="{{ $item['id'] }}">
                                        <option value="Tersedia" {{ $item['status'] == 'Tersedia' ? 'selected' : '' }}>
                                            Tersedia</option>
                                        <option value="Habis" {{ $item['status'] == 'Habis' ? 'selected' : '' }}>Habis
                                        </option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <a href="/detail-kayu/{{ $item['id'] }}" class="ml-1 text-blue-600 underline">Lihat
                                        Selengkapnya</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabButtons = document.querySelectorAll(".tab-btn");
        const tables = {
            bibit: document.getElementById("table-bibit"),
            kayu: document.getElementById("table-kayu"),
        };

        tabButtons.forEach(button => {
            button.addEventListener("click", function() {
                const tab = this.getAttribute("data-tab");

                // Sembunyikan semua tabel
                Object.values(tables).forEach(table => table.classList.add("hidden"));

                // Tampilkan tabel yang sesuai
                tables[tab].classList.remove("hidden");

                // Ubah tampilan tombol tab
                tabButtons.forEach(btn => btn.classList.remove("border-gray-800"));
                this.classList.add("border-gray-800");
            });
        });
    });
</script>
