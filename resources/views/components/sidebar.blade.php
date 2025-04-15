<!-- Tombol Hamburger -->
<button id="menu-btn" class="p-5 text-3xl bg-transparent text-green-600 rounded-lg fixed top-6 left-2 z-50">
    ☰
</button>

<!-- Overlay -->
<div id="overlay"
    class="fixed inset-0 bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 md:w-80 bg-white h-screen shadow-md fixed top-0 left-0 transform -translate-x-full transition-transform duration-300 flex flex-col justify-between z-50">

    <div>
        <!-- Header dengan Logo -->
        <div class="p-4 font-bold text-green-600 text-xl flex justify-between items-center">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="Logo" class="mr-2">
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
                <a href="{{ route('manajemenpengguna') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/pengguna.svg') }}" alt="Manajemen Pengguna" class="icon w-6 h-6">
                    <span class="menu-text">Manajemen Pengguna</span>
                </a>
            </li>
            <li data-menu="history-scan"
                class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                <a href="{{ route('historyscanbarcode') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/history.svg') }}" alt="History Scan Barcode" class="icon w-6 h-6">
                    <span class="menu-text">Riwayat Pindai Barcode</span>
                </a>
            </li>
            <li data-menu="jadwal-perawatan-bibit"
                class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                <a href="{{ route('historyperawatanbibit') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/monitoring.svg') }}" alt="Jadwal Perawatan Bibit"
                        class="icon w-6 h-6">
                    <span class="menu-text">Riwayat Perawatan Bibit</span>
                </a>
            </li>
        </ul>
    </div>

    <a href="{{ route('profile') }}"
        class="block p-4 border-t flex items-center w-[90%] mx-auto mb-6 hover:bg-gray-100 rounded-lg transition"
        data-menu="profile">
        <img src="https://via.placeholder.com/50" alt="Profile" class="w-12 h-12 rounded-full">
        <div class="ml-3">
            <p class="text-gray-800 font-semibold menu-text">Nama Pengguna</p>
            <p class="text-gray-600 text-sm">Role Pengguna</p>
        </div>
    </a>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", () => {
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
            overlay.classList.add('opacity-0', 'pointer-events-none');
            menuBtn.classList.remove('hidden');
            body.classList.remove('overflow-hidden');
        }

        function setActiveMenu(menuName) {
            console.log("Menu aktif:", menuName); // Debugging untuk cek menu yang aktif

            // Hapus semua class aktif dari menu lainnya
            menuItems.forEach(item => {
                item.classList.remove('bg-green-500', 'text-white', 'font-bold');
                item.querySelector('.menu-text').classList.remove('text-white');
                item.querySelector('.menu-text').classList.add('text-gray-600');
                if (item.querySelector('.icon')) {
                    item.querySelector('.icon').style.filter = "brightness(1)";
                }
            });

            // Aktifkan menu yang dipilih
            const activeItem = document.querySelector(`[data-menu="${menuName}"]`);
            if (activeItem) {
                activeItem.classList.add('bg-green-500', 'font-bold');
                activeItem.querySelector('.menu-text').classList.remove('text-gray-600');
                activeItem.querySelector('.menu-text').classList.add('text-white');
                if (activeItem.querySelector('.icon')) {
                    activeItem.querySelector('.icon').style.filter = "brightness(0) invert(1)";
                }
            }

            // Simpan menu aktif di localStorage
            localStorage.setItem("activeMenu", menuName);
        }

        // Set menu aktif saat halaman dimuat
        const savedMenu = localStorage.getItem("activeMenu") || "dashboard";
        setActiveMenu(savedMenu);

        // Event listener untuk klik menu
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                const menuName = item.getAttribute('data-menu');
                setActiveMenu(menuName);
            });
        });

        // Event listener untuk sidebar
        menuBtn.addEventListener('click', showSidebar);
        closeBtn.addEventListener('click', hideSidebar);
        overlay.addEventListener('click', hideSidebar);
    });
</script>
