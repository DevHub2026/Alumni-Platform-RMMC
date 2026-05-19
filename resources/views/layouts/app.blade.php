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

                <!-- Center Nav + Search -->
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

                    <!-- Search button -->
                    <a href="{{ route('search.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition
                              text-gray-600 hover:bg-gray-100 hover:text-gray-900
                              {{ request()->routeIs('search.index') ? 'bg-blue-50 text-blue-700' : '' }}">
                        🔍
                    </a>
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

        <!-- Notification Bell -->
        <div x-data="notificationBell()" class="relative">
            <button @click="open = !open; markRead()"
                    class="relative w-9 h-9 flex items-center justify-center
                           rounded-lg hover:bg-gray-100 transition text-gray-600">
                🔔
                <span x-show="unread > 0"
                      class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500
                             text-white text-xs rounded-full flex items-center
                             justify-center font-bold"
                      x-text="unread > 9 ? '9+' : unread">
                </span>
            </button>

            <!-- Dropdown -->
            <div x-show="open"
                 @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-xl
                        border border-gray-100 z-50 overflow-hidden">

                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <p class="font-semibold text-gray-800 text-sm">Notifications</p>
                    <a href="{{ route('notifications.index') }}"
                       class="text-xs text-blue-600 hover:underline">View all</a>
                </div>

                <div class="max-h-80 overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-8 text-center text-gray-400 text-sm">
                            🔔 No notifications yet
                        </div>
                    </template>

                    <template x-for="n in notifications" :key="n.id">
                        <a :href="`/posts/${n.data.post_id}`"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50
                                  transition border-b border-gray-50 last:border-0 block"
                           :class="{ 'bg-blue-50': !n.read_at }">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center
                                        justify-center text-blue-600 text-sm flex-shrink-0">
                                💬
                            </div>
                            <div>
                                <p class="text-sm text-gray-700" x-text="n.data.message"></p>
                                <p class="text-xs text-gray-400 mt-0.5"
                                   x-text="n.created_at_human"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

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

    <!-- Toast Notification System -->
    @if(session('success') || session('error'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-24 right-6 z-50 max-w-sm">

        @if(session('success'))
        <div class="flex items-center gap-3 bg-white border border-green-200
                    shadow-lg rounded-xl px-5 py-4">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center
                        justify-center text-green-600 flex-shrink-0 text-lg">
                ✓
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-800">Success</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p>
            </div>
            <button @click="show = false"
                    class="text-gray-300 hover:text-gray-500 flex-shrink-0">✕</button>
        </div>
        @endif

        @if(session('error'))
        <div class="flex items-center gap-3 bg-white border border-red-200
                    shadow-lg rounded-xl px-5 py-4">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center
                        justify-center text-red-600 flex-shrink-0 text-lg">
                ✕
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-800">Error</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ session('error') }}</p>
            </div>
            <button @click="show = false"
                    class="text-gray-300 hover:text-gray-500 flex-shrink-0">✕</button>
        </div>
        @endif

    </div>
    @endif

    <!-- Chatbot -->
    @auth
        @include('components.chatbot')
    @endauth

    <!-- Notification Bell Script -->
    <script>
    function notificationBell() {
        return {
            open: false,
            unread: 0,
            notifications: [],

            init() {
                this.fetchNotifications();
                // Poll every 30 seconds for new notifications
                setInterval(() => this.fetchNotifications(), 30000);
            },

            async fetchNotifications() {
                try {
                    const res = await fetch('/notifications/unread');
                    const data = await res.json();
                    this.notifications = data.notifications;
                    this.unread = data.unread;
                } catch(e) {}
            },

            async markRead() {
                if (this.unread === 0) return;
                try {
                    await fetch('/notifications/mark-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    this.unread = 0;
                } catch(e) {}
            }
        }
    }
    </script>

</body>
</html>