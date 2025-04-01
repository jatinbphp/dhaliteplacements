<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name') }}</title>
        <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/css/all.min.css') }}">
        @livewireStyles
        @yield('css')
    </head>
    <body class="hold-transition login-page" data-new-gr-c-s-check-loaded="14.1229.0" data-gr-ext-installed="" cz-shortcut-listen="true">
            @yield('content')
        @livewireScripts
        @yield('js')
    </body>
</html>
