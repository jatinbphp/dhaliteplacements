<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('adminlte/img/businessman.png') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/bootstrap-switch-button.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/select2.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/daterangepicker.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="{{ asset('assets/dist/css/custom.css') }}">
        @livewireStyles
        @yield('css')
    </head>

    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed sidebar-collapse">
        <div class="wrapper">
            @include('layouts.partials.navbar')
            @include('layouts.partials.sidebar')
            @yield('content')
            @include('layouts.partials.footer')
        </div>

        <!-- Scripts -->
        <script src="{{ asset('adminlte/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/bootstrap-switch-button.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/moment.min.js') }}"></script>
        <script src="{{ asset('adminlte/js/daterangepicker.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="{{ asset('assets/dist/js/table-actions.js')}}"></script>

        <script>
            function applyDarkMode() {
                const currentTheme = localStorage.getItem('theme') || 'light'; // Default to 'light'
                const themeIcon = document.getElementById('themeIcon');
                const mainHeader = document.querySelector('.main-header');

                if (currentTheme === 'dark') {
                    document.body.classList.add("dark-mode");
                    mainHeader?.classList.replace('navbar-light', 'navbar-dark');
                    themeIcon?.classList.replace('fa-moon', 'fa-sun');
                } else {
                    document.body.classList.remove("dark-mode");
                    mainHeader?.classList.replace('navbar-dark', 'navbar-light');
                    themeIcon?.classList.replace('fa-sun', 'fa-moon');
                }
                let isDarkMode = localStorage.getItem("theme") === "dark";
                let themeLinkId = "flatpickr-dark-theme";
                if (isDarkMode) {
                    let link = document.createElement("link");
                    link.id = themeLinkId;
                    link.rel = "stylesheet";
                    link.href = "https://npmcdn.com/flatpickr/dist/themes/dark.css";
                    document.head.appendChild(link);
                } else {
                    let existingLink = document.getElementById(themeLinkId);
                    if (existingLink) {
                        existingLink.remove();
                    }
                }
            }

            document.addEventListener("DOMContentLoaded", function () {
                if (window.themeScriptLoaded) return; // Prevents duplicate execution
                window.themeScriptLoaded = true;

                applyDarkMode(); // Apply theme when page loads

                // Use event delegation to handle clicks even after Livewire updates the DOM
                document.addEventListener('click', function (event) {
                    if (event.target.id === 'themeIcon') {
                        const isDarkMode = document.body.classList.contains('dark-mode');
                        localStorage.setItem('theme', isDarkMode ? 'light' : 'dark');
                        applyDarkMode(); // Reapply theme after change
                    }
                });

                // Reapply theme after Livewire updates or navigation
                document.addEventListener("livewire:updated", applyDarkMode);
                document.addEventListener("livewire:navigated", applyDarkMode);
            });
        </script>
        @include('common.messages')
        @livewireScripts
        @yield('js')
    </body>
</html>
