@extends('layouts.app')

@section('title', 'Manajemen Kayu & Bibit')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Halo Fitri 👋</h1>
        </div>

        <!-- Card Statistik -->
        <div class="bg-white p-6 rounded-3xl shadow-md mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:divide-x md:divide-gray-300">
                <!-- Total Bibit -->
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/bibit.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Bibit</p>
                        <h2 class="text-xl font-bold text-black-600">
                            {{ number_format(count($bibit ?? [])) }}
                        </h2>
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
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="flex items-center px-4 py-4 md:py-0">
                        <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                            <img src="/assets/images/kayu.svg" alt="New Icon" class="w-8 h-8">
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">Total Kayu</p>
                            <h2 class="text-xl font-bold text-black-600">{{ number_format(count($kayu ?? [])) }}</h2>
                            <p class="text-xs text-red-500 mt-1 flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                1% bulan ini
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Admin Aktif -->
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/admin.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Admin Aktif</p>
                        <h2 class="text-xl font-bold text-black-600">9</h2>
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
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Kayu</h2>

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
                            <th class="px-2 py-1 text-left">Batch Panen</th>
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
                                <td class="px-2 py-1">batch panen</td>
                                <td class="px-2 py-1">
                                    <select
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm bg-green-300"
                                        data-id="{{ $item['id'] }}" onchange="updateBackground(this)">
                                        <option value="Tersedia" {{ $item['status'] == 'Tersedia' ? 'selected' : '' }}>
                                            Tersedia
                                        </option>
                                        <option value="Kosong" {{ $item['status'] == 'Kosong' ? 'selected' : '' }}>
                                            Kosong
                                        </option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <<td class="px-2 py-1">
                                        <button
                                            class="ml-1 bg-teal-300 text-teal-700 text-semibold px-3 py-1 rounded-lg border border-teal-700 inline-block detail-btn"
                                            data-id="{{ $item['id'] }}" data-nama="{{ $item['nama'] }}"
                                            data-jenis="{{ $item['jenis'] }}" data-usia="{{ $item['usia'] }}"
                                            data-jumlah="{{ $item['jumlah'] }}" data-lokasi="{{ $item['lokasi'] }}"
                                            data-status="{{ $item['status'] }}">
                                            Lihat Selengkapnya
                                        </button>
                                </td>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Modal -->
            <div id="detailModal"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
                    <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-black">&times;</button>
                    <h2 class="text-xl font-bold mb-4">Detail Pohon/Bibit</h2>
                    <ul class="space-y-2 text-sm">
                        <li><strong>ID:</strong> <span id="detailId"></span></li>
                        <li><strong>Nama:</strong> <span id="detailNama"></span></li>
                        <li><strong>Jenis:</strong> <span id="detailJenis"></span></li>
                        <li><strong>Usia:</strong> <span id="detailUsia"></span></li>
                        <li><strong>Jumlah:</strong> <span id="detailJumlah"></span></li>
                        <li><strong>Lokasi:</strong> <span id="detailLokasi"></span></li>
                        <li><strong>Status:</strong> <span id="detailStatus"></span></li>
                    </ul>
                </div>
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
        } else if (selectElement.value === "Kosong") {
            selectElement.setAttribute("style", "background-color: #f48fb1 !important;"); // Kuning
        } else if (selectElement.value === "Siap Tanam") {
            selectElement.setAttribute("style", "background-color: #fde047 !important;"); // Kuning
        }
    }
    // Set warna awal saat halaman dimuat
    document.querySelectorAll(".status-dropdown").forEach(select => {
        updateBackground(select);
    });

    const detailButtons = document.querySelectorAll('.detail-btn');
    const modal = document.getElementById('detailModal');
    const closeModal = document.getElementById('closeModal');

    detailButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('detailId').textContent = btn.dataset.id;
            document.getElementById('detailNama').textContent = btn.dataset.nama;
            document.getElementById('detailJenis').textContent = btn.dataset.jenis;
            document.getElementById('detailUsia').textContent = btn.dataset.usia;
            document.getElementById('detailJumlah').textContent = btn.dataset.jumlah;
            document.getElementById('detailLokasi').textContent = btn.dataset.lokasi;
            document.getElementById('detailStatus').textContent = btn.dataset.status;
            modal.classList.remove('hidden');
        });
    });

    closeModal.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Optional: close when clicking outside the modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
</script>
