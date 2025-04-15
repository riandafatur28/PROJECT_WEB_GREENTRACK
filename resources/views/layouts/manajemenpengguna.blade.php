@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <!-- Hamburger dan teks Halo Fitri dalam satu baris -->
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Halo Fitri 👋</h1>
        </div>

        <div class="bg-white p-4 rounded-3xl shadow-md text-left w-56">
            <div class="flex items-center">
                <!-- Ikon di sebelah kiri -->
                <div class="bg-green-100 p-3 rounded-full flex items-center justify-center">
                    <img src="/assets/images/admin.svg" alt="New Icon" class="w-8 h-8">
                </div>

                <!-- Teks di sebelah kanan ikon -->
                <div class="ml-4 flex flex-col items-center">
                    <p class="text-gray-500 text-sm">Sedang Aktif</p>
                    <p class="text-3xl font-semibold text-center w-full">9</p>

                    <!-- Daftar gambar pengguna berada di bawah angka -->
                    <div class="flex mt-2">
                        <img class="w-6 h-6 rounded-full border-2 border-white -ml-2 first:ml-0"
                            src="https://randomuser.me/api/portraits/women/1.jpg" alt="User 1">
                        <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                            src="https://randomuser.me/api/portraits/men/2.jpg" alt="User 2">
                        <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                            src="https://randomuser.me/api/portraits/women/3.jpg" alt="User 3">
                        <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                            src="https://randomuser.me/api/portraits/men/4.jpg" alt="User 4">
                        <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                            src="https://randomuser.me/api/portraits/women/5.jpg" alt="User 5">
                    </div>
                </div>
            </div>
        </div>


        <div id="table-admin" class="bg-white shadow-md rounded-3xl p-3 mt-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Admin</h2>

                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Input Pencarian -->
                    <div class="relative w-full md:w-auto transition-all duration-300 peer-checked:hidden">
                        <label for="sidebarToggle" class="absolute"></label> <!-- Tambahkan peer -->
                        <input type="text" placeholder="Cari"
                            class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan Berdasarkan :</label>
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
                            <th class="px-2 py-1 text-left">ID</th>
                            <th class="px-2 py-1 text-left">Nama</th>
                            <th class="px-2 py-1 text-left">Email</th>
                            <th class="px-2 py-1 text-left">Peran Admin</th>
                            <th class="px-2 py-1 text-left">Status Akun</th>
                            <th class="px-2 py-1 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admin as $init)
                            <tr class="border-b">
                                <td class="px-2 py-1">{{ $init['id'] }}</td>
                                <td class="px-2 py-1">{{ $init['nama'] }}</td>
                                <td class="px-2 py-1">{{ $init['email'] }}</td>
                                <td class="px-2 py-1">{{ $init['peran_admin'] }}</td>
                                <td class="px-2 py-1">
                                    <select
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm {{ $init['status'] == 'Aktif' ? 'bg-green-300' : 'bg-red-300' }}"
                                        data-id="{{ $init['id'] }}" onchange="updateBackground(this)">
                                        <option value="Aktif" {{ $init['status'] == 'Aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="Nonaktif" {{ $init['status'] == 'Nonaktif' ? 'selected' : '' }}>
                                            Nonaktif</option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <a href="/detail-admin/{{ $init['id'] }}"
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
                    <button class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">40</button>
                    <button
                        class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&gt;</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function updateBackground(selectElement) {
        if (selectElement.value === "Aktif") {
            selectElement.style.backgroundColor = "#86efac"; // Hijau
        } else {
            selectElement.style.backgroundColor = "#f87171"; // Merah
        }
    }

    document.querySelectorAll(".status-dropdown").forEach(select => {
        updateBackground(select);
    });
</script>
