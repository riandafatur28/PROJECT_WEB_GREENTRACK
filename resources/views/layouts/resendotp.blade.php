<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode OTP | GreenTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
    <div class="bg-white p-6 rounded-lg shadow-md text-center w-full max-w-xs sm:max-w-md md:max-w-lg lg:max-w-xl">
        <h2 class="text-xl sm:text-2xl font-bold">Verifikasi Kode OTP 📩</h2>
        <p class="text-sm sm:text-lg text-gray-600 mt-8 mb-16">Kami telah mengirimkan kode verifikasi ke
            email.<br>Masukkan
            kode di bawah ini untuk melanjutkan.</p>
        <div class="flex justify-center gap-2 sm:gap-3 md:gap-4 lg:gap-6 mt-6">
            <input type="text" maxlength="1"
                class="otp-input w-10 h-10 sm:w-12 sm:h-12 text-center text-xl border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" pattern="[0-9]*" inputmode="numeric">
            <input type="text" maxlength="1"
                class="otp-input w-10 h-10 sm:w-12 sm:h-12 text-center text-xl border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" pattern="[0-9]*" inputmode="numeric">
            <input type="text" maxlength="1"
                class="otp-input w-10 h-10 sm:w-12 sm:h-12 text-center text-xl border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" pattern="[0-9]*" inputmode="numeric">
            <input type="text" maxlength="1"
                class="otp-input w-10 h-10 sm:w-12 sm:h-12 text-center text-xl border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" pattern="[0-9]*" inputmode="numeric">
            <input type="text" maxlength="1"
                class="otp-input w-10 h-10 sm:w-12 sm:h-12 text-center text-xl border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" pattern="[0-9]*" inputmode="numeric">
            <input type="text" maxlength="1"
                class="otp-input w-10 h-10 sm:w-12 sm:h-12 text-center text-xl border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" pattern="[0-9]*" inputmode="numeric">
        </div>
        <div class="mt-16">
            <button
                class="w-full bg-green-600 text-white py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                Kirim Ulang Kode OTP dalam [ ] Detik
            </button>
        </div>
        <div class="mt-6">
            <!-- Tombol kembali yang sekarang berfungsi sebagai link menuju halaman login -->
            <a href="forgotpassword"
                class="w-full bg-white text-green-600 py-3 rounded-md border border-green-600 hover:bg-green-200 hover:text-green text-center block">
                Kembali
            </a>
        </div>
    </div>


    </div>

    <script>
        function moveToNext(input) {
            if (input.value.length === 1) {
                let next = input.nextElementSibling;
                if (next && next.classList.contains('otp-input')) {
                    next.focus();
                }
            }
        }

        function moveToPrev(event, input) {
            if (event.key === "Backspace" && input.value === "") {
                let prev = input.previousElementSibling;
                if (prev && prev.classList.contains('otp-input')) {
                    prev.focus();
                }
            }
        }
    </script>
</body>

</html>
