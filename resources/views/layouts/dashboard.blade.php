@extends('layouts.app')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">{{ session('user_nama') }} 👋</h1>
        </div>

        <!-- Card Statistik -->
        <div class="bg-white p-6 rounded-3xl shadow-md mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:divide-x md:divide-gray-300">
                <!-- Total Bibit -->
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/bibit.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Bibit</p>
                        <h2 class="text-xl font-bold text-black-600">
                            {{ number_format(count($bibit ?? [])) }}
                        </h2>
                        <p class="text-xs text-green-500 mt-1 flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            16% bulan ini
                        </p>
                    </div>
                </div>

                <!-- Total Kayu -->
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/kayu.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Kayu</p>
                        <h2 class="text-xl font-bold text-black-600">{{ number_format(count($kayu ?? [])) }}</h2>
                        <p class="text-xs text-red-500 mt-1 flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            1% bulan ini
                        </p>
                    </div>
                </div>

                <!-- Admin Aktif -->
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/admin.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Admin Aktif</p>
                        <h2 class="text-xl font-bold text-black-600">9</h2>
                        <div class="flex mt-1">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2 first:ml-0"
                                src="https://randomuser.me/api/portraits/women/1.jpg" alt="Admin 1">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/men/2.jpg" alt="Admin 2">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/women/3.jpg" alt="Admin 3">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/men/4.jpg" alt="Admin 4">
                            <img class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                                src="https://randomuser.me/api/portraits/women/5.jpg" alt="Admin 5">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart & Aktivitas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Bibit Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-bold mb-2">Bibit Dalam Penyemaian</h3>
                {{-- <p class="text-base text-gray-600 mb-4">Total Bibit {{ number_format($totalBibit) }}</p> --}}
                <div id="bibitChart" style="height: 400px;"></div>
            </div>

            <!-- Kayu TPK Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-bold mb-2">Kayu di TPK</h3>
                <p class="text-base text-gray-600 mb-4">Total Kayu 800</p>
                <div style="height: 300px;">
                    <canvas id="kayuTPKChart"></canvas>
                </div>
            </div>

            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <!-- Teks Aktifitas Terbaru -->
                    <h3 class="text-xl font-semibold">Aktifitas Terbaru</h3>

                    <!-- Kontainer untuk Input Pencarian dan Dropdown Urutkan Berdasarkan -->
                    <div class="flex gap-4 items-center">
                        <!-- Input Pencarian -->
                        <div class="relative w-full md:w-48">
                            <input type="text" placeholder="Cari"
                                class="pl-8 pr-3 py-1 w-full border rounded-lg bg-gray-100 text-gray-800 focus:outline-none">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">🔍</span>
                        </div>

                        <!-- Urutkan Berdasarkan -->
                        <div>
                            <!-- <label class="text-gray-600 text-sm">Urutkan Berdasarkan:</label> -->
                            <select class="px-3 py-2 bg-gray-100 border rounded-lg">
                                <option value="30">30 hari terakhir</option>
                                <option value="7">7 hari terakhir</option>
                                <option value="1">Hari ini</option>
                            </select>
                        </div>
                    </div>
                </div>

                <table class="w-full text-sm text-left text-gray-600 border-collapse">
                    <thead>
                        <tr class="text-[#B5B7C0] border-b border-[#B5B7C0]">
                            <th class="py-2">Nama</th>
                            <th class="py-2">Aktifitas</th>
                            <th class="py-2">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://i.pravatar.cc/64?u={{ urlencode($activity['nama']) }}"
                                            alt="{{ $activity['nama'] }}" class="w-12 h-12 rounded-full">
                                        <div class="ml-6">
                                            <div class="text-xl font-semibold">{{ $activity['nama'] }}</div>
                                            <div class="text-sm text-gray-400">{{ $activity['userRole'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">{{ $activity['keterangan'] }}</td>
                                <td class="py-4 font-semibold">
                                    {{ $activity['waktu'] ? \Carbon\Carbon::parse($activity['waktu'])->diffForHumans() : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-400">Tidak ada aktivitas terbaru</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        @endsection

        @section('scripts')
            <!-- CDN Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <!-- CDN ApexCharts -->
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

            <!-- Script Kayu TPK Chart -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dataKayuTPK = {
                        labels: ['Tersedia', 'Terjual', 'Rusak'],
                        datasets: [{
                            data: [500, 250, 50],
                            backgroundColor: ['#4CAF50', '#F1EFFB', '#604008'],
                            hoverOffset: 4
                        }]
                    };

                    const configKayuTPK = {
                        type: 'doughnut',
                        data: dataKayuTPK,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    };

                    const ctxKayuTPK = document.getElementById('kayuTPKChart');
                    if (ctxKayuTPK) {
                        new Chart(ctxKayuTPK, configKayuTPK);
                    }
                });
            </script>

            <style>
                /* Tambahkan gaya ini agar bar berubah warna saat hover */
                #bibitChart .apexcharts-bar-series .apexcharts-series path:hover {
                    fill: #8DD88D !important;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var options = {
                        series: [{
                            name: 'Jumlah Bibit',
                            data: [500, 400, 350, 250, 600, 700, 450, 380, 520, 610, 330, 410]
                        }],
                        chart: {
                            type: 'bar',
                            height: 400
                        },
                        colors: ['#F2FCF1'], // Warna dasar chart
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '75%',
                                borderRadius: 10,
                            }
                        },
                        dataLabels: {
                            enabled: false,
                            style: {
                                colors: ['#000']
                            }
                        },
                        stroke: {
                            show: false
                        },
                        xaxis: {
                            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                                'Dec'
                            ]
                        },
                        yaxis: {
                            title: {
                                text: 'Jumlah Bibit'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + " bibit";
                                }
                            }
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#bibitChart"), options);
                    chart.render();
                });
            </script>
        @endsection
