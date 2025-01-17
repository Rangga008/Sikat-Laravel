<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body 
    x-data="{ 
        animate: false,
        init() { 
            setTimeout(() => { this.animate = true }, 100) 
        }
    }" 
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 font-sans"
>
    <div 
        x-transition:enter="transition duration-500 ease-out"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        class="w-full max-w-md text-center p-6 bg-white rounded-2xl shadow-2xl"
    >
        {{-- Logo --}}
        <div 
            class="mb-12 flex justify-center transform transition-all duration-700"
            :class="animate ? 'scale-100 rotate-0' : 'scale-0 rotate-180'"
        >
            <svg 
                class="h-20 w-20 text-blue-600" 
                fill="currentColor" 
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path 
                    fill-rule="evenodd" 
                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 005 18h10a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" 
                    clip-rule="evenodd" 
                />
            </svg>
        </div>

        {{-- Judul --}}
        <h1 
            class="text-3xl font-bold text-blue-900 mb-8 transform transition-all duration-700"
            :class="animate ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
        >
            {{ config('app.name') }}
        </h1>

        {{-- Navigasi --}}
        <div 
            class="space-y-4 transform transition-all duration-700 delay-300"
            :class="animate ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
        >
            @if (Route::has('login'))
                @auth
                    <a 
                        href="{{ url('/dashboard') }}" 
                        class="w-full block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition transform hover:scale-105"
                    >
                        Dashboard
                    </a>
                @else
                    <a 
                        href="{{ route('login') }}" 
                        class="w-full block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition transform hover:scale-105"
                    >
                        Login
                    </a>

                    @if (Route::has('register'))
                        <a 
                            href="{{ route('register') }}" 
                            class="w-full block px-6 py-3 bg-white text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition transform hover:scale-105"
                        >
                 Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>

        {{-- Footer --}}
        <footer class="mt-12 text-sm text-blue-700">
            <p>
                &copy; {{ date('Y') }} {{ config('app.name') }}. 
                All rights reserved.
            </p>
        </footer>
    </div>
</body>
</html>