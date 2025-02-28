<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>Login | GreenTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="flex items-center justify-center h-screen">
    <div class="w-full sm:w-full md:w-3/5 flex items-center justify-center bg-white-100 p-6">
        <div class="w-full max-w-sm sm:max-w-xs md:max-w-lg p-8 overflow-auto">
            <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold text-left text-gray-700 mb-6">
                Selamat Datang 👋
            </h1>
            <h3 class="text-sm sm:text-lg md:text-xl text-left text-gray-700 mb-12">
                Silahkan Login Terlebih Dahulu
            </h3>
            <form method="POST" action="#" autocomplete="off">
                <div>
                    <label class="block text-sm sm:text-base md:text-lg font-medium text-gray-700 mb-1" for="email">
                        Email
                    </label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none"
                        id="email" type="email" name="email" required>
                </div>

                <div class="mt-4 relative">
                    <label class="block text-sm sm:text-base md:text-lg font-medium text-gray-700 mb-1" for="password">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none pr-10"
                            id="password" type="password" name="password" required>
                        <span id="togglePassword" class="absolute inset-y-0 right-3 flex items-center cursor-pointer">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" />
                            </svg>
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 hidden"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-6.5 0-10-7-10-7a18.64 18.64 0 015.522-5.503M9.88 9.88a3 3 0 104.24 4.24M3 3l18 18" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="mt-4 flex justify-end items-center">
                    <a href="forgotpassword" class="text-sm md:text-base text-gray-600 underline cursor-pointer">
                        Lupa Kata Sandi?
                    </a>
                </div>

                <div class="mt-6">
                    <button
                        class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                        Login
                    </button>
                </div>

                <div class="mt-6">
                    <button
                        class="w-full bg-white text-green-600 py-2 rounded-md border border-green-600 hover:bg-green-200">
                        Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="hidden lg:block lg:w-2/5 w-full h-screen flex items-center justify-center bg-white-100 p-4">
        <div class="w-full h-full bg-cover bg-center bg-no-repeat rounded-xl"
            style="background-image: url('/build/assets/images/Art.svg');">
        </div>
    </div>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            var passwordInput = document.getElementById("password");
            var eyeOpen = document.getElementById("eyeOpen");
            var eyeClosed = document.getElementById("eyeClosed");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeOpen.classList.add("hidden");
                eyeClosed.classList.remove("hidden");
            } else {
                passwordInput.type = "password";
                eyeOpen.classList.remove("hidden");
                eyeClosed.classList.add("hidden");
            }
        });
    </script>
</body>

</html>
