@extends('layouts.app')

@section('title', 'Riwayat Perawatan Bibit')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Halo, {{ session('user_nama') }} 👋</h1>
        </div>

        <div id="table-aktivitas" class="bg-white shadow-md rounded-3xl p-5 mt-5">
            <!-- Search and Sort Section -->
            <div id="search-sort-section"
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Riwayat Perawatan</h2>

                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Pencarian -->
                    <div id="search-bar" class="relative w-full md:w-auto">
                        <form action="{{ route('historyperawatan') }}" method="GET" class="flex items-center">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari"
                                id="searchPerawatan"
                                class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                        </form>
                    </div>

                    <!-- Dropdown Urutkan -->
                    <div id="sort-dropdown" class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <select name="sort"
                            class="ml-1 text-gray-800 font-semibold border bg-gray-100 px-2 py-1 rounded-lg w-full md:w-auto"
                            onchange="this.form.submit()">
                            <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Terbaru</option>
                            <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto mt-6">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-white">
                        <tr class="text-gray-600">
                            <th class="py-3 px-4 text-left">Nama Admin</th>
                            <th class="py-3 px-4 text-left">Jenis Perawatan</th>
                            <th class="py-3 px-4 text-left">Nama Bibit</th>
                            <th class="py-3 px-4 text-left">Waktu</th>
                            <th class="py-3 px-4 text-left">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perawatan as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $data['nama'] }}</td>
                                <td class="py-3 px-4">{{ $data['keterangan'] }}</td>
                                <td class="py-3 px-4">{{ $data['nama_bibit'] }}</td>
                                <td class="py-3 px-4">{{ $data['waktu'] ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $data['detail'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-3 px-4 text-center text-gray-500">Tidak ada data ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between mt-8">
                <p class="text-sm text-gray-500">
                    Menampilkan data {{ ($currentPage - 1) * $perPage + 1 }} hingga
                    {{ min($currentPage * $perPage, $total) }} dari {{ $total }} entri
                </p>
                <div class="flex space-x-1" id="pagination-links">
                    <!-- Previous Page Button -->
                    @if ($currentPage > 1)
                        <a href="{{ route('historyperawatan', ['page' => $currentPage - 1, 'search' => request('search'), 'sort' => request('sort')]) }}"
                            class="px-3 py-1 border border-gray-300 rounded text-sm text-blue-600 hover:bg-blue-50">
                            ‹
                        </a>
                    @endif

                    <!-- Page Numbers -->
                    @php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($startPage + 4, ceil($total / $perPage));
                    @endphp

                    @for ($i = $startPage; $i <= $endPage; $i++)
                        <a href="{{ route('historyperawatan', ['page' => $i, 'search' => request('search'), 'sort' => request('sort')]) }}"
                            class="px-3 py-1 border border-gray-300 rounded text-sm {{ $i == $currentPage ? 'bg-green-500 text-white' : 'text-blue-600 hover:bg-blue-50' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    <!-- Next Page Button -->
                    @if ($currentPage < ceil($total / $perPage))
                        <a href="{{ route('historyperawatan', ['page' => $currentPage + 1, 'search' => request('search'), 'sort' => request('sort')]) }}"
                            class="px-3 py-1 border border-gray-300 rounded text-sm text-blue-600 hover:bg-blue-50">
                            ›
                        </a>
                    @endif

                    <!-- Last Page Button -->
                    @if ($currentPage < ceil($total / $perPage))
                        <a href="{{ route('historyperawatan', ['page' => ceil($total / $perPage), 'search' => request('search'), 'sort' => request('sort')]) }}"
                            class="px-3 py-1 border border-gray-300 rounded text-sm text-blue-600 hover:bg-blue-50">
                            »
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
