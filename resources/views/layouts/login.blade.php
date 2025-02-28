<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GreenTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="flex items-center justify-center h-screen">
    <!-- Bagian Kiri: Form Login -->
    <div class="w-full sm:w-full md:w-3/5 flex items-center justify-center bg-white-100 p-6">
        <div class="w-full max-w-sm sm:max-w-xs md:max-w-lg p-8 overflow-auto">
            <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold text-left text-gray-700 mb-6">
                Selamat Datang 👋
            </h1>

            <h3 class="text-sm sm:text-lg md:text-xl text-left text-gray-700 mb-12">Silahkan Login Terlebih Dahulu</h3>
            <form method="POST" action="#" autocomplete="off">
                <div>
                    <label class="block text-sm sm:text-base md:text-lg font-medium text-gray-700 mb-1"
                        for="email">Email</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none"
                        id="email" type="email" name="email" value="" required aria-label="Email">
                </div>
                <div class="mt-4">
                    <label class="block text-sm sm:text-base md:text-lg font-medium text-gray-700 mb-1"
                        for="password">Password</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none"
                        id="password" type="password" name="password" value="" required aria-label="Password">
                </div>
                <div class="mt-4 flex items-center">
                    <input type="checkbox" id="remember_me" name="remember" class="mr-2 cursor-pointer">
                    <label for="remember_me" class="text-sm md:text-base text-gray-600 cursor-pointer">Remember
                        me</label>
                </div>
                <div class="mt-6">
                    <button
                        class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                        Login
                    </button>
                </div>
                <div class="mt-6">
                    <button
                        class="w-full bg-white text-green-600 py-2 rounded-md border border-green-600 hover:bg-green-200 hover:text-green">
                        Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bagian Kanan: Gambar hanya muncul di layar besar -->
    <div class="hidden lg:block lg:w-2/5 w-full h-screen flex items-center justify-center bg-white-100 p-4">
        <!-- Menambahkan padding di semua sisi -->
        <div class="w-full h-full bg-cover bg-center bg-no-repeat rounded-xl"
            style="background-image: url('/build/assets/images/Art.svg');">
        </div>
    </div>

</body>

</html>
