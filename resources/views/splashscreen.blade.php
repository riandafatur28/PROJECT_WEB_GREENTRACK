<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Splash Screen</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.1.0/dist/tailwind.min.css"></script>
    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .fade-in-image {
            animation: fadeIn 2s ease-in-out;
        }
    </style>
</head>

<body class="h-screen bg-green-500 flex justify-center items-center">

    <!-- Splash screen container -->
    <div class="relative w-64 h-64 bg-white rounded-3xl shadow-lg flex justify-center items-center">
        <!-- QR Code & Tree Icon as Background with animation -->
        <div class="absolute w-32 h-32 bg-cover bg-center rounded-xl fade-in-image"
            style="background-image: url('{{ asset('assets/images/greentrack.svg') }}');">
        </div>
    </div>

    <script>
        // Redirect to landing page after 3 seconds
        setTimeout(function() {
            window.location.href = "{{ route('landingpage') }}"; // Ensure landing page route is correct
        }, 3000); // 3000 milliseconds = 3 seconds
    </script>

</body>

</html>
