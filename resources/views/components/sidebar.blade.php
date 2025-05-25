<!-- Tombol Hamburger -->
<button id="menu-btn" class="p-5 text-3xl bg-transparent text-green-600 rounded-lg fixed top-6 left-2 z-60">
    ☰
</button>

<!-- Overlay -->
<div id="overlay"
    class="fixed inset-0 bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-40">
</div>

<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 md:w-80 bg-white h-screen shadow-md fixed top-0 left-0 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col z-50">
    <div class="flex flex-col h-full overflow-hidden">
        <!-- Header dengan Logo -->
        <div class="p-4 font-bold text-green-600 text-xl flex justify-between items-center">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="Logo" class="mr-2">
            GreenTrack
            <button id="close-btn" class="text-gray-700 text-2xl">✕</button>
        </div>

        <!-- Scrollable Menu Area -->
        <div
            class="flex-1 overflow-y-auto overflow-x-hidden scrollable-content max-h-[calc(100vh-280px)] md:max-h-none">
            <ul id="menu-list" class="mt-6">
                <li class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('assets/images/dashboard.svg') }}" alt="Dashboard" class="icon w-6 h-6">
                        <span class="menu-text">Beranda</span>
                    </a>
                </li>
                <li class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                    <a href="{{ route('manajemenkayubibit') }}" class="flex items-center gap-2">
                        <img src="{{ asset('assets/images/manajemenbibit.svg') }}" alt="Data Bibit & Kayu"
                            class="icon w-6 h-6">
                        <span class="menu-text">Data Bibit & Kayu</span>
                    </a>
                </li>
                <li class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                    <a href="{{ route('manajemenpengguna.index') }}" class="flex items-center gap-2">
                        <img src="{{ asset('assets/images/pengguna.svg') }}" alt="Manajemen Pengguna"
                            class="icon w-6 h-6">
                        <span class="menu-text">Manajemen Pengguna</span>
                    </a>
                </li>
                <li class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                    <a href="{{ route('historyscanbarcode') }}" class="flex items-center gap-2">
                        <img src="{{ asset('assets/images/history.svg') }}" alt="History Scan Barcode"
                            class="icon w-6 h-6">
                        <span class="menu-text">Aktivitas Terbaru</span>
                    </a>
                </li>
                <li class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                    <a href="{{ route('historyperawatan') }}" class="flex items-center gap-2">
                        <img src="{{ asset('assets/images/monitoring.svg') }}" alt="Jadwal Perawatan Bibit"
                            class="icon w-6 h-6">
                        <span class="menu-text">Riwayat Perawatan</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Fixed Bottom Section -->
        <div class="mt-4 flex flex-col pb-4">
            <div class="flex flex-col md:flex-col-reverse">
                <!-- Logout Button -->
                <!-- Logout Button - Update the container and form classes -->
                <div class="flex justify-center order-last md:order-first w-full px-4">
                    <form action="{{ route('login') }}" method="GET" id="logout-form" class="w-full">
                        @csrf
                        <button type="submit" id="logoutButton"
                            class="bg-red-600 text-white px-3 py-2.5 rounded-lg text-lg hover:bg-red-700 w-full flex items-center justify-center gap-4">
                            <img src="{{ asset('assets/images/logout.png') }}" alt="Logout"
                                class="w-6 h-6 filter brightness-0 invert">
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>

                <!-- Foto Profil -->
                <a href="{{ route('profile') }}"
                    class="block py-3 px-4 border-t flex items-center w-[90%] mx-auto hover:bg-gray-100 rounded-lg transition order-first md:order-last mb-3"
                    id="profile-link">
                    <img src="{{ asset('assets/images/profile.jpg') }}" alt="Profile"
                        class="w-10 h-10 rounded-full object-cover border-2 border-green-500 shadow-sm">
                    <div class="ml-3">
                        <p class="text-gray-800 font-semibold menu-text">{{ session('user_nama') }}</p>
                        <p class="text-gray-600 text-sm">{{ session('role') }}</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</aside>

<style>
    @media (max-width: 768px) {
        body.sidebar-open {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden !important;
            touch-action: none !important;
        }

        #sidebar {
            width: 100vw !important;
            height: 100vh !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            overflow: hidden !important;
            touch-action: none !important;
            -webkit-overflow-scrolling: touch !important;
            overscroll-behavior: none !important;
        }

        .scrollable-content {
            max-height: calc(100vh - 280px) !important;
            padding-bottom: 1rem !important;
        }

        #profile-link {
            border-top: none !important;
            padding: 0.75rem 1rem !important;
            margin-bottom: 0.5rem !important;
        }

        #logoutButton {
            padding: 0.625rem 1rem !important;
            margin-bottom: 0.75rem !important;
            width: 100% !important;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const body = document.body;
        const menuItems = document.querySelectorAll("#menu-list li");
        const profileLink = document.getElementById('profile-link');
        const logoutForm = document.getElementById('logout-form');

        function showSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            menuBtn.classList.add('hidden');
            body.classList.add('sidebar-open');
        }

        function hideSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            menuBtn.classList.remove('hidden');
            body.classList.remove('sidebar-open');
        }

        menuBtn.addEventListener('click', showSidebar);
        closeBtn.addEventListener('click', hideSidebar);
        overlay.addEventListener('click', hideSidebar);

        // Close sidebar when clicking menu items on mobile
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    hideSidebar();
                }
            });
        });

        // Prevent scroll/touch events
        overlay.addEventListener('touchmove', (e) => e.preventDefault(), {
            passive: false
        });
        sidebar.addEventListener('touchmove', (e) => {
            if (!e.target.closest('.scrollable-content')) {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        // Set active menu item
        function setActiveMenuByURL() {
            const currentPath = window.location.pathname;
            menuItems.forEach(item => {
                const link = item.querySelector('a');
                const menuText = item.querySelector('.menu-text');
                const icon = item.querySelector('.icon');

                if (link && currentPath.startsWith(new URL(link.href, window.location.origin)
                        .pathname)) {
                    item.classList.add('bg-green-500', 'font-bold');
                    if (menuText) menuText.classList.add('text-white');
                    if (icon) icon.style.filter = "brightness(0) invert(1)";
                }
            });

            if (profileLink && currentPath.startsWith(new URL(profileLink.href, window.location.origin)
                    .pathname)) {
                profileLink.classList.add('bg-green-500');
                const profileText = profileLink.querySelector('.menu-text');
                if (profileText) profileText.classList.add('text-white');
            }
        }

        setActiveMenuByURL();
    });
</script>
