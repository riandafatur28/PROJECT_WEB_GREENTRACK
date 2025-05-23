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
    class="w-64 md:w-80 bg-white h-screen shadow-md fixed top-0 left-0 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between z-50">

    <div>
        <!-- Header dengan Logo -->
        <div class="p-4 font-bold text-green-600 text-xl flex justify-between items-center">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="Logo" class="mr-2">
            GreenTrack
            <button id="close-btn" class="text-gray-700 text-2xl">✕</button>
        </div>

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
                    <img src="{{ asset('assets/images/pengguna.svg') }}" alt="Manajemen Pengguna" class="icon w-6 h-6">
                    <span class="menu-text">Manajemen Pengguna</span>
                </a>
            </li>
            <li class="p-3 rounded-lg flex justify-between items-center w-[90%] mx-auto cursor-pointer transition">
                <a href="{{ route('historyscanbarcode') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/history.svg') }}" alt="History Scan Barcode" class="icon w-6 h-6">
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

    <!-- Logout Button -->
    <div class="flex justify-center mb-2">
        <button id="logoutButton" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="bg-red-600 text-white px-3 py-3 rounded-lg text-lg hover:bg-red-700 w-full mt-80 flex items-center justify-center gap-4 ml-4 mr-4">
            <!-- Logout Icon -->
            <img src="{{ asset('assets/images/logout.png') }}" alt="Logout"
                class="w-6 h-6 filter brightness-0 invert">
            <span>Keluar</span>
        </button>
    </div>



    <!-- Foto Profil -->
    <a href="{{ route('profile') }}"
        class="block p-4 border-t flex items-center w-[90%] mx-auto mb-6 hover:bg-gray-100 rounded-lg transition"
        id="profile-link">
        <img src="{{ asset('assets/images/profile.jpg') }}" alt="Profile"
            class="w-12 h-12 rounded-full object-cover border-2 border-green-500 shadow-sm">
        <div class="ml-3">
            <p class="text-gray-800 font-semibold menu-text">{{ session('user_nama') }}</p>
            <p class="text-gray-600 text-sm">{{ session('role') }}</p>
        </div>
    </a>
</aside>

<!-- SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const body = document.body;
        const menuItems = document.querySelectorAll("#menu-list li");
        const profileLink = document.getElementById('profile-link');

        function showSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('pointer-events-auto');
            menuBtn.classList.add('hidden');
            body.classList.add('overflow-hidden');
        }

        function hideSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('pointer-events-auto');
            menuBtn.classList.remove('hidden');
            body.classList.remove('overflow-hidden');
        }

        // Logout
        document.getElementById('logoutButton').addEventListener('click', function() {
            window.location.href = '{{ route('login') }}';
        });

        function setActiveMenuByURL() {
            const currentPath = window.location.pathname;

            menuItems.forEach(item => {
                const link = item.querySelector('a');
                const menuText = item.querySelector('.menu-text');
                const icon = item.querySelector('.icon');

                if (link && currentPath.startsWith(new URL(link.href, window.location.origin)
                        .pathname)) {
                    item.classList.add('bg-green-500', 'font-bold');
                    if (menuText) {
                        menuText.classList.remove('text-gray-600');
                        menuText.classList.add('text-white');
                    }
                    if (icon) {
                        icon.style.filter = "brightness(0) invert(1)";
                    }
                } else {
                    item.classList.remove('bg-green-500', 'font-bold');
                    if (menuText) {
                        menuText.classList.remove('text-white');
                        menuText.classList.add('text-gray-600');
                    }
                    if (icon) {
                        icon.style.filter = "brightness(1)";
                    }
                }
            });

            if (profileLink) {
                const profilePath = new URL(profileLink.href, window.location.origin).pathname;
                const profileText = profileLink.querySelector('.menu-text');
                if (currentPath.startsWith(profilePath)) {
                    profileLink.classList.add('bg-green-500');
                    profileText.classList.remove('text-gray-800');
                    profileText.classList.add('text-white');
                } else {
                    profileLink.classList.remove('bg-green-500');
                    profileText.classList.remove('text-white');
                    profileText.classList.add('text-gray-800');
                }
            }
        }

        setActiveMenuByURL();

        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    hideSidebar();
                }
            });
        });

        menuBtn.addEventListener('click', showSidebar);
        closeBtn.addEventListener('click', hideSidebar);
        overlay.addEventListener('click', hideSidebar);
    });
</script>
