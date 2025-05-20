@extends('layouts.app')

@section('title', 'Manajemen Kayu & Bibit')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Hallo, {{ session('user_nama') }} 👋</h1>
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
                        <form action="{{ url()->current() }}" method="GET">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Cari nama, id, jenis..." id="searchBibit"
                                class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                            <input type="hidden" name="sort" value="{{ $sort ?? 'terbaru' }}">
                            <button type="submit" class="hidden">Search</button>
                        </form>
                    </div>

                    <!-- Dropdown Sorting -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <form id="sortBibitForm" action="{{ url()->current() }}" method="GET">
                            <input type="hidden" name="search" value="{{ $search ?? '' }}">
                            <select id="sortBibit" name="sort"
                                onchange="document.getElementById('sortBibitForm').submit()"
                                class="ml-1 text-gray-800 font-semibold border bg-gray-100 px-2 py-1 rounded-lg w-full md:w-auto">
                                <option value="terbaru" {{ ($sort ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru
                                </option>
                                <option value="terlama" {{ ($sort ?? '') == 'terlama' ? 'selected' : '' }}>Terlama
                                </option>
                            </select>
                        </form>
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
                                <td class="px-2 py-1">{{ $item['id_bibit'] ?? $item['id'] }}</td>
                                <td class="px-2 py-1">{{ $item['jenis_bibit'] }}</td>
                                <td class="px-2 py-1">{{ $item['tinggi'] }} cm</td>
                                <td class="px-2 py-1">{{ $item['lokasi'] }}</td>
                                <td class="px-2 py-1">
                                    <select
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm bg-green-300"
                                        data-id="{{ $item['id'] }}" onchange="updateBackground(this)">
                                        <option value="Penyemaian"
                                            {{ $item['status'] == 'Penyemaian' || $item['status'] == 'Sedang' ? 'selected' : '' }}>
                                            Penyemaian
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
                                        data-id="{{ $item['id'] }}" data-id-bibit="{{ $item['id_bibit'] ?? '' }}"
                                        data-jenis="{{ $item['jenis_bibit'] }}" data-tinggi="{{ $item['tinggi'] }}"
                                        data-lokasi="{{ $item['lokasi'] }}" data-status="{{ $item['status'] }}"
                                        data-usia="{{ $item['usia'] ?? '' }}"
                                        data-nama="{{ $item['nama_bibit'] ?? '' }}"
                                        data-varietas="{{ $item['varietas'] ?? '' }}"
                                        data-produktivitas="{{ $item['produktivitas'] ?? '' }}"
                                        data-asal="{{ $item['asal_bibit'] ?? '' }}"
                                        data-nutrisi="{{ $item['nutrisi'] ?? '' }}"
                                        data-media="{{ $item['media_tanam'] ?? '' }}"
                                        data-tanggal="{{ $item['tanggal_pembibitan'] ?? ($item['tanggal_pembibitanFl'] ?? ($item['tanggal_pembibitanSl'] ?? '')) }}"
                                        data-status-hama="{{ $item['status_hama'] ?? '' }}"
                                        data-catatan="{{ $item['catatan'] ?? '' }}"
                                        data-gambar="{{ $item['gambar_image'] ?? '' }}">
                                        Lihat Selengkapnya
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center">
                <div class="flex space-x-1">
                    @if ($currentPage > 1)
                        <a href="{{ url()->current() }}?page={{ $currentPage - 1 }}&search={{ $search ?? '' }}&sort={{ $sort ?? 'terbaru' }}"
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Prev
                        </a>
                    @endif

                    @for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                        <a href="{{ url()->current() }}?page={{ $i }}&search={{ $search ?? '' }}&sort={{ $sort ?? 'terbaru' }}"
                            class="px-3 py-1 {{ $i == $currentPage ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md hover:bg-green-700 hover:text-white">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($currentPage < $lastPage)
                        <a href="{{ url()->current() }}?page={{ $currentPage + 1 }}&search={{ $search ?? '' }}&sort={{ $sort ?? 'terbaru' }}"
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Next
                        </a>
                    @endif
                </div>
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
                        <form action="{{ url()->current() }}?tab=kayu" method="GET">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Cari nama, id, jenis..." id="searchKayu"
                                class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                            <input type="hidden" name="sort" value="{{ $sort ?? 'terbaru' }}">
                            <input type="hidden" name="tab" value="kayu">
                            <button type="submit" class="hidden">Search</button>
                        </form>
                    </div>

                    <!-- Dropdown Sorting -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <form id="sortKayuForm" action="{{ url()->current() }}" method="GET">
                            <input type="hidden" name="search" value="{{ $search ?? '' }}">
                            <input type="hidden" name="tab" value="kayu">
                            <select id="sortKayu" name="sort"
                                onchange="document.getElementById('sortKayuForm').submit()"
                                class="ml-1 text-gray-800 font-semibold border bg-gray-100 px-2 py-1 rounded-lg w-full md:w-auto">
                                <option value="terbaru" {{ ($sort ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru
                                </option>
                                <option value="terlama" {{ ($sort ?? '') == 'terlama' ? 'selected' : '' }}>Terlama
                                </option>
                            </select>
                        </form>
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
                                <td class="px-2 py-1">{{ $item['id_kayu'] ?? $item['id'] }}</td>
                                <td class="px-2 py-1">{{ $item['jenis_kayu'] }}</td>
                                <td class="px-2 py-1">{{ $item['tinggi'] }} meter</td>
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
                                        class="ml-1 bg-teal-300 text-teal-700 text-semibold px-3 py-1 rounded-lg border border-teal-700 inline-block kayu-detail-btn"
                                        data-id="{{ $item['id'] }}" data-id-kayu="{{ $item['id_kayu'] ?? '' }}"
                                        data-nama="{{ $item['nama_kayu'] ?? '' }}"
                                        data-jenis="{{ $item['jenis_kayu'] }}" data-usia="{{ $item['usia'] ?? '' }}"
                                        data-jumlah="{{ $item['tinggi'] }}" data-lokasi="{{ $item['lokasi'] }}"
                                        data-status="{{ $item['status'] }}" data-barcode="{{ $item['barcode'] ?? '' }}"
                                        data-batch="{{ $item['batch_panen'] ?? '' }}"
                                        data-stok="{{ $item['jumlah_stok'] ?? '' }}"
                                        data-varietas="{{ $item['varietas'] ?? '' }}"
                                        data-tanggal-lahir="{{ $item['tanggal_lahir_pohon'] ?? '' }}"
                                        data-tanggal-panen="{{ $item['tanggal_panen'] ?? '' }}"
                                        data-catatan="{{ $item['catatan'] ?? '' }}"
                                        data-gambar="{{ $item['gambar_image'] ?? '' }}">
                                        Lihat Selengkapnya
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Kayu -->
            <div class="mt-4 flex justify-center">
                <div class="flex space-x-1">
                    @if ($currentPage > 1)
                        <a href="{{ url()->current() }}?page={{ $currentPage - 1 }}&search={{ $search ?? '' }}&sort={{ $sort ?? 'terbaru' }}&tab=kayu"
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Prev
                        </a>
                    @endif

                    @for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                        <a href="{{ url()->current() }}?page={{ $i }}&search={{ $search ?? '' }}&sort={{ $sort ?? 'terbaru' }}&tab=kayu"
                            class="px-3 py-1 {{ $i == $currentPage ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md hover:bg-green-700 hover:text-white">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($currentPage < $lastPage)
                        <a href="{{ url()->current() }}?page={{ $currentPage + 1 }}&search={{ $search ?? '' }}&sort={{ $sort ?? 'terbaru' }}&tab=kayu"
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Next
                        </a>
                    @endif
                </div>
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
                        <input type="file" id="detail-kayu-gambar-upload" name="gambar_image" accept="image/*" style="margin-bottom: 10px;">

                        <!-- Fields below image -->
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Nama Kayu</label>
                            <input type="text" id="detail-nama" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Jenis Kayu</label>
                            <input type="text" id="detail-jenis" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Tanggal Tebang</label>
                            <input type="text" id="detail-tanggal-lahir" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Catatan</label>
                            <input type="text" id="detail-catatan" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>
                    </div>

                    <!-- Right Column Form Fields -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">ID Kayu</label>
                            <input type="text" id="detail-id-kayu" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                            <input type="hidden" id="detail-id" value="">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Barcode</label>
                            <input type="text" id="detail-barcode" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Varietas</label>
                            <input type="text" id="detail-varietas" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Tinggi</label>
                            <div class="flex items-center">
                                <input type="text" id="detail-tinggi" placeholder="Placeholder"
                                    class="w-full border border-gray-300 rounded-l px-3 py-2 text-sm" readonly>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r px-3 py-2 text-sm text-gray-600">
                                    meter
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Usia</label>
                            <div class="flex items-center">
                                <input type="text" id="detail-usia" placeholder="Placeholder"
                                    class="w-full border border-gray-300 rounded-l px-3 py-2 text-sm" readonly>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r px-3 py-2 text-sm text-gray-600">
                                    tahun
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Jumlah Stok</label>
                            <input type="text" id="detail-stok" placeholder="Placeholder"
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
                    <button id="save-kayu-btn"
                        class="bg-green-600 text-white px-5 py-2 rounded text-sm font-medium hover:bg-green-700"
                        style="display: none;">
                        Simpan
                    </button>
                    <button id="delete-kayu-btn"
                        class="bg-red-600 text-gray-700 px-5 py-2 rounded text-sm font-medium hover:bg-gray-300">
                        Hapus
                    </button>
                    <button id="edit-kayu-btn"
                        class="bg-yellow-600 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-500">
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
                        <input type="file" id="detail-bibit-gambar-upload" name="gambar_image" accept="image/*" style="margin-bottom: 10px;">

                        <!-- Fields below image -->
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1 mt-21">Nama Bibit</label>
                            <input type="text" id="detail-bibit-nama" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Tanggal Tanam</label>
                            <input type="text" id="detail-bibit-tanggal" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Produktivitas</label>
                            <input type="text" id="detail-bibit-produktivitas" placeholder="Placeholder"
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

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Catatan</label>
                            <input type="text" id="detail-bibit-catatan" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>
                    </div>

                    <!-- Right Column Form Fields -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">ID Bibit</label>
                            <input type="text" id="detail-bibit-id" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                            <input type="hidden" id="detail-bibit-actual-id" value="">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Jenis Bibit</label>
                            <input type="text" id="detail-bibit-jenis" placeholder="Placeholder"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" readonly>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Usia Bibit</label>
                            <div class="flex items-center">
                                <input type="text" id="detail-bibit-usia" placeholder="Placeholder"
                                    class="w-full border border-gray-300 rounded-l px-3 py-2 text-sm" readonly>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r px-3 py-2 text-sm text-gray-600">
                                    hari
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Tinggi</label>
                            <div class="flex items-center">
                                <input type="text" id="detail-bibit-tinggi" placeholder="Placeholder"
                                    class="w-full border border-gray-300 rounded-l px-3 py-2 text-sm" readonly>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r px-3 py-2 text-sm text-gray-600">
                                    cm
                                </span>
                            </div>
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
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Status Hama</label>
                            <input type="text" id="detail-bibit-status-hama" placeholder="Placeholder"
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
                    <button id="save-bibit-btn"
                        class="bg-green-600 text-white px-5 py-2 rounded text-sm font-medium hover:bg-green-700"
                        style="display: none;">
                        Simpan
                    </button>
                    <button id="delete-bibit-btn"
                        class="bg-red-600 text-white-700 px-5 py-2 rounded text-sm font-medium hover:bg-gray-300">
                        Hapus
                    </button>
                    <button id="edit-bibit-btn"
                        class="bg-yellow-400 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-500">
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
            // Global variables
            let isEditingBibit = false;
            let isEditingKayu = false;

            // Modal Elements
            const modalBibit = document.getElementById('modalDetailBibit');
            const modalKayu = document.getElementById('modalDetailKayu');

            // Button Elements
            const detailButtonsBibit = document.querySelectorAll('.bibit-detail-btn');
            const detailButtonsKayu = document.querySelectorAll('.kayu-detail-btn');
            const tutupModalBibit = document.getElementById('tutupModalBibit');
            const tutupModalKayu = document.getElementById('tutupModal');

            // Action Buttons
            const saveBibitBtn = document.getElementById('save-bibit-btn');
            const deleteBibitBtn = document.getElementById('delete-bibit-btn');
            const editBibitBtn = document.getElementById('edit-bibit-btn');

            const saveKayuBtn = document.getElementById('save-kayu-btn');
            const deleteKayuBtn = document.getElementById('delete-kayu-btn');
            const editKayuBtn = document.getElementById('edit-kayu-btn');

            // Handle Bibit detail buttons click
            detailButtonsBibit.forEach(button => {
                button.addEventListener('click', function() {
                    // Reset edit mode
                    isEditingBibit = false;
                    enableDisableBibitFields(false);

                    // Get data from data-* attributes
                    const id = this.dataset.id || '';
                    const idBibit = this.dataset.idBibit || '';
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
                    const produktivitas = this.dataset.produktivitas || '';
                    const statusHama = this.dataset.statusHama || '';
                    const catatan = this.dataset.catatan || '';
                    const gambar = this.dataset.gambar || 'https://via.placeholder.com/300x220';

                    // Fill data into the modal form
                    document.getElementById('detail-bibit-actual-id').value = id;
                    document.getElementById('detail-bibit-id').value = idBibit || id;
                    document.getElementById('detail-bibit-jenis').value = jenis;
                    document.getElementById('detail-bibit-usia').value = usia || 'Tidak tersedia';
                    document.getElementById('detail-bibit-tinggi').value = tinggi || 'Tidak tersedia';
                    document.getElementById('detail-bibit-lokasi').value = lokasi !== 'Tidak tersedia' ? lokasi : 'Tidak tersedia';
                    document.getElementById('detail-bibit-status').value = status;
                    document.getElementById('detail-bibit-nama').value = nama || 'Tidak tersedia';
                    document.getElementById('detail-bibit-tanggal').value = tanggal || 'Tidak tersedia';
                    document.getElementById('detail-bibit-varietas').value = varietas || 'Tidak tersedia';
                    document.getElementById('detail-bibit-asal').value = asal || 'Tidak tersedia';
                    document.getElementById('detail-bibit-nutrisi').value = nutrisi !== '-' ? nutrisi : 'Tidak tersedia';
                    document.getElementById('detail-bibit-media').value = media !== '-' ? media : 'Tidak tersedia';
                    document.getElementById('detail-bibit-produktivitas').value = produktivitas || 'Tidak tersedia';
                    document.getElementById('detail-bibit-status-hama').value = statusHama !== '-' ? statusHama : 'Tidak tersedia';
                    document.getElementById('detail-bibit-catatan').value = catatan !== '-' ? catatan : 'Tidak tersedia';

                    // Set the image if available
                    if (gambar && gambar !== '' && gambar !== 'https://via.placeholder.com/250') {
                        document.getElementById('detail-bibit-foto').src = gambar;
                    } else {
                        document.getElementById('detail-bibit-foto').src = 'https://via.placeholder.com/300x220';
                    }

                    // Show modal
                    modalBibit.classList.remove('hidden');
                    modalBibit.classList.add('flex');
                });
            });

            // Handle Kayu detail buttons click
            detailButtonsKayu.forEach(button => {
                button.addEventListener('click', function() {
                    // Reset edit mode
                    isEditingKayu = false;
                    enableDisableKayuFields(false);

                    // Get data from data-* attributes
                    const id = this.dataset.id || '';
                    const idKayu = this.dataset.idKayu || '';
                    const jenis = this.dataset.jenis || '';
                    const nama = this.dataset.nama || '';
                    const jumlah = this.dataset.jumlah || '';
                    const status = this.dataset.status || '';
                    const usia = this.dataset.usia || '';
                    const barcode = this.dataset.barcode || '';
                    const batch = this.dataset.batch || '';
                    const stok = this.dataset.stok || '';
                    const varietas = this.dataset.varietas || '';
                    const tanggalLahir = this.dataset.tanggalLahir || '';
                    const tanggalPanen = this.dataset.tanggalPanen || '';
                    const catatan = this.dataset.catatan || '';
                    const gambar = this.dataset.gambar || 'https://via.placeholder.com/300x220';

                    // Fill data into the modal form
                    document.getElementById('detail-id').value = id;
                    document.getElementById('detail-id-kayu').value = idKayu || id;
                    document.getElementById('detail-barcode').value = barcode !== 'Tidak tersedia' ? barcode : 'Tidak tersedia';
                    document.getElementById('detail-jenis').value = jenis;
                    document.getElementById('detail-tinggi').value = jumlah ? jumlah.replace(/[^0-9.]/g, '') : 'Tidak tersedia';
                    document.getElementById('detail-usia').value = usia ? usia.replace(/[^0-9.]/g, '') : 'Tidak tersedia';
                    document.getElementById('detail-stok').value = stok || 'Tidak tersedia';
                    document.getElementById('detail-varietas').value = varietas !== '-' ? varietas : 'Tidak tersedia';
                    document.getElementById('detail-kondisi').value = status;
                    document.getElementById('detail-nama').value = nama || 'Tidak tersedia';
                    document.getElementById('detail-tanggal-lahir').value = tanggalLahir !== 'Tidak tersedia' ? tanggalLahir : 'Tidak tersedia';
                    document.getElementById('detail-catatan').value = catatan !== '-' ? catatan : 'Tidak tersedia';

                    // Set the image if available
                    if (gambar && gambar !== '' && gambar !== 'https://via.placeholder.com/250') {
                        document.getElementById('detail-foto').src = gambar;
                    } else {
                        document.getElementById('detail-foto').src = 'https://via.placeholder.com/300x220';
                    }

                    // Show modal
                    modalKayu.classList.remove('hidden');
                    modalKayu.classList.add('flex');
                });
            });

            // Functions to enable/disable form fields
            function enableDisableBibitFields(enable) {
                const fields = modalBibit.querySelectorAll('input[type="text"]');
                fields.forEach(field => {
                    if (field.id !== 'detail-bibit-id') { // Keep ID field readonly
                        field.readOnly = !enable;
                        field.classList.toggle('bg-gray-100', !enable);
                    }
                });

                // Toggle button display
                saveBibitBtn.style.display = enable ? 'block' : 'none';
                editBibitBtn.textContent = enable ? 'Cancel' : 'Edit';
            }

            function enableDisableKayuFields(enable) {
                const fields = modalKayu.querySelectorAll('input[type="text"]');
                fields.forEach(field => {
                    if (field.id !== 'detail-id-kayu') { // Keep ID field readonly
                        field.readOnly = !enable;
                        field.classList.toggle('bg-gray-100', !enable);
                    }
                });

                // Toggle button display
                saveKayuBtn.style.display = enable ? 'block' : 'none';
                editKayuBtn.textContent = enable ? 'Cancel' : 'Edit';
            }

            // Edit button for Bibit
            editBibitBtn.addEventListener('click', function() {
                isEditingBibit = !isEditingBibit;
                enableDisableBibitFields(isEditingBibit);
            });

            // Save button for Bibit
            saveBibitBtn.addEventListener('click', function() {
                const id = document.getElementById('detail-bibit-actual-id').value;
                
                // Create FormData object
                const formData = new FormData();
                
                // Get numeric values and clean them
                const tinggi = parseInt(document.getElementById('detail-bibit-tinggi').value.replace(/[^0-9]/g, '')) || 0;
                const usia = parseInt(document.getElementById('detail-bibit-usia').value.replace(/[^0-9]/g, '')) || 0;
                
                // Append all form fields with proper data cleaning
                formData.append('id', id);
                formData.append('nama_bibit', document.getElementById('detail-bibit-nama').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('jenis_bibit', document.getElementById('detail-bibit-jenis').value.trim());
                formData.append('varietas', document.getElementById('detail-bibit-varietas').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('asal_bibit', document.getElementById('detail-bibit-asal').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('produktivitas', document.getElementById('detail-bibit-produktivitas').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('kondisi', document.getElementById('detail-bibit-status').value.trim());
                formData.append('media_tanam', document.getElementById('detail-bibit-media').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('nutrisi', document.getElementById('detail-bibit-nutrisi').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('status_hama', document.getElementById('detail-bibit-status-hama').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('catatan', document.getElementById('detail-bibit-catatan').value.replace(' Tidak tersedia', '').trim() || null);
                formData.append('tinggi', tinggi.toString());
                formData.append('usia', usia.toString());

                // Append image if selected
                const imageInput = document.getElementById('detail-bibit-gambar-upload');
                if (imageInput.files.length > 0) {
                    formData.append('gambar_image', imageInput.files[0]);
                }

                // Add CSRF token
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                // Show loading state
                saveBibitBtn.disabled = true;
                saveBibitBtn.textContent = 'Menyimpan...';

                // Send update request
                fetch('/bibit/update/' + id, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        // Do not set Content-Type header, let the browser set it for FormData
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                // Try to parse as JSON
                                return Promise.reject(JSON.parse(text));
                            } catch (e) {
                                // If not JSON, reject with text
                                return Promise.reject({ message: text });
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Data bibit berhasil diperbarui!');
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Gagal memperbarui data bibit');
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    alert('Terjadi kesalahan: ' + (error.message || 'Unknown error'));
                })
                .finally(() => {
                    // Reset button state
                    saveBibitBtn.disabled = false;
                    saveBibitBtn.textContent = 'Simpan';
                });
            });

            // Delete button for Bibit
            deleteBibitBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus bibit ini?')) {
                    const id = document.getElementById('detail-bibit-actual-id').value;

                    fetch('/bibit/delete/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Bibit berhasil dihapus!');
                            location.reload();
                        } else {
                            alert('Gagal menghapus bibit: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
                }
            });

            // Edit button for Kayu
            editKayuBtn.addEventListener('click', function() {
                isEditingKayu = !isEditingKayu;
                enableDisableKayuFields(isEditingKayu);
            });

            // Save button for Kayu
            saveKayuBtn.addEventListener('click', function() {
                const id = document.getElementById('detail-id').value;
                
                // Create FormData object
                const formData = new FormData();
                
                // Get all the updated values and clean them
                const tinggi = document.getElementById('detail-tinggi').value.replace(/[^0-9.]/g, '');
                const usia = document.getElementById('detail-usia').value.replace(/[^0-9]/g, '');
                const jumlahStok = document.getElementById('detail-stok').value.replace(/[^0-9]/g, '');
                
                // Prepare the data object to match Firestore structure
                const kayuData = {
                    id: id,
                    id_kayu: document.getElementById('detail-id-kayu').value.trim(),
                    nama_kayu: document.getElementById('detail-nama').value.replace(' Tidak tersedia', '').trim() || null,
                    jenis_kayu: document.getElementById('detail-jenis').value.trim(),
                    varietas: document.getElementById('detail-varietas').value.replace(' Tidak tersedia', '').trim() || null,
                    barcode: document.getElementById('detail-barcode').value.replace(' Tidak tersedia', '').trim() || null,
                    catatan: document.getElementById('detail-catatan').value.replace(' Tidak tersedia', '').trim() || null,
                    tinggi: parseFloat(tinggi) || 0,
                    usia: parseInt(usia) || 0,
                    jumlah_stok: parseInt(jumlahStok) || 0,
                    status: document.getElementById('detail-kondisi').value.trim()
                };

                // Log data yang akan dikirim
                console.log('Data yang akan dikirim:', kayuData);

                // Append the stringified data to FormData
                formData.append('data', JSON.stringify(kayuData));

                // Append image if selected
                const imageInput = document.getElementById('detail-kayu-gambar-upload');
                if (imageInput.files.length > 0) {
                    formData.append('gambar_image', imageInput.files[0]);
                }

                // Add CSRF token
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('_method', 'PUT');

                // Show loading state
                saveKayuBtn.disabled = true;
                saveKayuBtn.textContent = 'Menyimpan...';

                // Send update request
                fetch('/kayu/update/' + id, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                return Promise.reject(JSON.parse(text));
                            } catch (e) {
                                return Promise.reject({ message: text });
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update the display values immediately
                        document.getElementById('detail-tinggi').value = tinggi;
                        document.getElementById('detail-usia').value = usia;
                        document.getElementById('detail-stok').value = jumlahStok;
                        
                        // Close modal and refresh page
                        modalKayu.classList.add('hidden');
                        modalKayu.classList.remove('flex');
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Gagal memperbarui data kayu');
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    alert('Terjadi kesalahan: ' + (error.message || 'Unknown error'));
                })
                .finally(() => {
                    // Reset button state
                    saveKayuBtn.disabled = false;
                    saveKayuBtn.textContent = 'Simpan';
                });
            });

            // Delete button for Kayu
            deleteKayuBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus kayu ini?')) {
                    const id = document.getElementById('detail-id').value;

                    fetch('/kayu/delete/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Kayu berhasil dihapus!');
                            location.reload();
                        } else {
                            alert('Gagal menghapus kayu: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
                }
            });

            // Handle tab switching
            const tabButtons = document.querySelectorAll(".tab-btn");
            const tables = {
                bibit: document.getElementById("table-bibit"),
                kayu: document.getElementById("table-kayu"),
            };

            // Set default active tab or use URL param if available
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');

            if (tabParam === 'kayu') {
                document.querySelector('.tab-btn[data-tab="kayu"]').classList.add("border-gray-800", "text-gray-800");
                document.querySelector('.tab-btn[data-tab="bibit"]').classList.add("text-gray-600", "border-transparent");
                tables.bibit.classList.add('hidden');
                tables.kayu.classList.remove('hidden');
            } else {
                document.querySelector('.tab-btn[data-tab="bibit"]').classList.add("border-gray-800", "text-gray-800");
                document.querySelector('.tab-btn[data-tab="kayu"]').classList.add("text-gray-600", "border-transparent");
            }

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

                    // Update URL with current tab
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tab);
                    window.history.replaceState({}, '', url);
                });
            });

            // Close modal Bibit
            if (tutupModalBibit) {
                tutupModalBibit.addEventListener('click', function() {
                    modalBibit.classList.add('hidden');
                    modalBibit.classList.remove('flex');
                });
            }

            // Close modal Kayu
            if (tutupModalKayu) {
                tutupModalKayu.addEventListener('click', function() {
                    modalKayu.classList.add('hidden');
                    modalKayu.classList.remove('flex');
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

            // Set background color for status dropdowns
            document.querySelectorAll(".status-dropdown").forEach(select => {
                updateBackground(select);
            });

            // Fungsi untuk mengupdate status bibit dan kayu
            const statusDropdowns = document.querySelectorAll('.status-dropdown');
            statusDropdowns.forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const status = this.value;
                    const isBibit = this.closest('table').id === "table-bibit";

                    // Tentukan URL berdasarkan tabel (Bibit atau Kayu)
                    const url = isBibit ? '/bibit/update-status' : '/kayu/update-status';

                    // Tampilkan loading state
                    const originalValue = this.value;
                    this.disabled = true;
                    this.style.opacity = '0.5';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            id,
                            status
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update background color
                            updateBackground(this);
                            // Show success message
                            const message = isBibit ? 'Status bibit berhasil diperbarui' : 'Status kayu berhasil diperbarui';
                            alert(message);
                        } else {
                            throw new Error(data.message || 'Gagal memperbarui status');
                        }
                    })
                    .catch(error => {
                        // Revert to original value on error
                        this.value = originalValue;
                        updateBackground(this);
                        alert('Terjadi kesalahan: ' + error.message);
                    })
                    .finally(() => {
                        // Reset loading state
                        this.disabled = false;
                        this.style.opacity = '1';
                    });
                });
            });

            // Preview gambar baru Bibit
            const bibitFileInput = document.getElementById('detail-bibit-gambar-upload');
            bibitFileInput.addEventListener('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    document.getElementById('detail-bibit-foto').src = URL.createObjectURL(file);
                }
            });
            // Preview gambar baru Kayu
            const kayuFileInput = document.getElementById('detail-kayu-gambar-upload');
            kayuFileInput.addEventListener('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    document.getElementById('detail-foto').src = URL.createObjectURL(file);
                }
            });
        });

        function updateBackground(selectElement) {
            if (selectElement.value === "Tersedia") {
                selectElement.style.backgroundColor = "#4ade80"; // Green
            } else if (selectElement.value === "Kosong") {
                selectElement.style.backgroundColor = "#f48fb1"; // Pink
            } else if (selectElement.value === "Siap Tanam") {
                selectElement.style.backgroundColor = "#fde047"; // Yellow
            } else if (selectElement.value === "Pernyemaian" || selectElement.value === "Penyemaian") {
                selectElement.style.backgroundColor = "#4ade80"; // Green
            }
        }
    </script>
@endsection
