<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Sandi | GreenTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="font-inter bg-gray-100">

    <div class="w-full h-screen flex items-center justify-center p-6">
        <!-- Bagian Kiri: Form Login -->
        <div class="w-full sm:w-11/12 md:w-9/12 lg:w-1/2 xl:w-1/3 p-8 sm:p-10 bg-white rounded-lg shadow-lg">
            <div class="w-full max-w-sm sm:max-w-xs md:max-w-lg lg:max-w-xl xl:max-w-2xl p-8 overflow-auto">
                <h2 class="text-lg sm:text-xl font-bold">Lupa Kata Sandi? 🔐</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-4 mb-12">Jangan khawatir! Masukkan email
                    yang terdaftar, dan kami akan mengirimkan tautan
                    untuk mengatur ulang kata sandi Anda.</p>
                <form method="POST" action="#" autocomplete="off">
                    <div>
                        <label class="block text-base sm:text-lg md:text-xl font-medium text-gray-700 mb-2"
                            for="email">Email</label>
                        <input
                            class="w-full px-4 py-3 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
                            id="email" type="email" name="email" value="" required aria-label="Email">
                    </div>
                    <div class="mt-6">
                        <a href="resendotp"
                            class="w-full bg-green-600 text-white py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 block text-center">Kirim
                            Kode OTP</a>
                    </div>
                    <div class="mt-6">
                        <!-- Tombol kembali yang sekarang berfungsi sebagai link menuju halaman login -->
                        <a href="login"
                            class="w-full bg-white text-green-600 py-3 rounded-md border border-green-600 hover:bg-green-200 hover:text-green text-center block">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
