<!-- Tombol Hamburger -->
<button id="menu-btn" class="p-5 text-3xl bg-transparent text-green-600 rounded-lg fixed top-4 left-2 z-50">
    ☰
</button>

<!-- Overlay -->
<div id="overlay"
    class="fixed inset-0 bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 md:w-80 bg-white h-screen shadow-md fixed top-0 left-0 transform -translate-x-full transition-transform duration-300 flex flex-col justify-between">

    <div>
        <!-- Header dengan Logo -->
        <div class="p-4 font-bold text-green-600 text-xl flex justify-between items-center">
            <img src="{{ asset('assets/images/forest.svg') }}" alt="Logo" class="mr-2">
            GreenTrack
            <button id="close-btn" class="text-gray-700 text-2xl">✕</button>
        </div>

        <ul id="menu-list" class="mt-6">
            <li data-menu="dashboard"
                class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/dashboard.svg') }}" alt="Dashboard" class="icon w-6 h-6">
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li data-menu="manajemen-kayu-bibit"
                class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                <a href="{{ route('manajemenkayubibit') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/manajemenbibit.svg') }}" alt="Data Bibit & Kayu"
                        class="icon w-6 h-6">
                    <span class="menu-text">Data Bibit & Kayu</span>
                </a>
            </li>
            <li data-menu="manajemen-pengguna"
                class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                <a href="#" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/pengguna.svg') }}" alt="Manajemen Pengguna" class="icon w-6 h-6">
                    <span class="menu-text">Manajemen Pengguna</span>
                </a>
            </li>
            <li data-menu="history-scan"
                class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                <a href="#" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/history.svg') }}" alt="History Scan Barcode" class="icon w-6 h-6">
                    <span class="menu-text">History Scan Barcode</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Profil di bagian bawah -->
    <div class="p-4 border-t flex items-center w-[90%] mx-auto mb-6">
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
        body.classList.add('overflow-hidden');
    }

    function hideSidebar() {
        sidebar.classList.add('-translate-x-full');
        menuBtn.classList.remove('hidden');
        body.classList.remove('overflow-hidden');

        setTimeout(() => {
            overlay.classList.add('opacity-0', 'pointer-events-none');
        }, 300);
    }

    function setActiveMenu(menuName) {
        menuItems.forEach(item => {
            item.classList.remove('bg-green-500', 'text-white', 'font-bold');
            item.querySelector('.menu-text').classList.remove('text-white');
            item.querySelector('.menu-text').classList.add('text-gray-600');
            item.querySelector('.icon').style.filter = "brightness(1)"; // Ikon normal
        });

        const activeItem = document.querySelector(`[data-menu="${menuName}"]`);
        if (activeItem) {
            activeItem.classList.add('bg-green-500', 'font-bold');
            activeItem.querySelector('.menu-text').classList.remove('text-gray-600');
            activeItem.querySelector('.menu-text').classList.add('text-white');
            activeItem.querySelector('.icon').style.filter = "brightness(0) invert(1)"; // Ikon putih
        }

        localStorage.setItem("activeMenu", menuName);
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

    // Ambil menu aktif dari localStorage saat halaman dimuat
    document.addEventListener("DOMContentLoaded", () => {
        const savedMenu = localStorage.getItem("activeMenu") || "dashboard";
        setActiveMenu(savedMenu);
    });
</script>
