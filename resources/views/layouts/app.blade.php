<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GreenTrack')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="flex">
        @include('components.sidebar') <!-- Sidebar akan tampil di semua halaman -->

        <div class="flex-1 p-6">
            <!-- Mengatur konten utama agar menempati ruang penuh -->
            @yield('content') <!-- Tempat untuk konten halaman -->
        </div>
    </div>
</body>

</html>
