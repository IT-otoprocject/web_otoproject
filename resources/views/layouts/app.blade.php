<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo/otoproject_icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/otoproject_icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome & icon  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/responsive.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    @if (!Auth::check())
        <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 dark:bg-gray-900">
            <div class="bg-white dark:bg-gray-800 p-8 rounded shadow text-center">
                <h2 class="text-2xl font-bold text-red-600 mb-4">Anda harus login</h2>
                <a href="{{ route('login') }}">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded text-lg">Login</button>
                </a>
            </div>
        </div>
    @else
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-[95%] mx-auto py-4 sm:py-5 lg:py-6 px-4 sm:px-6 lg:px-8 xl:max-w-[85%] 2xl:max-w-[1700px]">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Page Content -->
            <main class="py-4 sm:py-6 lg:py-8">
                <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 xl:max-w-[85%] 2xl:max-w-[1700px]">
                    {{ $slot }}
                </div>
            </main>
        </div>
    @endif
</body>

</html>