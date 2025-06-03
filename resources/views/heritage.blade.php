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

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <header class="w-full max-h-[70dvh] relative">
            <img class="w-full max-h-[70dvh] object-cover" src="{{ asset('storage/images/lyon-1.jpg') }}"
                alt="">
            <h1
                class="absolute p-5 bg-black top-[90%] left-[50%] -translate-x-[50%] text-white font-bold text-4xl/9 max-w-4xl">
                Wander Through Time: Uncover Lyon's Secrets on Foot</h1>
        </header>
        <main class="py-24 grid grid-cols-12 gap-4 border max-w-[75dvw] mx-auto">
            <section class="col-span-8 bg-white rounded-lg shadow-lg p-6 py-8">
                <p>
                    <span class="float-left mr-2 text-7xl font-bold">H</span>ey movie buffs, art enthusiasts, and anyone
                    with a love for the miniature! Prepare to be amazed by the Musée Miniature et Cinéma, a unique
                    museum that blends art, history, and cinematic magic. This captivating venue showcases an incredible
                    collection of miniature models, painstakingly crafted replicas of famous buildings, iconic film
                    sets, and charming scenes from everyday life.
                </p>
            </section>
            <aside class="col-span-4 bg-white rounded-lg shadow-lg h-fit p-6">
                <ul>
                    <li>Date: 2026-01-10</li>
                    <li>tags: <a href="#">Lyon</a></li>
                    <li>Draft: True</li>
                </ul>
            </aside>
        </main>
    </div>
</body>

</html>
