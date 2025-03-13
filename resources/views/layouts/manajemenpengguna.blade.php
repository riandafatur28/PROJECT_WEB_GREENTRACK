@extends('layouts.app')

@section('content')
    <div class="bg-gray-100 p-6">
        <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">Hello Fitri 👋,</h1>
                <div class="bg-green-100 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500">Sedang Aktif</p>
                    <p class="text-3xl font-semibold">9</p>
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-3">Data Admin</h2>
            <div class="flex justify-between mb-4">
                <input type="text" placeholder="Cari" class="border p-2 rounded-lg w-1/3">
                <div class="flex space-x-2">
                    <button class="bg-green-500 text-white px-4 py-2 rounded-lg">Tambah Admin</button>
                    <select class="border p-2 rounded-lg">
                        <option>Urutkan berdasarkan: Terlama</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border rounded-lg">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">ID</th>
                            <th class="p-3 text-left">Nama</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-left">Peran Admin</th>
                            <th class="p-3 text-left">Status Akun</th>
                            <th class="p-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="p-3">001</td>
                            <td class="p-3">Fitri</td>
                            <td class="p-3">@email.com</td>
                            <td class="p-3">TPK</td>
                            <td class="p-3">
                                <select class="bg-green-500 text-white px-3 py-1 rounded-lg">
                                    <option>Aktif</option>
                                </select>
                            </td>
                            <td class="p-3">
                                <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">Lihat Selengkapnya</button>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-3">002</td>
                            <td class="p-3">Gita</td>
                            <td class="p-3">@email.com</td>
                            <td class="p-3">TPK</td>
                            <td class="p-3">
                                <select class="bg-red-500 text-white px-3 py-1 rounded-lg">
                                    <option>Nonaktif</option>
                                </select>
                            </td>
                            <td class="p-3">
                                <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">Lihat Selengkapnya</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-4">
                <p class="text-sm text-gray-500">Menampilkan data 1 hingga 8 dari 256 entri</p>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 bg-gray-300 rounded-lg">&lt;</button>
                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg">1</button>
                    <button class="px-3 py-1 bg-gray-300 rounded-lg">2</button>
                    <button class="px-3 py-1 bg-gray-300 rounded-lg">3</button>
                    <button class="px-3 py-1 bg-gray-300 rounded-lg">...</button>
                    <button class="px-3 py-1 bg-gray-300 rounded-lg">40</button>
                    <button class="px-3 py-1 bg-gray-300 rounded-lg">&gt;</button>
                </div>
            </div>
        </div>
    </div>
@endsection
