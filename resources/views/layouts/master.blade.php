<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ARG Portal">
    <meta name="author" content="Tagile Solutions">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <title>U.O.K ARG Portal</title>

    @include('partials.styles')
    @include('partials.scripts')
</head>

<body>
    @include('partials.toast')
    <div class="wrapper ">
        @include('partials.header')
        <div class="main-content">
            @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                @include('partials.sidebar')
                <div class="content-wrapper">
                    <div class="main bg-light">
                        <main class="content ">
                            @yield(section: 'content') </main>
                    </div>
                </div>
            @else
                <main class="content">
                    @yield('content')
                </main>
            @endif
        </div>
        @include('partials.footer')
    </div>


    <script>
        $(document).ready(function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const closeSidebar = document.getElementById('closeSidebar');
            const navbar = document.querySelector('.navbar');
            const overlay = document.getElementById('overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('sidebar-active');
                overlay.classList.toggle('overlay-active');
                if (window.innerWidth <= 767.98) {
                    navbar.classList.toggle('navbar-hidden');
                }
            }

            sidebarToggle.addEventListener('click', () => {
                toggleSidebar();
            });

            closeSidebar.addEventListener('click', () => {
                toggleSidebar();
            });

            overlay.addEventListener('click', () => {
                toggleSidebar();
            });
        });
    </script>
</body>

</html>
