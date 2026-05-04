<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ !empty($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- FONT --}}
    {{-- GOOGLE SANS CODE --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans+Code:ital,wght,MONO@0,300..800,1;1,300..800,1&display=swap"
        rel="stylesheet">

    {{-- favicon --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="/site.webmanifest">

    @livewireStyles
</head>

<body class="flex justify-center my-5">
    <div class="space-y-5 lg:w-2/5 w-[80%]">
        <div class="">
            {{ $slot ?? '' }}
            @yield('slot')
        </div>
    </div>

    {{-- session notif --}}
    @if (session('success'))
        <div id="sessionSuccess" class="fixed bottom-5 left-1/2 -translate-x-1/2 bg-highlighter p-3 rounded-2xl">
            {{ session('success') }}
        </div>
        {{-- hilangkan pesan sukses setelah 3 detik --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => document.getElementById('sessionSuccess').remove(), 3000);
            })
        </script>
    @endif

    @livewireScripts
</body>

</html>
