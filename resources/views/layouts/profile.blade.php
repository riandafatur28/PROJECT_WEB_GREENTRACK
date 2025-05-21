@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="flex flex-col justify-center items-center min-h-screen bg-gray-50 p-10 relative">

        <!-- Tombol Tutup -->
        <button onclick="closeProfile()"
            class="absolute top-4 left-4 text-3xl text-red-600 hover:text-red-800 z-10 leading-none">
            &times;
        </button>

        <!-- Foto Profil -->
        <div class="relative flex flex-col items-center">
            <div class="w-52 h-52 bg-gray-200 rounded-full flex justify-center items-center cursor-not-allowed"
                id="imageContainer">
                <img id="profileImage" src="{{ session('profile_image') ?: 'https://via.placeholder.com/200' }}" alt="Profile"
                    class="w-48 h-48 rounded-full object-cover">
                <input type="file" id="profileImageInput" accept="image/*" class="hidden">
            </div>
            <h2 class="text-2xl font-bold mt-6">{{ session('user_nama') }}</h2>
        </div>

        <!-- Form Input -->
        <div class="grid grid-cols-1 gap-6 mt-8 w-full max-w-4xl">
            <div>
                <label class="block text-gray-700 text-lg">Nama</label>
                <input type="text" id="nama"
                    class="editable-input w-full border px-4 py-3 rounded-lg text-lg bg-white text-gray-500"
                    value="{{ session('user_nama') }}" readonly>
            </div>
            <div>
                <label class="block text-gray-700 text-lg">Email</label>
                <input type="text" id="email"
                    class="editable-input w-full border px-4 py-3 rounded-lg text-lg bg-white text-gray-500"
                    value="{{ session('email') }}" readonly>
            </div>

            <div>
                <label class="block text-gray-700 text-lg">Role</label>
                <input type="text" id="role"
                    class="editable-input w-full border px-4 py-3 rounded-lg text-lg bg-white text-gray-500"
                    value="{{ session('role') }}" readonly>
            </div>
        </div>

        <!-- Tombol -->
        <div class="w-full mt-8 flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 justify-center items-center">
            <button class="bg-green-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-green-700 w-full md:w-auto"
                id="saveButton" disabled>Simpan</button>
            <button class="bg-gray-500 text-white px-8 py-3 rounded-lg text-lg hover:bg-gray-600 w-full md:w-auto"
                id="editButton">Edit</button>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-red-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-red-700 w-full md:w-auto"
                    id="logoutButton">Keluar</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let editMode = false;

        // Toggle edit mode
        document.getElementById('editButton').addEventListener('click', function() {
            editMode = true;

            document.querySelectorAll('.editable-input').forEach(input => {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.classList.remove('text-gray-500');
                input.classList.add('text-gray-900');
            });

            document.getElementById('saveButton').removeAttribute('disabled');

            // Enable image change
            const imageContainer = document.getElementById('imageContainer');
            imageContainer.classList.remove('cursor-not-allowed');
            imageContainer.classList.add('cursor-pointer');

            imageContainer.addEventListener('click', function() {
                if (editMode) {
                    document.getElementById('profileImageInput').click();
                }
            });
        });

        // Handle profile image change
        document.getElementById('profileImageInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Logout action - handled via form submit
        document.getElementById('logoutButton').addEventListener('click', function() {
            // This is handled via a form submission now
        });

        // Close profile
        function closeProfile() {
            window.history.back();
        }
    </script>
@endpush
