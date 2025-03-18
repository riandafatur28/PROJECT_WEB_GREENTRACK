@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="flex flex-col justify-center items-center min-h-screen bg-gray-50 p-10 relative">
        <!-- Foto Profil -->
        <div class="relative flex flex-col items-center">
            <div class="w-52 h-52 bg-gray-200 rounded-full flex justify-center items-center">
                <img src="https://via.placeholder.com/200" alt="Profile" class="w-48 h-48 rounded-full object-cover">
            </div>
            <h2 class="text-2xl font-bold mt-6">FITRI MEYDAYANI</h2>
        </div>

        <!-- Form Input -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-8 w-full max-w-4xl">
            <div>
                <label class="block text-gray-700 text-lg">Nama</label>
                <input type="text" class="w-full border px-4 py-3 rounded-lg text-lg bg-white" value="FITRI MEYDAYANI"
                    readonly>
            </div>
            <div>
                <label class="block text-gray-700 text-lg">Email</label>
                <input type="text" class="w-full border px-4 py-3 rounded-lg text-lg bg-white"
                    value="fitri.meydayani@email.com" readonly>
            </div>
            <div>
                <label class="block text-gray-700 text-lg">Label</label>
                <input type="text" class="w-full border px-4 py-3 rounded-lg text-lg bg-white"
                    placeholder="Masukkan teks..." disabled>
            </div>
            <div>
                <label class="block text-gray-700 text-lg">Label</label>
                <input type="text" class="w-full border px-4 py-3 rounded-lg text-lg bg-white"
                    placeholder="Masukkan teks..." disabled>
            </div>
            <div>
                <label class="block text-gray-700 text-lg">Label</label>
                <input type="text" class="w-full border px-4 py-3 rounded-lg text-lg bg-white"
                    placeholder="Masukkan teks..." disabled>
            </div>
            <div>
                <label class="block text-gray-700 text-lg">Label</label>
                <input type="text" class="w-full border px-4 py-3 rounded-lg text-lg bg-white"
                    placeholder="Masukkan teks..." disabled>
            </div>
        </div>

        <!-- Tombol -->
        <div class="fixed bottom-4 right-4 flex space-x-4 md:relative md:mt-8 md:justify-center">
            <button class="bg-green-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-green-700" id="saveButton"
                disabled>Simpan</button>
            <button class="bg-gray-500 text-white px-8 py-3 rounded-lg text-lg hover:bg-gray-600"
                id="editButton">Edit</button>
            <button class="bg-red-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-red-700"
                id="logoutButton">Keluar</button>
        </div>
    </div>
@endsection

<script>
    document.getElementById('editButton').addEventListener('click', function() {
        document.querySelectorAll('input').forEach(input => {
            input.removeAttribute('disabled');
        });
        document.getElementById('saveButton').removeAttribute('disabled');
    });
    document.getElementById('logoutButton').addEventListener('click', function() {
        window.location.href = 'login.blade'; // Ganti dengan halaman login yang sesuai
    });
</script>
