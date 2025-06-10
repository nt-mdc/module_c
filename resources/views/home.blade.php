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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <header class="w-full min-h-40 relative bg-gray-100">
            <h1
                class="absolute p-5 bg-black top-[80%] left-[50%] -translate-x-[50%] text-white font-bold text-4xl/9 max-w-4xl">
                Listing Page</h1>
        </header>
        <main class="py-24 grid grid-cols-12 gap-4 max-w-[75dvw] mx-auto ">
            <section class="col-span-8 bg-white rounded-lg shadow-lg p-6 py-8">
                <ul class="space-y-3 list-disc px-6">
                    @foreach ($data as $item)
                        <li>
                            <a href="{{ $item['link'] }}">
                                <span class="text-blue-600 font-semibold">{{ $item['title'] }}</span>
                                <br>
                                <span>{{ isset($item['summary']) ? $item['summary'] : '' }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
            <aside class="col-span-4 bg-white rounded-lg shadow-lg h-fit p-6">
                <x-input-label for="search" :value="__('Search')" />
                <form action="{{route("keyword.pages")}}" method="post" class="flex justify-center items-center gap-3 w-full">
                    @csrf
                    <x-text-input id="search" class="block mt-1 w-full" type="text" name="search"
                        placeholder="KEYWORD" :value="old('search')" required autofocus />
                    <x-primary-button>
                        {{ __('Search') }}
                    </x-primary-button>
                </form>
            </aside>
        </main>
    </div>
</body>

</html>
