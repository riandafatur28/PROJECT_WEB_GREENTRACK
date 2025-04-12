<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GreenTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        #kayuTPKChart {
            width: 100% !important;
            height: 300px !important;
        }
    </style>

    <!-- <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style> -->
</head>


<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Header -->
            <img src="assets/images/logo_greentrack.png" alt="logo">
            <img src="assets/images/Qr_Code.png" alt="qr code">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">GreenTrack</h1>
            <div class="text-gray-700">
                <span class="font-semibold">Hello Fitri</span>
            </div>
        </div>

        <!-- Combined Card for Total Bibit, Jumlah Kayu, dan Sedang Aktif -->
        <!-- Combined Card for Total Bibit, Jumlah Kayu, dan Sedang Aktif -->
        <div class="bg-white p-6 rounded-3xl shadow mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center px-24">
                <!-- Total Bibit -->
                <div class="flex items-center space-x-6">
                    <img src="assets/images/icon_bibit.png" alt="Bibit Pohon" class="w-16 h-16 rounded-full">
                    <div>
                        <h4 class="text-gray-600">Total Bibit</h4>
                        <p class="text-2xl font-bold" id="totalBibit">5,423</p>
                        <p class="text-sm text-green-500">↑ tex bulan ini</p>
                    </div>
                </div>

                <!-- Jumlah Kayu -->
                <div class="flex items-center space-x-6 text-center relative">
                    <img src="assets/images/pohon.png" alt="Pohon" class="w-16 h-16 rounded-full">
                    <div>
                        <h4 class="text-gray-600">Jumlah Kayu</h4>
                        <p class="text-2xl font-bold" id="jumlahKayu">1,893</p>
                        <p class="text-sm text-red-500">↓ tx bulan ini</p>
                    </div>
                    <!-- Spinner Animation -->
                    <!-- <div id="spinner"
                        class="hidden absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-gray-900"></div>
                    </div> -->
                </div>

                <!-- Sedang Aktif -->
                <div class="flex items-center space-x-6 text-center">
                    <img src="assets/images/komputer.png" alt="Komputer" class="w-16 h-16 rounded-full">
                    <div>
                        <h4 class="text-gray-600">Sedang Aktif</h4>
                        <p class="text-2xl font-bold" id="sedangAktif">9</p>
                        <!-- <p class="text-sm">$0.000</p> -->
                    </div>
                </div>
            </div>
        </div>





        <!-- Middle Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Bibli Dalam Penyemaian -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-3x1 font-bold">Bibit Dalam Penyemaian</h3>
                <p class="text-1xl font-poppins">Total Bibit 1500</p>
                <div id="bibitChart"></div> <!-- Tempat Chart -->
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold">Kayu di TPK</h3>
                <p class="text-2xl font-poppins">Total kayu 800</p>
                <div style="height: 300px;">
                    <canvas id="kayuTPKChart"></canvas>
                </div>
            </div>



            <!-- Quarterly -->
            <!-- <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h3 class="text-lg font-semibold">Quarterly</h3>
            <p class="text-2xl font-bold">25:35</p>
        </div> -->

            <!-- Aktifitas Terbaru -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Aktifitas Terbaru</h3>
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Janis Aktifitas</th>
                            <th class="text-left">Waktu</th>
                            <th class="text-left">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2">Fitri Meydayani</td>
                            <td class="py-2">Tambah Data Bibli -f</td>
                            <td class="py-2">5 menit lalu</td>
                            <td class="py-2">20</td>
                        </tr>
                        <tr>
                            <td class="py-2">Rianda</td>
                            <td class="py-2">Scan Barcode Kayu ●</td>
                            <td class="py-2">1 jam lalu</td>
                            <td class="py-2">20</td>
                        </tr>
                        <tr>
                            <td class="py-2">Yulia Gita</td>
                            <td class="py-2">Cetak Barcode Bibli ●</td>
                            <td class="py-2">Hari ini</td>
                            <td class="py-2">20</td>
                        </tr>
                        <tr>
                            <td class="py-2">Huda</td>
                            <td class="py-2">Scan Barcode Kayu ●</td>
                            <td class="py-2">1 hari lalu</td>
                            <td class="py-2">20</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Data untuk chart Kayu di TPK
                const dataKayuTPK = {
                    labels: ['Tersedia', 'Terjual', 'Rusak'],
                    datasets: [{
                        data: [500, 250, 50], // Contoh data: tersedia, terjual, rusak
                        backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
                        hoverOffset: 4
                    }]
                };

                // Konfigurasi chart
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

                // Render chart di canvas
                const ctxKayuTPK = document.getElementById('kayuTPKChart').getContext('2d');
                new Chart(ctxKayuTPK, configKayuTPK);
            });
        </script>

        <script>
            var options = {
                series: [{
                    name: 'Jumlah Bibit',
                    data: [500, 400, 350, 250, 600, 700, 450, 380, 520, 610, 330, 410] // Data jumlah bibit
                }],
                chart: {
                    type: 'bar',
                    height: 400
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '75%', // Lebar batang
                        borderRadius: 10, // Membuat ujung batang lebih tumpul
                    },
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#000'] // Warna angka label
                    }
                },
                stroke: {
                    show: false, // Menghilangkan garis tepi
                },
                xaxis: {
                    categories: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ], // Nama jenis bibit
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
                        formatter: function (val) {
                            return val + " bibit";
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#bibitChart"), options);
            chart.render();
        </script>



</body>

</html>