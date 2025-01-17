<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('img/logo2.jpg') }}" type="image/x-icon">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        {{-- Alpine.js untuk notifikasi --}}
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div 
            x-data="notificationManager()" 
            class="min-h-screen bg-gray-100"
        >
            {{-- Notification Container --}}
            <div class="fixed top-4 right-4 z-50 space-y-2">
                {{-- Flash Messages dari Server --}}
                @if(session('success'))
                    <div 
                        x-init="add('{{ session('success') }}', 'success')"
                        class="hidden"
                    ></div>
                @endif

                @if(session('error'))
                    <div 
                        x-init="add('{{ session('error') }}', 'error')"
                        class="hidden"
                    ></div>
                @endif

                {{-- Notification Template --}}
                <template x-for="notification in notifications" :key="notification.id">
                    <div 
                        x-show="true"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-full"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-full"
                        :class="{
                            'bg-green-500': notification.type === 'success',
                            'bg-red-500': notification.type === 'error',
                            'bg-blue-500': notification.type === 'info'
                        }"
                        class="text-white px-4 py-2 rounded-lg shadow-lg cursor-pointer"
                        x-text="notification.message"
                        @click="remove(notification.id)"
                    ></div>
                </template>
            </div>

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Notification Manager Script --}}
        <script>
            function notificationManager() {
                return {
                    notifications: [],
                    add(message, type = 'success') {
                        const id = Date.now();
                        this.notifications.push({ id, message, type });
                        
                        setTimeout(() => {
                            this.remove(id);
                        }, 5000);
                    },
                    remove(id) {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    }
                };
            }

            // Fungsi global untuk menambah notifikasi dari JavaScript
            function addNotification(message, type = 'success') {
                const notificationManager = document.querySelector('[x-data]')?.__x.$data;
                if (notificationManager) {
                    notificationManager.add(message, type);
                }
            }
        </script>
    </body>
</html>