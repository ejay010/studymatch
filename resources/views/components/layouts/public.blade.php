<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'StudyMatch - Connect with Verified Educators' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @livewireStyles
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <!-- Logo Icon (SVG) -->
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9L5 11.18V17.18L12 21L19 17.18V11.18L21 10.09V17H23V9L12 3ZM18.82 9L12 12.72L5.18 9L12 5.28L18.82 9ZM17 15.99L12 18.72L7 15.99V12.27L12 15L17 12.27V15.99Z"/></svg>
                        <span class="font-extrabold text-2xl tracking-tight text-gray-900">Study<span class="text-blue-600">Match</span></span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8 font-medium text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition {{ request()->routeIs('home') ? 'text-blue-600' : '' }}">Find a Tutor</a>
                    <a href="{{ route('classes.index') }}" class="hover:text-blue-600 transition {{ request()->routeIs('classes.index') ? 'text-blue-600' : '' }}">Small Group Classes</a>
                    <a href="{{ route('resources.index') }}" class="hover:text-blue-600 transition {{ request()->routeIs('resources.index') ? 'text-blue-600' : '' }}">Resource Store</a>
                    <a href="{{ route('apply.index') }}" class="text-blue-600 hover:text-blue-800 transition font-semibold">Apply to Teach</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Log in</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition shadow-sm">Sign up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} StudyMatch Inc. All rights reserved. Built with Laravel.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
