@extends('layouts.app')

@section('title', 'Riwayat Perawatan Bibit')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">{{ session('user_nama') }} 👋</h1>
        </div>

        <div id="table-aktivitas" class="bg-white shadow-md rounded-3xl p-5 mt-5">
            <h2 class="text-xl font-semibold text-gray-800">Riwayat Perawatan</h2>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-100">
                            <th class="py-3 text-left px-4">Profil Admin</th>
                            <th class="py-3 text-left px-4">Jenis Aktivitas</th>
                            <th class="py-3 text-left px-4">Waktu</th>
                            <th class="py-3 text-left px-4">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedPerawatan as $data)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <p class="font-semibold">{{ $init['created_by_name'] }}</p>
                                </td>
                                <td class="py-3 px-4">{{ $init['jenis_perawatan'] }}</td>
                                <td class="py-3 px-4">{{ $init['waktu'] }}</td>
                                <td class="py-3 px-4">{{ $init['catatan'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-8">
                <p class="text-sm text-gray-500">Menampilkan data 1 hingga 8 dari {{ $paginatedPerawatan->total() }} entri
                </p>
                <div class="flex space-x-1">
                    {{ $paginatedPerawatan->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
