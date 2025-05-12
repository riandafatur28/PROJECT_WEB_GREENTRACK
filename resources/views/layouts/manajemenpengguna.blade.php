@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">{{ session('user_nama') }} 👋</h1>
        </div>

        <div id="table-admin" class="bg-white shadow-md rounded-3xl p-3 mt-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Data Admin</h2>
                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <button onclick="showAddAdminModal()"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600">Tambah</button>

                    <div class="relative w-full md:w-auto">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                            placeholder="Cari"
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
                                        data-status="{{ $init['status'] }}">
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
                        @if ($currentPage > 1)
                            <a href="{{ route('manajemenpengguna.index', ['page' => $currentPage - 1, 'search' => request('search')]) }} "
                                class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&lt;</a>
                        @endif
                        @for ($i = 1; $i <= ceil($total / $perPage); $i++)
                            <a href="{{ route('manajemenpengguna.index', ['page' => $i, 'search' => request('search')]) }} "
                                class="px-2 py-0.5 {{ $currentPage == $i ? 'bg-green-500 text-white' : 'bg-white text-gray-500' }} border border-gray-300 rounded-md text-xs">{{ $i }}</a>
                        @endfor
                        @if ($currentPage < ceil($total / $perPage))
                            <a href="{{ route('manajemenpengguna.index', ['page' => $currentPage + 1, 'search' => request('search')]) }} "
                                class="px-2 py-0.5 bg-white text-gray-500 border border-gray-300 rounded-md text-xs">&gt;</a>
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
                    <div class="w-full md:w-1/2 h-auto bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                        <img id="modalImage" src="https://randomuser.me/api/portraits/lego/2.jpg"
                            class="w-full h-48 object-cover cursor-pointer" alt="Foto Admin" onclick="uploadImage()">
                    </div>

                    <div class="w-full md:w-1/2 space-y-3">
                        <input type="hidden" id="modalMode">
                        <div id="idField"><label class="text-gray-600 text-sm">ID Admin</label><input type="text"
                                id="modalID" readonly
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800"></div>
                        <div><label class="text-gray-600 text-sm">Nama</label><input type="text" id="modalNama"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800"></div>
                        <div><label class="text-gray-600 text-sm">Email</label><input type="email" id="modalEmail"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800"></div>
                        <div><label class="text-gray-600 text-sm">Peran</label>
                            <select id="modalPeranSelect"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                                <option value="admin_penyemaian">Admin Penyemaian</option>
                                <option value="admin_tpk">Admin TPK</option>
                            </select>
                        </div>
                        <div><label class="text-gray-600 text-sm">Status</label>
                            <select id="modalStatusSelect"
                                class="w-full px-3 py-2 rounded-lg border bg-gray-100 text-gray-800">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
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
            const data = {
                nama,
                email,
                peran,
                status
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

        // Upload image function
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
            input.click();
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
    </script>
@endpush
