@extends('layouts.app')

@section('title', 'Manajemen Kayu & Bibit')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">{{ session('user_nama') }} 👋</h1>
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
                            {{ number_format($totalBibit) }}
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
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/kayu.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Kayu</p>
                        <h2 class="text-xl font-bold text-black-600">{{ number_format($totalKayu) }}</h2>
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
                        <input type="text" placeholder="Cari" id="searchBibit"
                            class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                    </div>

                    <!-- Dropdown Sorting -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <select id="sortBibit"
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
                            <th class="px-2 py-1 text-left">Tinggi</th>
                            <th class="px-2 py-1 text-left">Lokasi</th>
                            <th class="px-2 py-1 text-left">Status</th>
                            <th class="px-2 py-1 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bibit as $item)
                            <tr class="border-b">
                                <td class="px-2 py-1">{{ $item['id'] }}</td>
                                <td class="px-2 py-1">{{ $item['jenis_bibit'] }}</td>
                                <td class="px-2 py-1">{{ $item['tinggi'] }} cm</td>
                                <td class="px-2 py-1">{{ $item['lokasi'] }}</td>
                                <td class="px-2 py-1">
                                    <select
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm bg-green-300"
                                        data-id="{{ $item['id'] }}" onchange="updateBackground(this)">
                                        <option value="Penyemaian"
                                            {{ $item['status'] == 'Penyemaian' || $item['status'] == 'Sedang' ? 'selected' : '' }}>
                                            Persemaian
                                        </option>
                                        <option value="Siap Tanam"
                                            {{ $item['status'] == 'Siap Tanam' ? 'selected' : '' }}>
                                            Siap Tanam
                                        </option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <button
                                        class="ml-1 bg-teal-300 text-teal-700 text-semibold px-3 py-1 rounded-lg border border-teal-700 inline-block bibit-detail-btn"
                                        data-id="{{ $item['id'] }}" data-jenis="{{ $item['jenis_bibit'] }}"
                                        data-tinggi="{{ $item['tinggi'] }}" data-lokasi="{{ $item['lokasi'] }}"
                                        data-status="{{ $item['status'] }}" data-usia="{{ $item['usia'] ?? '' }}"
                                        data-nama="{{ $item['nama_bibit'] ?? '' }}"
                                        data-varietas="{{ $item['varietas'] ?? '' }}"
                                        data-produktivitas="{{ $item['produktivitas'] ?? '' }}"
                                        data-asal="{{ $item['asal_bibit'] ?? '' }}"
                                        data-nutrisi="{{ $item['nutrisi'] ?? '' }}"
                                        data-media="{{ $item['media_tanam'] ?? '' }}"
                                        data-gambar="{{ $item['gambar_image'] ?? '' }}">
                                        Lihat Selengkapnya
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

        <!-- Table Kayu -->
        <div id="table-kayu" class="bg-white shadow-md rounded-3xl p-3 hidden">
            <!-- Header Data Kayu dengan Search dan Sort -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Kayu</h2>

                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Input Pencarian -->
                    <div class="relative w-full md:w-auto">
                        <input type="text" placeholder="Cari" id="searchKayu"
                            class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                    </div>

                    <!-- Dropdown Sorting -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <select id="sortKayu"
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
                            <th class="px-2 py-1 text-left">ID Kayu</th>
                            <th class="px-2 py-1 text-left">Jenis Kayu</th>
                            <th class="px-2 py-1 text-left">Tinggi</th>
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
                                <td class="px-2 py-1">{{ $item['jenis_kayu'] }}</td>
                                <td class="px-2 py-1">{{ $item['tinggi'] }} Batang</td>
                                <td class="px-2 py-1">{{ $item['lokasi'] }}</td>
                                <td class="px-2 py-1">{{ $item['batch_panen'] ?? 'Tidak tersedia' }}</td>
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
                                    <button
                                        class="ml-1 bg-teal-300 text-teal-700 text-semibold px-3 py-1 rounded-lg border border-teal-700 inline-block detail-btn"
                                        data-id="{{ $item['id'] }}" data-nama="{{ $item['nama'] ?? '' }}"
                                        data-jenis="{{ $item['jenis_kayu'] }}" data-usia="{{ $item['usia'] ?? '' }}"
                                        data-jumlah="{{ $item['tinggi'] }}" data-lokasi="{{ $item['lokasi'] }}"
                                        data-status="{{ $item['status'] }}" data-barcode="{{ $item['barcode'] ?? '' }}"
                                        data-batch="{{ $item['batch_panen'] ?? '' }}"
                                        data-tanggal="{{ $item['tanggal_lahir_pohon'] ?? '' }}"
                                        data-gambar="{{ $item['gambar_image'] ?? '' }}">
                                        Lihat Selengkapnya
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Detail Data Kayu -->
        <div id="modalDetailKayu" class="fixed inset-0 bg-black bg-opacity-5 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-xl p-6 relative">
                <!-- Close button -->
                <button id="tutupModal"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-xl">&times;</button>

                <!-- Title -->
                <h2 class="text-xl font-bold text-center text-amber-800 mb-5">Detail Data Kayu</h2>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- Left Column with Image and Fields -->
                    <div class="space-y-3">
                        <!-- Wood Image -->
                        <img id="detail-foto" src="https://via.placeholder.com/300x220" alt="Foto Kayu"
                            class="w-full h-52 object-cover mb-3">

                        <!-- Fields below image -->
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Penanggung Jawab</label>
                            <input type="text" id="detail-penanggung" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Tanggal Kayu</label>
                            <input type="text" id="detail-tanggal" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>
                    </div>

                    <!-- Right Column Form Fields -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">ID Barcode</label>
                            <input type="text" id="detail-id" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Jenis Kayu</label>
                            <input type="text" id="detail-jenis" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Volume</label>
                            <input type="text" id="detail-volume" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Diameter</label>
                            <input type="text" id="detail-diameter" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Panjang</label>
                            <input type="text" id="detail-panjang" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Kondisi Kayu</label>
                            <input type="text" id="detail-kondisi" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-2">
                    <button class="bg-green-600 text-white px-5 py-2 rounded text-sm font-medium hover:bg-green-700">
                        Simpan
                    </button>
                    <button class="bg-gray-200 text-gray-700 px-5 py-2 rounded text-sm font-medium hover:bg-gray-300">
                        Hapus
                    </button>
                    <button class="bg-gray-400 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-500">
                        Edit
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Detail Data Bibit -->
        <div id="modalDetailBibit" class="fixed inset-0 bg-black bg-opacity-5 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-xl p-6 relative">
                <!-- Close button -->
                <button id="tutupModalBibit"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-xl">&times;</button>

                <!-- Title -->
                <h2 class="text-xl font-bold text-center text-green-800 mb-5">Detail Data Bibit</h2>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- Left Column with Image and Fields -->
                    <div class="space-y-3">
                        <!-- Seedling Image -->
                        <img id="detail-bibit-foto" src="https://via.placeholder.com/300x220" alt="Foto Bibit"
                            class="w-full h-52 object-cover mb-3">

                        <!-- Fields below image -->
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1 mt-21">Nama Bibit</label>
                            <input type="text" id="detail-bibit-penanggung" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Tanggal Tanam</label>
                            <input type="text" id="detail-bibit-tanggal" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Nutrisi</label>
                            <input type="text" id="detail-bibit-nutrisi" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Media Tanam</label>
                            <input type="text" id="detail-bibit-media" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>
                    </div>

                    <!-- Right Column Form Fields -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">ID Bibit</label>
                            <input type="text" id="detail-bibit-id" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Jenis Bibit</label>
                            <input type="text" id="detail-bibit-jenis" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Usia Bibit</label>
                            <input type="text" id="detail-bibit-usia" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Tinggi</label>
                            <input type="text" id="detail-bibit-tinggi" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Lokasi</label>
                            <input type="text" id="detail-bibit-lokasi" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Status</label>
                            <input type="text" id="detail-bibit-status" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Varietas</label>
                            <input type="text" id="detail-bibit-varietas" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Asal Bibit</label>
                            <input type="text" id="detail-bibit-asal" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-2">
                    <button class="bg-green-600 text-white px-5 py-2 rounded text-sm font-medium hover:bg-green-700">
                        Simpan
                    </button>
                    <button class="bg-gray-200 text-gray-700 px-5 py-2 rounded text-sm font-medium hover:bg-gray-300">
                        Hapus
                    </button>
                    <button class="bg-gray-400 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-500">
                        Edit
                    </button>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalBibit = document.getElementById('modalDetailBibit');
            const detailButtonsBibit = document.querySelectorAll('.bibit-detail-btn');
            const tutupModalBibit = document.getElementById('tutupModalBibit');

            // Handle Bibit detail buttons click
            detailButtonsBibit.forEach(button => {
                button.addEventListener('click', function() {
                    // Get data from data-* attributes
                    const id = this.dataset.id || '';
                    const jenis = this.dataset.jenis || '';
                    const tinggi = this.dataset.tinggi || '';
                    const lokasi = this.dataset.lokasi || '';
                    const status = this.dataset.status || '';
                    const usia = this.dataset.usia || '';
                    const nama = this.dataset.nama || '';
                    const tanggal = this.dataset.tanggal || '';
                    const varietas = this.dataset.varietas || '';
                    const asal = this.dataset.asal || '';
                    const nutrisi = this.dataset.nutrisi || '';
                    const media = this.dataset.media || '';
                    const gambar = this.dataset.gambar || 'https://via.placeholder.com/300x220';

                    // Fill data into the modal form
                    document.getElementById('detail-bibit-id').value = id;
                    document.getElementById('detail-bibit-jenis').value = jenis;
                    document.getElementById('detail-bibit-usia').value = usia ? usia + ' tahun' :
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-tinggi').value = tinggi ? tinggi + ' cm' :
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-lokasi').value = lokasi;
                    document.getElementById('detail-bibit-status').value = status;
                    document.getElementById('detail-bibit-penanggung').value = nama ||
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-tanggal').value = tanggal ||
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-varietas').value = varietas ||
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-asal').value = asal ||
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-nutrisi').value = nutrisi ||
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-media').value = media ||
                        'Data tidak tersedia';

                    // Set the image if available
                    if (gambar && gambar !== '') {
                        document.getElementById('detail-bibit-foto').src = gambar;
                    } else {
                        document.getElementById('detail-bibit-foto').src =
                            'https://via.placeholder.com/300x220';
                    }

                    // Show modal
                    modalBibit.classList.remove('hidden');
                    modalBibit.classList.add('flex');
                });
            });

            // Close modal Bibit
            if (tutupModalBibit) {
                tutupModalBibit.addEventListener('click', function() {
                    modalBibit.classList.add('hidden');
                    modalBibit.classList.remove('flex');
                });
            }

            // Close modal Bibit when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modalBibit) {
                    modalBibit.classList.add('hidden');
                    modalBibit.classList.remove('flex');
                }
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Kayu Modal
            const modalKayu = document.getElementById('modalDetailKayu');
            const detailButtonsKayu = document.querySelectorAll('.detail-btn');
            const tutupModalKayu = document.getElementById('tutupModal');

            // Bibit Modal
            const modalBibit = document.getElementById('modalDetailBibit');
            const detailButtonsBibit = document.querySelectorAll('.bibit-detail-btn');
            const tutupModalBibit = document.getElementById('tutupModalBibit');

            // Handle Kayu detail buttons click
            detailButtonsKayu.forEach(button => {
                button.addEventListener('click', function() {
                    // Get data from data-* attributes
                    const id = this.dataset.id || '';
                    const jenis = this.dataset.jenis || '';
                    const nama = this.dataset.nama || '';
                    const jumlah = this.dataset.jumlah || '';
                    const lokasi = this.dataset.lokasi || '';
                    const status = this.dataset.status || '';
                    const usia = this.dataset.usia || '';
                    const barcode = this.dataset.barcode || '';
                    const batch = this.dataset.batch || '';
                    const tanggal = this.dataset.tanggal || '';
                    const gambar = this.dataset.gambar || 'https://via.placeholder.com/300x220';

                    // Fill data into the modal form
                    document.getElementById('detail-id').value = barcode || id;
                    document.getElementById('detail-jenis').value = jenis;
                    document.getElementById('detail-volume').value = jumlah + ' Batang';
                    document.getElementById('detail-diameter').value = 'Data tidak tersedia';
                    document.getElementById('detail-panjang').value = 'Data tidak tersedia';
                    document.getElementById('detail-kondisi').value = status;
                    document.getElementById('detail-penanggung').value = nama ||
                        'Data tidak tersedia';
                    document.getElementById('detail-tanggal').value = tanggal ||
                        'Data tidak tersedia';

                    // Set the image if available
                    if (gambar && gambar !== '') {
                        document.getElementById('detail-foto').src = gambar;
                    }

                    // Show modal
                    modalKayu.classList.remove('hidden');
                    modalKayu.classList.add('flex');
                });
            });

            // Handle Bibit detail buttons click
            detailButtonsBibit.forEach(button => {
                button.addEventListener('click', function() {
                    // Get data from data-* attributes
                    const id = this.dataset.id || '';
                    const jenis = this.dataset.jenis || '';
                    const tinggi = this.dataset.tinggi || '';
                    const lokasi = this.dataset.lokasi || '';
                    const status = this.dataset.status || '';
                    const usia = this.dataset.usia || '';
                    const nama = this.dataset.nama || '';
                    const tanggal = this.dataset.tanggal || '';
                    const varietas = this.dataset.varietas || '';
                    const asal = this.dataset.asal || '';
                    const nutrisi = this.dataset.nutrisi || '';
                    const media = this.dataset.media || '';
                    const gambar = this.dataset.gambar || 'https://via.placeholder.com/300x220';

                    // Fill data into the modal form
                    document.getElementById('detail-bibit-id').value = id;
                    document.getElementById('detail-bibit-jenis').value = jenis;
                    document.getElementById('detail-bibit-usia').value = usia ? usia + ' hari' :
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-tinggi').value = tinggi ? tinggi + ' cm' :
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-lokasi').value = lokasi;
                    document.getElementById('detail-bibit-status').value = status;
                    document.getElementById('detail-bibit-penanggung').value = produktivitas ||
                        'Data tidak tersedia';
                    document.getElementById('detail-bibit-tanggal').value = tanggal ||
                        'Data tidak tersedia';

                    // Set the image if available
                    if (gambar && gambar !== '') {
                        document.getElementById('detail-bibit-foto').src = gambar;
                    }

                    // Show modal
                    modalBibit.classList.remove('hidden');
                    modalBibit.classList.add('flex');
                });
            });


            // Close modal Kayu
            if (tutupModalKayu) {
                tutupModalKayu.addEventListener('click', function() {
                    modalKayu.classList.add('hidden');
                    modalKayu.classList.remove('flex');
                });
            }

            // Close modal Bibit
            if (tutupModalBibit) {
                tutupModalBibit.addEventListener('click', function() {
                    modalBibit.classList.add('hidden');
                    modalBibit.classList.remove('flex');
                });
            }

            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modalKayu) {
                    modalKayu.classList.add('hidden');
                    modalKayu.classList.remove('flex');
                }
                if (event.target === modalBibit) {
                    modalBibit.classList.add('hidden');
                    modalBibit.classList.remove('flex');
                }
            });

            // Initialize tab behavior
            const tabButtons = document.querySelectorAll(".tab-btn");
            const tables = {
                bibit: document.getElementById("table-bibit"),
                kayu: document.getElementById("table-kayu"),
            };

            // Set default active tab (bibit)
            document.querySelector('.tab-btn[data-tab="bibit"]').classList.add("border-gray-800", "text-gray-800");

            tabButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const tab = this.getAttribute("data-tab");

                    if (tables[tab]) {
                        // Hide all tables
                        Object.values(tables).forEach(table => table?.classList.add("hidden"));

                        // Show selected table
                        tables[tab].classList.remove("hidden");
                    }

                    // Update tab button appearance
                    tabButtons.forEach(btn => {
                        btn.classList.remove("border-gray-800", "text-gray-800");
                        btn.classList.add("text-gray-600", "border-transparent");
                    });

                    this.classList.add("border-gray-800", "text-gray-800");
                    this.classList.remove("text-gray-600", "border-transparent");
                });
            });

            // Set background color for status dropdowns
            document.querySelectorAll(".status-dropdown").forEach(select => {
                updateBackground(select);
            });
        });

        function updateBackground(selectElement) {
            if (selectElement.value === "Tersedia") {
                selectElement.style.backgroundColor = "#4ade80"; // Green
            } else if (selectElement.value === "Kosong") {
                selectElement.style.backgroundColor = "#f48fb1"; // Pink
            } else if (selectElement.value === "Siap Tanam") {
                selectElement.style.backgroundColor = "#fde047"; // Yellow
            } else if (selectElement.value === "Persemaian" || selectElement.value === "Penyemaian") {
                selectElement.style.backgroundColor = "#4ade80"; // Green
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk mengupdate status bibit dan kayu
            const statusDropdowns = document.querySelectorAll('.status-dropdown');
            statusDropdowns.forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const status = this.value;

                    // Tentukan URL berdasarkan tabel (Bibit atau Kayu)
                    const url = this.closest('table').id === "table-bibit" ?
                        '/bibit/update-status' : '/kayu/update-status';

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                id,
                                status
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Status berhasil diperbarui');
                            } else {
                                alert('Gagal memperbarui status');
                            }
                        })
                        .catch(error => alert('Terjadi kesalahan'));
                });
            });
        });
    </script>
@endsection
