<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In — Alumni Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex">

    <!-- Left Panel — Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-700
                via-blue-600 to-indigo-700 flex-col justify-between p-12">

        <!-- Logo -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl
                        flex items-center justify-center text-xl">
                🎓
            </div>
            <span class="text-white font-bold text-xl">Alumni Platform</span>
        </div>

        <!-- Center content -->
        <div>
            <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                Welcome back to your<br>
                <span class="text-blue-200">Alumni Community</span>
            </h1>
            <p class="text-blue-100 text-lg leading-relaxed mb-10">
                Stay connected with your batchmates, discover opportunities,
                and be part of the Ramon Magsaysay Memorial College alumni network.
            </p>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white bg-opacity-10 rounded-2xl p-4 text-center">
                    <p class="text-2xl font-bold text-white">
                        {{ App\Models\User::where('role','alumni')->count() }}+
                    </p>
                    <p class="text-blue-200 text-xs mt-1">Alumni</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-2xl p-4 text-center">
                    <p class="text-2xl font-bold text-white">
                        {{ App\Models\Event::where('is_published',true)->count() }}+
                    </p>
                    <p class="text-blue-200 text-xs mt-1">Events</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-2xl p-4 text-center">
                    <p class="text-2xl font-bold text-white">
                        {{ App\Models\Post::where('status','visible')->count() }}+
                    </p>
                    <p class="text-blue-200 text-xs mt-1">Posts</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-blue-300 text-sm">
            © {{ date('Y') }} Ramon Magsaysay Memorial College
        </p>
    </div>

    <!-- Right Panel — Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md">

            <!-- Mobile logo -->
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <span class="text-2xl">🎓</span>
                <span class="text-blue-700 font-bold text-lg">Alumni Platform</span>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Welcome back</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Log in to your alumni account to continue.
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200
                            text-green-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email"
                           class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email Address
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="you@example.com"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl
                                  text-sm bg-gray-50 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-transparent
                                  transition placeholder-gray-300
                                  @error('email') border-red-300 bg-red-50 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password"
                               class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs text-blue-600 hover:underline">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl
                                  text-sm bg-gray-50 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-transparent
                                  transition placeholder-gray-300
                                  @error('password') border-red-300 bg-red-50 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me -->
                <div class="flex items-center gap-2">
                    <input id="remember_me"
                           type="checkbox"
                           name="remember"
                           class="w-4 h-4 rounded border-gray-300
                                  text-blue-600 focus:ring-blue-500">
                    <label for="remember_me" class="text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white
                               font-semibold py-3 px-4 rounded-xl transition
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:ring-offset-2">
                    Log In
                </button>

            </form>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <p class="text-xs text-gray-400">Don't have an account?</p>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <!-- Register link -->
            <a href="{{ route('register') }}"
               class="block w-full text-center border-2 border-blue-700
                      text-blue-700 font-semibold py-3 px-4 rounded-xl
                      hover:bg-blue-50 transition text-sm">
                Create an Account
            </a>

        </div>
    </div>

</body>
</html>