<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Alumni Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-gray-50">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="{{ url('/') }}"
                   class="flex items-center gap-2 text-blue-700 font-bold text-lg">
                    🎓 <span>Alumni Platform</span>
                </a>

                <!-- Center Nav -->
                <div class="hidden md:flex items-center gap-1">
                    @foreach([
                        ['route' => 'home', 'label' => 'Home'],
                        ['route' => 'announcements.index', 'label' => 'Announcements'],
                        ['route' => 'events.index', 'label' => 'Events'],
                        ['route' => 'alumni.index', 'label' => 'Directory'],
                        ['route' => 'gallery.index', 'label' => 'Gallery'],
                        ['route' => 'posts.index', 'label' => 'Posts'],
                    ] as $link)
                        <a href="{{ route($link['route']) }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs($link['route'])
                                     ? 'bg-blue-50 text-blue-700'
                                     : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                <!-- Right Side -->
<div class="flex items-center gap-2">
    @auth
        @if(Auth::user()->role === 'admin')
            <a href="{{ url('/admin') }}"
               class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg
                      hover:bg-indigo-700 transition font-medium">
                ⚙️ Admin Panel
            </a>
        @endif

        <a href="{{ route('profile.show') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                  font-medium text-gray-700 hover:bg-gray-100 transition">
            <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-full
                         flex items-center justify-center text-xs font-bold">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
            <span class="hidden md:block">{{ Auth::user()->name }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="text-sm bg-gray-100 hover:bg-red-50
                           hover:text-red-600 text-gray-600 px-4 py-2
                           rounded-lg transition font-medium">
                Logout
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
           class="text-sm text-gray-600 hover:text-blue-700
                  font-medium px-3 py-2 rounded-lg hover:bg-gray-100">
            Log in
        </a>
        <a href="{{ route('register') }}"
           class="text-sm bg-blue-700 text-white px-4 py-2 rounded-lg
                  hover:bg-blue-800 font-medium transition">
            Register
        </a>
    @endauth
</div>

            </div>
        </div>
    </nav>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-700
                        px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-600
                        px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                ❌ {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- MAIN CONTENT -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
    </main>

    <!-- FOOTER -->
<footer class="bg-white border-t border-gray-100 mt-16 w-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">

            <!-- Logo -->
            <div class="flex items-center gap-2 text-blue-700 font-bold text-lg">
                🎓 Alumni Platform
                <span class="text-xs font-normal text-gray-400 ml-1">
                    Ramon Magsaysay Memorial College
                </span>
            </div>

            <!-- Nav Links -->
            <div class="flex flex-wrap justify-center gap-5 text-sm text-gray-400">
                <a href="{{ route('home') }}"
                   class="hover:text-blue-600 transition">Home</a>
                <a href="{{ route('announcements.index') }}"
                   class="hover:text-blue-600 transition">Announcements</a>
                <a href="{{ route('events.index') }}"
                   class="hover:text-blue-600 transition">Events</a>
                <a href="{{ route('alumni.index') }}"
                   class="hover:text-blue-600 transition">Directory</a>
                <a href="{{ route('gallery.index') }}"
                   class="hover:text-blue-600 transition">Gallery</a>
                <a href="{{ route('posts.index') }}"
                   class="hover:text-blue-600 transition">Posts</a>
            </div>

            <!-- Copyright -->
            <p class="text-xs text-gray-400">
                © {{ date('Y') }} Alumni Platform. All rights reserved.
            </p>

        </div>
    </div>
</footer>

    <!-- Chatbot -->
    @auth
        @include('components.chatbot')
    @endauth

</body>
</html>