<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Livewire styles wajib --}}
    @livewireStyles
</head>

<body class="bg-gray-100 font-sans antialiased pb-20">
    <div class="min-h-screen">
        {{-- Header hanya sekali di sini --}}
        @include('components.header')

        <main>
            {{-- Slot wajib untuk Livewire --}}
            {{ $slot }}
        </main>
        @include('components.bottom-bar')

    </div>

    @stack('scripts')
    @livewireScripts
</body>

</html>