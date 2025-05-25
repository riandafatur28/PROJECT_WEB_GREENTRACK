{{-- filepath: d:\project\laravel_project\PROJECT_WEB_GREENTRACK\resources\views\layouts\profile.blade.php --}}
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
            <div class="w-52 h-52 bg-gray-200 rounded-full flex justify-center items-center cursor-pointer"
                id="imageContainer">
                <img id="profileImage" src="{{ asset('assets/images/profile.jpg') }}" alt="Profile"
                    class="w-52 h-52 object-cover rounded-full">
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
    </div>
@endsection

@push('scripts')
    <script>
        // Close profile
        function closeProfile() {
            window.history.back();
        }
    </script>
@endpush
