@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="flex justify-center items-center min-h-screen">

        <div class="bg-white p-8 rounded-3xl shadow-md w-full max-w-3xl text-center">
            <!-- Foto Profil -->
            <div class="flex flex-col items-center">
                <img src="https://via.placeholder.com/150" alt="Profile" class="w-32 h-32 rounded-full">
                <h2 class="text-xl font-bold mt-4">FITRI MEYDAYANI</h2>
            </div>

            <!-- Form Input -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-gray-600 text-sm text-left">Nama</label>
                    <input type="text" class="w-full border px-3 py-2 rounded-lg" value="FITRI MEYDAYANI" readonly>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm text-left">Email</label>
                    <input type="text" class="w-full border px-3 py-2 rounded-lg" value="fitri.meydayani@email.com"
                        readonly>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm text-left">Label</label>
                    <input type="text" class="w-full border px-3 py-2 rounded-lg" placeholder="Placeholder">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm text-left">Label</label>
                    <input type="text" class="w-full border px-3 py-2 rounded-lg" placeholder="Placeholder">
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex justify-center space-x-4 mt-6">
                <button class="bg-green-600 text-white px-6 py-2 rounded-lg">Simpan</button>
                <button class="bg-gray-400 text-white px-6 py-2 rounded-lg">Edit</button>
                <button class="bg-red-600 text-white px-6 py-2 rounded-lg">Keluar</button>
            </div>
        </div>

    </div>
@endsection
