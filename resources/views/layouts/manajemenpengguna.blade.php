@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Halo Fitri 👋</h1>
        </div>

        <div id="table-admin" class="bg-white shadow-md rounded-3xl p-3 mt-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Admin</h2>
                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Tombol Tambah Admin di sebelah kiri pencarian -->
                    <button onclick="showAddAdminModal()"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600">
                        Tambah
                    </button>
                    <div class="relative w-full md:w-auto">
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
                                        class="status-dropdown px-2 py-1 rounded-lg border text-xs md:text-sm {{ $init['status'] == 'Aktif' ? 'bg-green-300' : 'bg-red-300' }} "
                                        data-id="{{ $init['id'] }}" onchange="updateBackground(this)">
                                        <option value="Aktif" {{ $init['status'] == 'Aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="Nonaktif" {{ $init['status'] == 'Nonaktif' ? 'selected' : '' }}>
                                            Nonaktif</option>
                                    </select>
                                </td>
                                <td class="px-2 py-1">
                                    <button onclick="showAdminDetail(this)"
                                        class="ml-1 bg-teal-300 text-teal-700 font-semibold px-3 py-1 rounded-lg border border-teal-700"
                                        data-nama="{{ $init['nama'] }}" data-email="{{ $init['email'] }}"
                                        data-peran="{{ $init['peran_admin'] }}" data-status="{{ $init['status'] }}">
                                        Lihat Selengkapnya
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-between mt-8">
                    <p class="text-sm text-gray-500">Menampilkan data 1 hingga 8 dari 256 entri</p>
                    <div class="flex space-x-1">
                        <button
                            class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&lt;</button>
                        <button
                            class="px-2 py-0.5 bg-green-500 text-white border border-green-500 rounded-md text-xs">1</button>
                        <button
                            class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">2</button>
                        <button
                            class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">3</button>
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

        <!-- Modal Pop Up Lengkap -->
        <div id="adminModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50"
            onclick="closeModal()">
            <div class="bg-white w-full max-w-5xl rounded-2xl shadow-lg p-6 relative flex flex-col gap-6"
                onclick="event.stopPropagation()">
                <!-- Tombol Tutup -->
                <button onclick="closeModal()"
                    class="absolute top-2 right-4 text-gray-500 hover:text-gray-700 text-xl font-bold">
                    &times;
                </button>

                <!-- Judul -->
                <h2 class="text-xl font-semibold text-gray-800 text-center">Data Admin</h2>

                <!-- Konten Modal -->
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Gambar persegi panjang lebih besar -->
                    <div class="w-full md:w-1/2 h-auto bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                        <img id="modalImage" src="https://randomuser.me/api/portraits/lego/2.jpg"
                            class="w-full h-48 object-cover cursor-pointer" alt="Foto Admin" onclick="uploadImage()">
                    </div>

                    <!-- Detail -->
                    <div class="w-full md:w-1/2 space-y-3">
                        <div>
                            <label class="text-gray-600 text-sm">ID Admin</label>
                            <input type="text" id="modalID" readonly
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Nama</label>
                            <input type="text" id="modalNama" readonly
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Email</label>
                            <input type="email" id="modalEmail" readonly
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Peran</label>
                            <input type="text" id="modalPeran" readonly
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>
                        <div>
                            <label class="text-gray-600 text-sm">Status</label>
                            <input type="text" id="modalStatus" readonly
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                        </div>

                        <!-- Tombol aksi -->
                        <div class="flex justify-end gap-2 pt-4">
                            <button id="editButton"
                                class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-400"
                                onclick="enableEdit()">Edit</button>
                            <button
                                class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600">Simpan</button>
                            <button
                                class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script>
            function showAdminDetail(button) {
                document.getElementById('modalID').value = button.closest('tr').querySelector('td:first-child').innerText;
                document.getElementById('modalNama').value = button.getAttribute('data-nama');
                document.getElementById('modalEmail').value = button.getAttribute('data-email');
                document.getElementById('modalPeran').value = button.getAttribute('data-peran');
                document.getElementById('modalStatus').value = button.getAttribute('data-status');

                const modal = document.getElementById("adminModal");
                modal.classList.remove("hidden");
                modal.classList.add("flex");
            }

            function closeModal() {
                const modal = document.getElementById("adminModal");
                modal.classList.add("hidden");
                modal.classList.remove("flex");
            }

            function enableEdit() {
                document.getElementById('modalNama').removeAttribute('readonly');
                document.getElementById('modalPeran').removeAttribute('readonly');
                document.getElementById('modalStatus').removeAttribute('readonly');
                document.getElementById('editButton').setAttribute('disabled', 'true'); // Disable tombol Edit saat klik
            }

            function uploadImage() {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';

                input.onchange = (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            document.getElementById('modalImage').src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                };

                input.click(); // Triggers the file input dialog
            }

            function updateBackground(select) {
                const selectedValue = select.value;
                select.classList.toggle('bg-green-300', selectedValue === 'Aktif');
                select.classList.toggle('bg-red-300', selectedValue === 'Nonaktif');
            }

            function showAddAdminModal() {
                // Reset form inputs pada modal
                document.getElementById('modalID').value = '';
                document.getElementById('modalNama').value = '';
                document.getElementById('modalEmail').value = '';
                document.getElementById('modalPeran').value = '';
                document.getElementById('modalStatus').value = 'Aktif'; // Set default status ke "Aktif"

                // Tampilkan modal
                const modal = document.getElementById("adminModal");
                modal.classList.remove("hidden");
                modal.classList.add("flex");
            }
        </script>
    @endpush
