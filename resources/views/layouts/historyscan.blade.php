@extends('layouts.app')

@section('title', 'Riwayat Scan Barcode')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Hallo, {{ session('user_nama') }} 👋</h1>
        </div>

        <!-- Start of the Table Section -->
        <div id="table-aktivitas" class="overflow-x-auto mt-6 bg-white shadow-md rounded-3xl p-5">
            <div id="search-sort-section"
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 space-y-3 md:space-y-0">
                <h2 class="text-lg font-semibold text-gray-800 text-center md:text-left">Aktivitas Terbaru</h2>

                <div class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    <!-- Pencarian -->
                    <div id="search-bar" class="relative w-full md:w-auto">
                        <input type="text" placeholder="Cari" id="searchActivities"
                            class="pl-8 pr-3 py-1 w-full md:w-48 border rounded-lg bg-green-100 text-gray-800 focus:outline-none"
                            value="{{ $search }}">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                    </div>

                    <!-- Dropdown Urutkan -->
                    <div id="sort-dropdown" class="flex flex-col md:flex-row md:items-center">
                        <label class="text-gray-600 text-sm">Urutkan:</label>
                        <select id="sortKayu"
                            class="ml-1 text-gray-800 font-semibold border bg-gray-100 px-2 py-1 rounded-lg w-full md:w-auto">
                            <option value="terbaru" {{ $sortOrder == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ $sortOrder == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 mt-8">
                    <thead class="bg-white">
                        <tr class="text-gray-600">
                            <th class="py-3 px-4 text-left">Profil Admin</th>
                            <th class="py-3 px-4 text-left">Jenis Aktivitas</th>
                            <th class="py-3 px-8 text-left">Waktu</th>
                            <th class="py-3 px-8 text-left">Detail</th>
                        </tr>
                    </thead>
                    <tbody id="activity-tbody">
                        @forelse ($activities as $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <!-- Gambar: Cek apakah ada imageUrl di activity -->
                                        <img src="{{ $activity['imageUrl'] ? $activity['imageUrl'] : 'https://i.pravatar.cc/64?u=' . urlencode($activity['nama']) }}"
                                            alt="{{ $activity['nama'] }}" class="w-10 h-10 rounded-full">
                                        <div>
                                            <div class="font-semibold">{{ $activity['nama'] }}</div>
                                            <div class="text-sm text-gray-400">{{ $activity['userRole'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">{{ $activity['keterangan'] }}</td>
                                <td class="py-3 px-8 whitespace-nowrap">
                                    {{ $activity['waktu'] ? \Carbon\Carbon::parse($activity['waktu'])->diffForHumans() : '-' }}
                                </td>
                                <td class="py-3 px-8">{{ $activity['detail'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 px-4 text-center text-gray-500">Tidak ada aktivitas terbaru
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- Pagination Footer inside table -->
                    <tfoot>
                        <tr>
                            <td colspan="4" class="px-4 py-3">
                                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                    <!-- Informasi entri -->
                                    <div class="text-sm text-gray-500">
                                        <span id="entries-info">
                                            Menampilkan {{ ($currentPage - 1) * $perPage + 1 }} -
                                            {{ min($currentPage * $perPage, $total) }} dari {{ $total }} entri
                                        </span>
                                    </div>

                                    <!-- Pagination controls -->
                                    <div class="flex items-center space-x-1" id="pagination-controls">
                                        <!-- First Page Button -->
                                        <button onclick="changePage(1)"
                                            class="px-3 py-1 border border-gray-300 rounded text-sm {{ $currentPage == 1 ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50' }}"
                                            {{ $currentPage == 1 ? 'disabled' : '' }}>
                                            «
                                        </button>

                                        <!-- Previous Page Button -->
                                        <button onclick="changePage({{ max(1, $currentPage - 1) }})"
                                            class="px-3 py-1 border border-gray-300 rounded text-sm {{ $currentPage == 1 ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50' }}"
                                            {{ $currentPage == 1 ? 'disabled' : '' }}>
                                            ‹
                                        </button>

                                        <!-- Page Numbers -->
                                        @php
                                            $startPage = max(1, $currentPage - 2);
                                            $endPage = min($startPage + 4, $totalPages);

                                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                                $startPage = max(1, $endPage - 4);
                                            }
                                        @endphp

                                        @for ($i = $startPage; $i <= $endPage; $i++)
                                            <button onclick="changePage({{ $i }})"
                                                class="px-3 py-1 border border-gray-300 rounded text-sm {{ $i == $currentPage ? 'bg-green-500 text-white border-green-500' : 'text-blue-600 hover:bg-blue-50' }}">
                                                {{ $i }}
                                            </button>
                                        @endfor

                                        <!-- Next Page Button -->
                                        <button onclick="changePage({{ min($totalPages, $currentPage + 1) }})"
                                            class="px-3 py-1 border border-gray-300 rounded text-sm {{ $currentPage == $totalPages ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50' }}"
                                            {{ $currentPage == $totalPages ? 'disabled' : '' }}>
                                            ›
                                        </button>

                                        <!-- Last Page Button -->
                                        <button onclick="changePage({{ $totalPages }})"
                                            class="px-3 py-1 border border-gray-300 rounded text-sm {{ $currentPage == $totalPages ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50' }}"
                                            {{ $currentPage == $totalPages ? 'disabled' : '' }}>
                                            »
                                        </button>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- End of Table Section -->
    </div>

    <!-- Hidden form for handling pagination with filters -->
    <form id="paginationForm" method="GET" action="{{ route('history.index') }}" style="display: none;">
        <input type="hidden" name="page" id="pageInput" value="{{ $currentPage }}">
        <input type="hidden" name="search" id="searchInput" value="{{ $search }}">
        <input type="hidden" name="sort" id="sortInput" value="{{ $sortOrder }}">
        <input type="hidden" name="search_type" id="searchTypeInput" value="{{ $searchType }}">
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Page Loaded!");

            // Handle search input with debounce
            let searchTimeout;
            const searchInput = document.getElementById('searchActivities');
            const searchForm = document.getElementById('paginationForm');

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    document.getElementById('searchInput').value = searchInput.value;
                    document.getElementById('pageInput').value = 1; // Reset to first page
                    searchForm.submit();
                }, 500); // 500ms debounce
            });

            // Handle sort dropdown
            const sortSelect = document.getElementById('sortKayu');
            sortSelect.addEventListener('change', function() {
                document.getElementById('sortInput').value = sortSelect.value;
                document.getElementById('pageInput').value = 1; // Reset to first page
                searchForm.submit();
            });
        });

        // Function to handle page changes
        function changePage(page) {
            document.getElementById('pageInput').value = page;
            document.getElementById('searchInput').value = document.getElementById('searchActivities').value;
            document.getElementById('sortInput').value = document.getElementById('sortKayu').value;
            document.getElementById('paginationForm').submit();
        }

        // Function to update pagination display (if using AJAX)
        function updatePagination(currentPage, totalPages, total, perPage) {
            const entriesInfo = document.getElementById('entries-info');
            const startEntry = (currentPage - 1) * perPage + 1;
            const endEntry = Math.min(currentPage * perPage, total);

            entriesInfo.textContent = `Menampilkan ${startEntry} - ${endEntry} dari ${total} entri`;

            // Update pagination controls
            // This would need to be implemented based on your specific needs
        }
    </script>
@endsection
