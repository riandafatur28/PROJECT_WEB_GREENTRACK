@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Hello Fitri 👋</h1>
            <img src="{{ asset('assets/images/forest.svg') }}" alt="logo" class="h-10">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                1% bulan ini
                            </p>
                        </div>
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
                <p class="text-base text-gray-600 mb-4">Total Bibit 1500</p>
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

            <!-- Aktivitas -->
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Aktifitas Terbaru</h3>
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-gray-700 font-semibold">
                        <tr>
                            <th class="py-2">Nama</th>
                            <th class="py-2">Aktifitas</th>
                            <th class="py-2">Waktu</th>
                            <th class="py-2">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2">Fitri Meydayani</td>
                            <td class="py-2">Tambah Data Bibit</td>
                            <td class="py-2">5 menit lalu</td>
                            <td class="py-2">20</td>
                        </tr>
                        <tr>
                            <td class="py-2">Rianda</td>
                            <td class="py-2">Scan Barcode Kayu</td>
                            <td class="py-2">1 jam lalu</td>
                            <td class="py-2">20</td>
                        </tr>
                        <tr>
                            <td class="py-2">Yulia Gita</td>
                            <td class="py-2">Cetak Barcode Bibit</td>
                            <td class="py-2">Hari ini</td>
                            <td class="py-2">20</td>
                        </tr>
                        <tr>
                            <td class="py-2">Huda</td>
                            <td class="py-2">Scan Barcode Kayu</td>
                            <td class="py-2">1 hari lalu</td>
                            <td class="py-2">20</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
                    backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
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

    <!-- Script Bibit Chart -->
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
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '75%',
                        borderRadius: 10
                    }
                },
                dataLabels: {
                    enabled: true,
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
