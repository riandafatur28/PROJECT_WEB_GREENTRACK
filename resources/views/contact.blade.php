<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>GreenTrack | Kontak</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href={{ asset('assets/img/apple-touch-icon.png') }} rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Marcellus:wght@400&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href={{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }} rel="stylesheet">
    <link href={{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }} rel="stylesheet">
    <link href={{ asset('assets/vendor/aos/aos.css') }} rel="stylesheet">
    <link href={{ asset('assets/vendor/swiper/swiper-bundle.min.css') }} rel="stylesheet">
    <link href={{ asset('assets/vendor/glightbox/css/glightbox.min.css') }} rel="stylesheet">

    <!-- Main CSS File -->
    <link href={{ asset('assets/css/main.css') }} rel="stylesheet">
</head>

<body class="contact-page">

    <header id="header" class="header d-flex align-items-center mb-60 py-3">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <h1 class="text-green-500 text-xl font-bold font-sans">GreenTrack</h1>

            <nav id="navmenu" class="navmenu">
                <ul class="flex space-x-4">
                    <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
                    <li><a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">Tentang
                            Kami</a></li>
                    <li><a href="{{ url('/services') }}"
                            class="{{ request()->is('services') ? 'active' : '' }}">Layanan Kami</a></li>
                    <li><a href="{{ url('/contact') }}"
                            class="{{ request()->is('contact') ? 'active' : '' }}">Kontak</a></li>
                    <li><a href="{{ route('login') }}"
                            class="btn btn-success login-btn text-white bg-green-500 hover:bg-green-600 py-2 px-4 rounded-md font-semibold uppercase transition duration-300 ease-in-out">Login</a>
                    </li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </header>


    <main class="main mt-64"> <!-- Menambahkan margin-top untuk memberi ruang lebih besar -->
        <!-- Page Title -->
        <div class="page-title dark-background" data-aos="fade"
            style="background-image: url(assets/img/page-title-bg.webp);">
            <div class="container position-relative">
                <h1>Kontak</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="{{ url('/') }}"
                                class="{{ request()->is('/') ? 'current' : '' }}">Beranda</a></li>
                        <li class="current">Kontak</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->
    </main>




    <!-- Contact Section -->
    <section id="contact" class="contact section">

        <div class="mb-5 ml-8 mr-8">
            <iframe style="width: 100%; height: 400px;"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63284.33688646477!2d111.8378824286128!3d-7.545391501234625!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e784b07b58ae7ed%3A0xcd76227bd3b5d3!2sPerum%20Perhutani%20KPH%20Nganjuk!5e0!3m2!1sid!2sid!4v1744583897508!5m2!1sid!2sid"
                frameborder="0" allowfullscreen="">
            </iframe>
        </div><!-- End Google Maps -->


    </section><!-- /Contact Section -->

    </main>

    <footer id="footer" class="footer dark-background">

        <div class="footer-top">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6 footer-about">
                        <a href="index.html" class="logo d-flex align-items-center">
                            <span class="sitename">GreenTrack</span>
                        </a>
                        <div class="footer-contact pt-3">
                            <p>Perum Perhutani KPH Nganjuk</p>
                            <p>Jl. Merdeka No.6, Mangundikaran, Mangun Dikaran, Kec. Nganjuk, Kabupaten Nganjuk, Jawa
                                Timur 64419</p>
                            <p class="mt-3"><strong>Telepon:</strong> <span> 0358 321197 321729</span></p>
                            <p><strong>Email:</strong> <span>greentrack@gmail.com</span></p>
                        </div>
                    </div>

                    <!-- Menambahkan ml-auto pada kolom Useful Links untuk menggeser ke kanan -->
                    <div class="col-lg-2 col-md-3 footer-links ml-auto">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><a href="{{ url('/') }}"
                                    class="{{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
                            <li><a href="{{ url('/about') }}"
                                    class="{{ request()->is('about') ? 'active' : '' }}">Tentang Kami</a></li>
                            <li><a href="{{ url('/services') }}"
                                    class="{{ request()->is('services') ? 'active' : '' }}">Layanan Kami</a></li>
                            <li><a href="{{ url('/contact') }}"
                                    class="{{ request()->is('contact') ? 'active' : '' }}">Kontak</a></li>
                        </ul>
                    </div>

                    <!-- Menambahkan ml-auto pada kolom Fitur Aplikasi untuk menggeser ke kanan -->
                    <div class="col-lg-2 col-md-3 footer-links ml-auto">
                        <h4>Fitur Aplikasi</h4>
                        <ul>
                            <li><a href="#">Scan Barcode Bibit</a></li>
                            <li><a href="#">Riwayat Tanaman</a></li>
                            <li><a href="#">Kalender Perawatan</a></li>
                            <li><a href="#">Manajemen Admin</a></li>
                            <li><a href="#">Laporan Distribusi</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="copyright text-center">
            <div
                class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">

                <div class="d-flex flex-column align-items-center align-items-lg-start">
                    <div>
                        © Copyright <strong><span>GreenTrack</span></strong>. All Rights Reserved
                    </div>
                    <div class="credits">
                        <!-- All the links in the footer should remain intact. -->
                        <!-- You can delete the links only if you purchased the pro version. -->
                        <!-- Licensing information: https://bootstrapmade.com/license/ -->
                        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/herobiz-bootstrap-business-template/ -->
                    </div>
                </div>

                <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
                    <a href=""><i class="bi bi-twitter-x"></i></a>
                    <a href=""><i class="bi bi-facebook"></i></a>
                    <a href=""><i class="bi bi-instagram"></i></a>
                    <a href=""><i class="bi bi-linkedin"></i></a>
                </div>

            </div>
        </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src={{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}></script>
    <script src={{ asset('assets/vendor/php-email-form/validate.js') }}></script>
    <script src={{ asset('assets/vendor/aos/aos.js') }}></script>
    <script src={{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}></script>
    <script src={{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}></script>

    <!-- Main JS File -->
    <script src={{ asset('assets/js/main.js') }}></script>

</body>

</html>
