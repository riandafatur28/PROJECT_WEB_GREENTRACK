@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ session('user_nama') }} 👋</h1>
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
                        <h2 class="text-xl font-bold text-black-600">{{ number_format($totalBibit) }}</h2>
                    </div>
                </div>

                <!-- Total Kayu -->
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/kayu.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Kayu</p>
                        <h2 class="text-xl font-bold text-black-600">{{ number_format($totalKayu) }}</h2>
                    </div>
                </div>

                <!-- Total Admin -->
                <div class="flex items-center px-4 py-4 md:py-0">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <img src="/assets/images/admin.svg" alt="New Icon" class="w-8 h-8">
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Admin</p>
                        <h2 class="text-xl font-bold text-black-600">{{ $totalAdmin }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart & Aktivitas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Bibit Chart -->
            <div class="bg-white p-6 rounded-3xl shadow">
                <h3 class="text-xl font-bold mb-2">Bibit Dalam Penyemaian</h3>
                <p class="text-base text-gray-600 mb-4">Total Bibit {{ number_format($totalBibit) }}</p>
                <div id="bibitChart" style="height: 400px;"></div>
            </div>

            <!-- Kayu TPK Chart -->
            <div class="bg-white p-6 rounded-3xl shadow">
                <h3 class="text-xl font-bold mb-2">Kayu di TPK</h3>
                <p class="text-base text-gray-600 mb-4">Total Kayu {{ number_format($totalKayu) }}</p>
                <div style="height: 300px;">
                    <canvas id="kayuTPKChart"></canvas>
                </div>
            </div>

            <div class="md:col-span-2 bg-white p-6 rounded-3xl shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Aktivitas Terbaru</h3>
                </div>

                <table class="w-full text-sm text-left text-gray-600 border-collapse">
                    <thead>
                        <tr class="text-[#B5B7C0] border-b border-[#B5B7C0]">
                            <th class="py-2">Nama</th>
                            <th class="py-2">Aktivitas</th>
                            <th class="py-2">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ !empty($activity['image']) ? $activity['image'] : 'https://i.pravatar.cc/64?u=' . urlencode($activity['nama']) }}"
                                            alt="{{ $activity['nama'] }}" class="w-12 h-12 rounded-full">
                                        <div class="ml-6">
                                            <div class="text-xl font-semibold">{{ $activity['nama'] }}</div>
                                            <div class="text-sm text-gray-400">{{ $activity['userRole'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">{{ $activity['keterangan'] }}</td>
                                <td class="py-4 font-semibold">{{ $activity['waktu'] }}</td>
                            </tr>
                        @empty
                            @foreach ($activities as $activity)
                                <div>
                                    <h4>{{ $activity['nama'] }}</h4>
                                    <p>{{ $activity['userRole'] }}</p>
                                    <p>{{ $activity['keterangan'] }}</p>
                                    <p>{{ $activity['waktu'] }}</p>
                                    <img src="{{ $activity['image'] }}" alt="{{ $activity['nama'] }}">
                                </div>
                            @endforeach

                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-400">Tidak ada aktivitas terbaru</td>
                            </tr>
                        @endforelse
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kayu TPK Chart (Doughnut Chart)
            const kayuData = @json($kayuData);
            
            const dataKayuTPK = {
                labels: ['Tersedia', 'Terjual', 'Rusak'],
                datasets: [{
                    data: [
                        kayuData.tersedia,
                        kayuData.terjual,
                        kayuData.rusak
                    ],
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
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                },
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value.toLocaleString('id-ID')} kayu`;
                                }
                            }
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var bibitCounts = @json($bibitCounts);

            var options = {
                series: [{
                    name: 'Jumlah Bibit',
                    data: bibitCounts
                }],
                chart: {
                    type: 'bar',
                    height: 400,
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#4CAF50'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '60%',
                        borderRadius: 6,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toLocaleString('id-ID');
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ['#304758']
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Bibit',
                        style: {
                            fontSize: '14px'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toLocaleString('id-ID');
                        }
                    }
                },
                fill: {
                    opacity: 1,
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.3,
                        opacityFrom: 0.9,
                        opacityTo: 0.8
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString('id-ID') + " bibit";
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    row: {
                        colors: ['transparent', 'transparent']
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#bibitChart"), options);
            chart.render();
        });
    </script>
@endsection
