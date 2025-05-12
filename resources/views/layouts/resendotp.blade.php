<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Pengiriman Tautan | GreenTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex justify-center items-center">

    <!-- Pop-up Notification -->
    <div class="bg-white p-6 rounded-lg shadow-md text-center w-full max-w-xs sm:max-w-md md:max-w-lg lg:max-w-xl">
        <h2 class="text-xl sm:text-2xl font-bold text-green-600">Tautan Reset Kata Sandi Telah Dikirimkan 📧</h2>
        <p class="text-sm sm:text-lg text-gray-600 mt-4 mb-12">
            Kami telah mengirimkan tautan untuk memperbarui kata sandi ke email yang Anda masukkan sebelumnya.<br>
            Silakan periksa email Anda dan ikuti instruksi untuk mereset kata sandi.
        </p>

        <!-- Button to Close/Go Back -->
        <div class="mt-8">
            <a href="login"
                class="w-full bg-green-600 text-white py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                Kembali ke Halaman Login
            </a>
        </div>

        <!-- Optionally, add a timer for re-sending the link -->
        <div class="mt-6 text-sm text-gray-500">
            <span>Jika Anda tidak menerima email, klik <a href="forgotpassword"
                    class="text-green-600 hover:text-green-700">di sini</a> untuk mengirim ulang.</span>
        </div>
    </div>

</body>

</html>
