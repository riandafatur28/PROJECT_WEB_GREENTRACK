<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenTrack | @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>


</head>

<body class="bg-sky-50">
    <div class="flex">
        @include('components.sidebar') <!-- Sidebar akan tampil di semua halaman -->

        <div class="flex-1 p-6">
            <!-- Mengatur konten utama agar menempati ruang penuh -->
            @yield('content') <!-- Tempat untuk konten halaman -->
        </div>
    </div>
</body>

</html>
