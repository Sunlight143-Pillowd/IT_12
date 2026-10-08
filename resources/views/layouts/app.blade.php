<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

<<<<<<< HEAD
        <style>
            @media (min-width: 1024px) {
                .app-desktop-navigation,
                .app-settings-navigation {
                    display: flex !important;
                }

                .app-mobile-navigation-toggle,
                .app-responsive-navigation {
                    display: none !important;
                }
            }

            @media (max-width: 1023px) {
                .app-desktop-navigation,
                .app-settings-navigation {
                    display: none !important;
                }
            }
        </style>

=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
<<<<<<< HEAD
        <div class="flex min-h-screen flex-col bg-gray-100">
=======
        <div class="min-h-screen bg-gray-100">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
<<<<<<< HEAD
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
=======
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
<<<<<<< HEAD
            <main class="flex-1">
                {{ $slot }}
            </main>
            @include('layouts.footer')
=======
            <main>
                {{ $slot }}
            </main>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        </div>
    </body>
</html>
