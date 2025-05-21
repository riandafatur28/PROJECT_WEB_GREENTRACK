@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Halo, {{ session('user_nama') }} 👋</h1>
        </div>

        <div id="table-admin" class="bg-white shadow-md rounded-3xl p-3 mt-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Admin</h2>
                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <button onclick="showAddAdminModal()"
                        class="flex items-center bg-blue-50 text-gray-500 px-4 py-1.5 rounded-lg font-semibold hover:bg-blue-200 transition-colors duration-300 w-full md:w-auto">
                        <span class="mr-2 text-lg">+</span>
                        <span>Tambahkan Admin</span>
                    </button>

                    <!-- Search and Sort Form -->
                    <div class="bg-white p-4 rounded-xl shadow-sm mb-6">
                        <form method="GET" action="{{ url()->current() }}">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Search Input -->
                                <div class="relative w-full md:w-1/2">
                                    <input type="text" name="search" value="{{ $search ?? '' }}"
                                        placeholder="Cari nama, email, atau peran admin..."
                                        class="pl-10 pr-3 py-2 w-full border rounded-lg bg-green-50 text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>

                                <!-- Sort and Filter Controls -->
                                <div class="flex gap-4">
                                    <div>
                                        <select name="sort"
                                            class="border rounded-lg bg-green-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-300">
                                            <option value="terbaru"
                                                {{ ($sortOrder ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru
                                            </option>
                                            <option value="terlama" {{ ($sortOrder ?? '') == 'terlama' ? 'selected' : '' }}>
                                                Terlama</option>
                                        </select>
                                    </div>

                                    <button type="submit"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Filter
                                    </button>

                                    @if (!empty($search) || ($sortOrder ?? '') != 'terbaru')
                                        <a href="{{ url()->current() }}"
                                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs md:text-sm mt-8">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-2 py-1 text-left">ID Admin</th>
                            <th class="px-2 py-1 text-left">Nama</th>
                            <th class="px-2 py-1 text-left">Email</th>
                            <th class="px-2 py-1 text-left">Peran Admin</th>
                            <th class="px-2 py-1 text-left">Status Akun</th>
                            <th class="px-2 py-1 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="adminTableBody">
                        @foreach ($admin as $init)
                            <tr class="border-b">
                                <td class="px-2 py-1">{{ $init['id'] }}</td>
                                <td class="px-2 py-1">{{ $init['nama'] }}</td>
                                <td class="px-2 py-1">{{ $init['email'] }}</td>
                                <td class="px-2 py-1">{{ $init['peran_admin'] }}</td>
                                <td class="px-2 py-1">
                                    <select
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm {{ $init['status'] == 'Aktif' ? 'bg-green-300' : 'bg-red-300' }}"
                                        data-id="{{ $init['id'] }}" onchange="updateStatus(this)">
                                        <option value="Aktif" {{ $init['status'] == 'Aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="Nonaktif" {{ $init['status'] == 'Nonaktif' ? 'selected' : '' }}>
                                            Nonaktif</option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <button onclick="showAdminDetail(this)"
                                        class="ml-1 bg-teal-300 text-teal-700 font-semibold px-3 py-1 rounded-lg border border-teal-700"
                                        data-id="{{ $init['id'] }}" data-nama="{{ $init['nama'] }}"
                                        data-email="{{ $init['email'] }}" data-peran="{{ $init['peran_admin'] }}"
                                        data-status="{{ $init['status'] }}" data-photo="{{ $init['photo_url'] }}">
                                        Lihat Selengkapnya
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-between mt-8">
                    <p class="text-sm text-gray-500">Menampilkan data {{ ($currentPage - 1) * $perPage + 1 }} hingga
                        {{ min($currentPage * $perPage, $total) }} dari {{ $total }} entri</p>
                    <div class="flex space-x-1">
                        <!-- Previous Page Button -->
                        @if ($currentPage > 1)
                            <a href="{{ route('manajemenpengguna.index', ['page' => $currentPage - 1, 'search' => request('search')]) }}"
                                class="px-3 py-1 border border-gray-300 rounded text-sm {{ $currentPage == 1 ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50' }}">
                                ‹
                            </a>
                        @endif

                        <!-- Page Numbers -->
                        @php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($startPage + 4, $totalPages);

                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                $startPage = max(1, $endPage - 4);
                            }
                        @endphp

                        @for ($i = $startPage; $i <= $endPage; $i++)
                            <a href="{{ route('manajemenpengguna.index', ['page' => $i, 'search' => request('search')]) }}"
                                class="px-3 py-1 border border-gray-300 rounded text-sm {{ $i == $currentPage ? 'bg-green-500 text-white border-green-500' : 'text-blue-600 hover:bg-blue-50' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        <!-- Next Page Button -->
                        @if ($currentPage < $totalPages)
                            <a href="{{ route('manajemenpengguna.index', ['page' => $currentPage + 1, 'search' => request('search')]) }}"
                                class="px-3 py-1 border border-gray-300 rounded text-sm {{ $currentPage == $totalPages ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50' }}">
                                ›
                            </a>
                        @endif

                        <!-- Last Page Button -->
                        @if ($currentPage < $totalPages)
                            <a href="{{ route('manajemenpengguna.index', ['page' => $totalPages, 'search' => request('search')]) }}"
                                class="px-3 py-1 border border-gray-300 rounded text-sm {{ $currentPage == $totalPages ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50' }}">
                                »
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Modal Admin --}}
        <div id="adminModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50"
            onclick="closeModal()">
            <div class="bg-white w-full max-w-5xl rounded-2xl shadow-lg p-6 relative flex flex-col gap-6"
                onclick="event.stopPropagation()">
                <button onclick="closeModal()"
                    class="absolute top-2 right-4 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                <h2 class="text-xl font-semibold text-gray-800 text-center">Data Admin</h2>

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-1/2 flex flex-col">
                        <!-- Perbaikan tampilan foto di modal: Menggunakan container dan memastikan gambar sesuai proporsi asli -->
                        <div class="bg-gray-200 rounded-lg overflow-hidden flex-grow flex justify-center items-center">
                            <img id="modalImage" src="https://randomuser.me/api/portraits/lego/2.jpg"
                                class="max-w-full max-h-full object-contain cursor-pointer" alt="Foto Admin"
                                onclick="uploadImage()">
                        </div>
                        <p class="text-center text-sm text-gray-500 mt-2">Klik pada gambar untuk mengubah foto</p>
                        <input type="hidden" id="modalPhotoUrl">
                    </div>

                    <div class="w-full md:w-1/2 space-y-3">
                        <input type="hidden" id="modalMode">
                        <div id="idField">
                            <label class="text-gray-600 text-sm">ID Admin</label>
                            <input type="text" id="modalID" readonly
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Nama</label>
                            <input type="text" id="modalNama"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Email</label>
                            <input type="email" id="modalEmail"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Password</label>
                            <input type="password" id="password" name="password"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Peran</label>
                            <select id="modalPeranSelect"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                                <option value="admin_penyemaian">Admin Penyemaian</option>
                                <option value="admin_tpk">Admin TPK</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Status</label>
                            <select id="modalStatusSelect"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600"
                            onclick="simpanPerubahan()">Perbarui</button>
                        <button class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600"
                            onclick="hapusAdmin()">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        let isEditMode = false;

        // Show modal to add new admin
        function showAddAdminModal() {
            isEditMode = false;
            document.getElementById('modalMode').value = 'tambah';
            document.getElementById('modalID').value = '';
            document.getElementById('modalNama').value = '';
            document.getElementById('modalEmail').value = '';
            document.getElementById('modalPeranSelect').value = 'admin_penyemaian';
            document.getElementById('modalStatusSelect').value = 'Aktif';
            document.getElementById('modalPhotoUrl').value = 'https://randomuser.me/api/portraits/lego/2.jpg';
            document.getElementById('modalImage').src = 'https://randomuser.me/api/portraits/lego/2.jpg';
            document.getElementById('idField').style.display = 'none';
            openModal();
        }

        // Show modal to edit admin details
        function showAdminDetail(button) {
            isEditMode = true;
            document.getElementById('modalMode').value = 'edit';
            document.getElementById('modalID').value = button.getAttribute('data-id');
            document.getElementById('modalNama').value = button.getAttribute('data-nama');
            document.getElementById('modalEmail').value = button.getAttribute('data-email');
            document.getElementById('modalPeranSelect').value = button.getAttribute('data-peran').toLowerCase().replace(' ',
                '_');
            document.getElementById('modalStatusSelect').value = button.getAttribute('data-status');

            // Set photo URL
            const photoUrl = button.getAttribute('data-photo');
            document.getElementById('modalPhotoUrl').value = photoUrl;
            document.getElementById('modalImage').src = photoUrl;

            document.getElementById('idField').style.display = 'block';
            openModal();
        }

        // Open modal
        function openModal() {
            const modal = document.getElementById("adminModal");
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }

        // Close modal
        function closeModal() {
            const modal = document.getElementById("adminModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }

        // Save changes (add or edit admin)
        function simpanPerubahan() {
            const nama = document.getElementById('modalNama').value;
            const email = document.getElementById('modalEmail').value;
            const peran = document.getElementById('modalPeranSelect').value;
            const status = document.getElementById('modalStatusSelect').value;
            const mode = document.getElementById('modalMode').value;
            const photoUrl = document.getElementById('modalPhotoUrl').value;

            const data = {
                nama,
                email,
                peran,
                status,
                photo_url: photoUrl
            };

            if (mode === 'edit') data.id = document.getElementById('modalID').value;

            const url = (mode === 'edit') ? '/update-admin' : '/add-admin';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    closeModal();
                    location.reload();
                })
                .catch(() => alert('Terjadi kesalahan.'));
        }

        // Delete admin
        function hapusAdmin() {
            const id = document.getElementById('modalID').value;

            if (confirm('Apakah Anda yakin ingin menghapus admin ini?')) {
                fetch('/delete-admin', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            id
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        closeModal();
                        location.reload();
                    })
                    .catch(() => alert('Terjadi kesalahan.'));
            }
        }

        // Upload image function - Ditingkatkan untuk menyimpan URL
        function uploadImage() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = (e) => {
                const file = e.target.files[0];
                if (file) {
                    // Untuk demo, kita hanya menampilkan preview dan menggunakan URL gambar statis
                    // Di implementasi nyata, Anda akan mengupload ke Firebase Storage dan mendapatkan URL

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('modalImage').src = event.target.result;

                        // Ini hanya untuk demo - dalam aplikasi nyata, Anda akan mengupload ke Firebase Storage
                        // dan menggunakan URL yang dikembalikan
                        simulateUpload(event.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            };
            input.click();
        }

        // Fungsi simulasi untuk upload (dalam implementasi nyata, ini akan mengupload ke Firebase Storage)
        function simulateUpload(dataUrl) {
            // Contoh URL gambar - dalam implementasi nyata, Anda akan mendapatkan URL dari Firebase Storage
            const exampleUrls = [
                'https://iili.io/3S3Pxqu.jpg',
                'https://randomuser.me/api/portraits/men/1.jpg',
                'https://randomuser.me/api/portraits/women/2.jpg',
                'https://randomuser.me/api/portraits/lego/3.jpg'
            ];

            // Pilih URL secara acak untuk demo
            const randomUrl = exampleUrls[Math.floor(Math.random() * exampleUrls.length)];

            // Dalam aplikasi nyata, Anda akan mengirim file ke server dan mendapatkan URL dari respons
            setTimeout(() => {
                document.getElementById('modalPhotoUrl').value = randomUrl;
                console.log('Photo URL set to:', randomUrl);
            }, 500);
        }

        // Update the status of admin
        function updateStatus(select) {
            const status = select.value;
            const id = select.dataset.id;
            select.classList.toggle('bg-green-300', status === 'Aktif');
            select.classList.toggle('bg-red-300', status === 'Nonaktif');

            fetch('/update-status', {
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
                .then(response => response.ok ? console.log('Status updated') : alert('Gagal update'))
                .catch(() => alert('Terjadi kesalahan'));
        }

        // Search form handling
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    window.location.href = '{{ route('manajemenpengguna.index') }}?search=' + this.value;
                }
            });
        });
    </script>
@endpush
