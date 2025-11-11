<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Shop Prototype</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="max-w-2xl mx-auto p-8 text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Laravel 12 Shop Prototype
            </h1>
            <p class="text-lg text-gray-600 mb-8">
                Modern e-commerce built with Laravel 12, Filament 4, Livewire 3, Alpine.js, and Tailwind CSS 4
            </p>

            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Admin Panel</h2>
                    <p class="text-gray-600 mb-4">Manage products and orders</p>
                    <a href="/admin" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-medium px-6 py-2 rounded-lg transition">
                        Go to Admin Panel
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Coming Soon</h2>
                    <p class="text-gray-600">
                        Product listing, cart, and checkout pages are being built...
                    </p>
                </div>
            </div>

            <div class="mt-8 text-sm text-gray-500">
                <p>Tech Stack: Laravel 12 • Filament 4 • Livewire 3 • Alpine.js • Tailwind CSS 4</p>
            </div>
        </div>
    </div>
</body>
</html>
