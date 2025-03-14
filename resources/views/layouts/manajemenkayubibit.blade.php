@extends('layouts.app')


@section('content')
    <div class="w-full p-4 md:p-6 mt-8">
        <!-- Header -->
        <h1 class="text-lg md:text-2xl font-semibold text-gray-800 mb-4">Hello Fitri 👋,</h1>

        <div class="bg-white p-6 rounded-3xl shadow-md">
            <div class="grid grid-cols-3 divide-x divide-gray-300">
                <!-- Total Bibit -->
                <div class="flex items-center justify-center px-4">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m-4-4h8m-8-4h8m-8-4h8" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-600 text-sm">Total Bibit</p>
                        <h2 class="text-xl font-bold text-green-600">{{ number_format(count($bibit)) }}</h2>
                        <p class="text-xs text-green-500 mt-1 flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            16% bulan ini
                        </p>
                    </div>
                </div>

                <!-- Total Kayu -->
                <div class="flex items-center justify-center px-4">
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m-4-4h8m-8-4h8m-8-4h8" />
                        </svg>
                    </div>
                    <div class="ml-3 flex flex-col">
                        <p class="text-gray-600 text-sm">Total Kayu</p>
                        <h2 class="text-xl font-bold text-red-600">{{ number_format(count($kayu)) }}</h2>
                        <p class="text-xs text-red-500 mt-1 flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            1% bulan ini
                        </p>
                    </div>
                </div>


                <!-- Admin Aktif -->
                <div class="flex items-center justify-center px-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m-4-4h8m-8-4h8m-8-4h8" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-600 text-sm">Admin Aktif</p>
                        <h2 class="text-xl font-bold text-blue-600">9</h2>
                        <!-- Daftar Admin -->
                        <div class="flex mt-1">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2 first:ml-0"
                                src="https://randomuser.me/api/portraits/women/1.jpg" alt="Admin 1">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/men/2.jpg" alt="Admin 2">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/women/3.jpg" alt="Admin 3">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/men/4.jpg" alt="Admin 4">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/women/5.jpg" alt="Admin 5">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tabs -->
        <div class="flex border-b mb-4 mt-8 space-x-2">
            <button
                class="tab-btn px-4 py-2 text-gray-600 hover:text-gray-800 border-b-2 border-transparent transition-all duration-300"
                data-tab="bibit">Bibit</button>
            <button
                class="tab-btn px-4 py-2 text-gray-600 hover:text-gray-800 border-b-2 border-transparent transition-all duration-300"
                data-tab="kayu">Kayu</button>
        </div>

        <!-- Table Bibit -->
        <div id="table-bibit" class="bg-white shadow-md rounded-3xl p-3">
            <!-- Header Data Bibit dengan Search dan Sort -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Bibit</h2>

                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Input Pencarian -->
                    <div class="relative w-full md:w-auto">
                        <input type="text" placeholder="Cari"
                            class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                    </div>

                    <!-- Dropdown Sorting -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <select
                            class="ml-1 text-gray-800 font-semibold border bg-gray-100 px-2 py-1 rounded-lg w-full md:w-auto">
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs md:text-sm">
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
                                    <select
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm bg-green-300"
                                        data-id="{{ $item['id'] }}" onchange="updateBackground(this)">
                                        <option value="Penyemaian" {{ $item['status'] == 'Penyemaian' ? 'selected' : '' }}>
                                            Persemaian
                                        </option>
                                        <option value="Siap Tanam" {{ $item['status'] == 'Siap Tanam' ? 'selected' : '' }}>
                                            Siap Tanam
                                        </option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <a href="/detail-bibit/{{ $item['id'] }}"
                                        class="ml-1 bg-teal-300 text-teal-700 text-semibold px-3 py-1 rounded-lg border border-teal-700 inline-block">
                                        Lihat Selengkapnya
                                    </a>
                                </td>
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
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">40</button>
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&gt;</button>
                </div>
            </div>
        </div>

        <!-- Table Kayu -->
        <div id="table-kayu" class="bg-white shadow-md rounded-3xl p-3 hidden">
            <!-- Header Data Bibit dengan Search dan Sort -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Bibit</h2>

                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Input Pencarian -->
                    <div class="relative w-full md:w-auto">
                        <input type="text" placeholder="Cari"
                            class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                    </div>

                    <!-- Dropdown Sorting -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <select
                            class="ml-1 text-gray-800 font-semibold border bg-gray-100 px-2 py-1 rounded-lg w-full md:w-auto">
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>
                </div>
            </div>
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
                                    <select
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm bg-green-300"
                                        data-id="{{ $item['id'] }}" onchange="updateBackground(this)">
                                        <option value="Tersedia" {{ $item['status'] == 'Tersedia' ? 'selected' : '' }}>
                                            Tersedia
                                        </option>
                                        <option value="Siap Potong"
                                            {{ $item['status'] == 'Siap Potong' ? 'selected' : '' }}>
                                            Siap Potong
                                        </option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <a href="/detail-kayu/{{ $item['id'] }}"
                                        class="ml-1 bg-teal-300 text-teal-700 text-semibold px-3 py-1 rounded-lg border border-teal-700 inline-block">
                                        Lihat Selengkapnya
                                    </a>
                                </td>
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
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">40</button>
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&gt;</button>
                </div>
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
                console.log("Tab diklik:", tab); // Debugging

                if (tables[tab]) {
                    console.log("Menampilkan tabel:", tab); // Debugging

                    // Sembunyikan semua tabel
                    Object.values(tables).forEach(table => table?.classList.add("hidden"));

                    // Tampilkan tabel yang sesuai
                    tables[tab].classList.remove("hidden");
                } else {
                    console.log("Tabel tidak ditemukan:", tab);
                }

                // Ubah tampilan tombol tab
                tabButtons.forEach(btn => {
                    btn.classList.remove("border-gray-800", "text-gray-800");
                    btn.classList.add("text-gray-600", "border-transparent");
                });

                this.classList.add("border-gray-800", "text-gray-800");
                this.classList.remove("text-gray-600", "border-transparent");
            });
        });
    });

    function updateBackground(selectElement) {
        if (selectElement.value === "Tersedia") {
            selectElement.setAttribute("style", "background-color: green-400 !important;"); // Hijau
        } else if (selectElement.value === "Siap Potong") {
            selectElement.setAttribute("style", "background-color: #fde047 !important;"); // Kuning
        } else if (selectElement.value === "Siap Tanam") {
            selectElement.setAttribute("style", "background-color: #fde047 !important;"); // Kuning
        }
    }
    // Set warna awal saat halaman dimuat
    document.querySelectorAll(".status-dropdown").forEach(select => {
        updateBackground(select);
    });
</script>
