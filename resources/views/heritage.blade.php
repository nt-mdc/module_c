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
            <img class="w-full max-h-[70dvh] object-cover"
                src="{{ asset('storage/images/' . $data['frontMatter']['cover']) }}" alt="">
            <h1
                class="absolute p-5 bg-black top-[90%] left-[50%] -translate-x-[50%] text-white font-bold text-4xl/9 max-w-4xl">
                {{ $data['frontMatter']['title'] }}</h1>
        </header>
        <main class="py-24 grid grid-cols-12 gap-4 max-w-[80dvw] mx-auto">
            <section class="col-span-8 bg-white rounded-lg shadow-lg p-6 py-8">

                <span class="[&>p]:first-letter:text-7xl [&>p]:first-letter:font-bold [&>p]:first-letter:float-left [&>p]:first-letter:mr-2">{!! $data['first'] !!}</span>

                @foreach ($data['content'] as $item)
                    {!! $item !!}
                @endforeach

                {{-- {!!$data['content']!!} --}}
            </section>
            <aside class="col-span-4 bg-white rounded-lg shadow-lg h-fit p-6 sticky top-2">
                <ul class="space-y-1">
                    @foreach ($data['frontMatter'] as $key => $item)
                        @if ($key === 'title' || $key === 'cover')
                            @continue
                        @endif
                        <li class="capitalize">{{ $key }}: <span>{{ $item }}</span></li>
                    @endforeach
                </ul>
            </aside>
        </main>
    </div>
</body>

</html>
