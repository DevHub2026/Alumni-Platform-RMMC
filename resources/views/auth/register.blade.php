<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Alumni Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex">

    <!-- Left Panel — Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-700
                via-blue-600 to-blue-700 flex-col justify-between p-12">

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl
                        flex items-center justify-center text-xl">
                🎓
            </div>
            <span class="text-white font-bold text-xl">Alumni Platform</span>
        </div>

        <div>
            <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                Join the RMMC<br>
                <span class="text-blue-200">Alumni Network</span>
            </h1>
            <p class="text-blue-100 text-lg leading-relaxed mb-10">
                Create your account and connect with thousands of Ramon Magsaysay
                Memorial College graduates from all over the Philippines.
            </p>

            <!-- Benefits list -->
            <div class="space-y-3">
                @foreach([
                    ['🔍', 'Search and connect with fellow alumni'],
                    ['📢', 'Get the latest school announcements'],
                    ['🎉', 'Join exclusive alumni events'],
                    ['💼', 'Share career opportunities'],
                    ['🤖', 'AI-powered alumni assistant'],
                ] as [$icon, $text])
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg
                                flex items-center justify-center text-sm flex-shrink-0">
                        {{ $icon }}
                    </div>
                    <p class="text-blue-100 text-sm">{{ $text }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-blue-300 text-sm">
            © {{ date('Y') }} Ramon Magsaysay Memorial College
        </p>
    </div>

    <!-- Right Panel — Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12
                overflow-y-auto">
        <div class="w-full max-w-md py-8">

            <!-- Mobile logo -->
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <span class="text-2xl">🎓</span>
                <span class="text-blue-700 font-bold text-lg">Alumni Platform</span>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Create your account</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Join the alumni community today. It's free.
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name"
                           class="block text-sm font-medium text-gray-700 mb-1.5">
                        Full Name
                    </label>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           autocomplete="name"
                           placeholder="Juan dela Cruz"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl
                                  text-sm bg-gray-50 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-transparent
                                  transition placeholder-gray-300
                                  @error('name') border-red-300 bg-red-50 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

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
                    <label for="password"
                           class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                    </label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           placeholder="Min. 8 characters"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl
                                  text-sm bg-gray-50 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-transparent
                                  transition placeholder-gray-300
                                  @error('password') border-red-300 bg-red-50 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation"
                           class="block text-sm font-medium text-gray-700 mb-1.5">
                        Confirm Password
                    </label>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           placeholder="Repeat your password"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl
                                  text-sm bg-gray-50 focus:outline-none focus:ring-2
                                  focus:ring-blue-500 focus:border-transparent
                                  transition placeholder-gray-300">
                </div>

                <!-- Terms notice -->
                <p class="text-xs text-gray-400 leading-relaxed">
                    By creating an account you agree to our platform's community
                    guidelines. Complete your profile after registering to become
                    a <span class="text-blue-600 font-medium">Verified Alumni</span>
                    and unlock all features.
                </p>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white
                               font-semibold py-3 px-4 rounded-xl transition
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:ring-offset-2">
                    Create Account
                </button>

            </form>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <p class="text-xs text-gray-400">Already have an account?</p>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <!-- Login link -->
            <a href="{{ route('login') }}"
               class="block w-full text-center border-2 border-blue-700
                      text-blue-700 font-semibold py-3 px-4 rounded-xl
                      hover:bg-blue-50 transition text-sm">
                Log In Instead
            </a>

        </div>
    </div>

</body>
</html>