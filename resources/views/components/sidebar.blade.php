<!-- Tombol Hamburger -->

<button id="menu-btn" class="p-5 text-3xl bg-transparent text-green-600 rounded-lg fixed top-4 left-2 z-50">
    ☰
</button>

<!-- Overlay -->
<div id="overlay"
    class="fixed inset-0 bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Sidebar -->
<aside id="sidebar"
    class="w-240 md:w-80 bg-white h-screen shadow-md fixed top-0 left-0 transform -translate-x-full transition-transform duration-300 flex flex-col justify-between">

    <div>
        <!-- Header dengan Logo -->
        <div class="p-4 font-bold text-green-600 text-xl flex justify-between items-center">
            <img src="https://via.placeholder.com/30" alt="Logo" class="mr-2">
            GreenTrack
            <button id="close-btn" class="text-gray-700 text-2xl">✕</button>
        </div>

        <ul id="menu-list" class="mt-6">
            <li data-menu="dashboard"
                class="p-3 rounded-lg flex justify-between items-center w-[85%] mx-auto cursor-pointer">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <button class="text-gray-500">></button>
            </li>
            <li data-menu="manajemen-kayu"
                class="p-3 rounded-lg flex justify-between items-center w-[85%] mx-auto cursor-pointer">
                <a href="{{ route('manajemenkayubibit') }}">Manajemen Kayu & Bibit</a>
                <button class="text-gray-500">></button>
            </li>
            <li data-menu="manajemen-pengguna"
                class="p-3 rounded-lg flex justify-between items-center w-[85%] mx-auto cursor-pointer">
                {{-- <a href="{{ route('manajemenpengguna') }}">Manajemen Pengguna</a> --}}
                <button class="text-gray-500">></button>
            </li>
            <li data-menu="histori-scan"
                class="p-3 rounded-lg flex justify-between items-center w-[85%] mx-auto cursor-pointer">
                {{-- <a href="{{ route('historybarcode') }}">History Scan Barcode</a> --}}
                <button class="text-gray-500">></button>
            </li>
            <li data-menu="manajemen-profil"
                class="p-3 rounded-lg flex justify-between items-center w-[85%] mx-auto cursor-pointer">
                {{-- <a href="{{ route('manajemenprofil') }}">Manajemen Profil</a> --}}
                <button class="text-gray-500">></button>
            </li>
        </ul>
    </div>

    <!-- Profil di bagian bawah -->
    <div class="p-4 border-t flex items-center w-[85%] mx-auto mb-6">
        <img src="https://via.placeholder.com/50" alt="Profile" class="w-12 h-12 rounded-full">
        <div class="ml-3">
            <p class="text-gray-800 font-semibold">Nama Pengguna</p>
            <p class="text-gray-600 text-sm">Role Pengguna</p>
        </div>
    </div>
</aside>

<!-- Script -->
<script>
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const body = document.body;
    const menuItems = document.querySelectorAll("#menu-list li");

    function showSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        menuBtn.classList.add('hidden');
        body.classList.add('overflow-hidden'); // Mencegah scroll saat sidebar terbuka
    }

    function hideSidebar() {
        sidebar.classList.add('-translate-x-full');
        menuBtn.classList.remove('hidden');
        body.classList.remove('overflow-hidden');

        // Tunggu transisi sebelum menyembunyikan overlay
        setTimeout(() => {
            overlay.classList.add('opacity-0', 'pointer-events-none');
        }, 300);
    }

    function setActiveMenu(menuName) {
        menuItems.forEach(item => {
            item.classList.remove('bg-green-100', 'text-green-600', 'font-semibold');
        });
        const activeItem = document.querySelector(`[data-menu="${menuName}"]`);
        if (activeItem) {
            activeItem.classList.add('bg-green-100', 'text-green-600', 'font-semibold');
        }
    }

    menuBtn.addEventListener('click', showSidebar);
    closeBtn.addEventListener('click', hideSidebar);
    overlay.addEventListener('click', hideSidebar);

    // Event listener untuk menetapkan menu aktif saat diklik
    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            const menuName = item.getAttribute('data-menu');
            setActiveMenu(menuName);
        });
    });

    // Atur menu default yang aktif
    setActiveMenu("dashboard");
</script>
