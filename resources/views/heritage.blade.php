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

    <style>
        .enlarged {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 30;
            background: #0000008e;
        }

        .enlarged img {
            width: auto;
            height: 100%;
            margin: 0 auto;
        }

        .spotlight {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle 200px at 50% 50%, transparent 10%, rgba(255, 255, 255, 0.45));
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <header class="w-full max-h-[70dvh] relative">
            <img class="w-full max-h-[70dvh] object-cover"
                src="{{ asset('storage/images/' . $data['frontMatter']['cover']) }}" alt="">
            <h1
                class="absolute p-5 bg-black top-[90%] left-[50%] -translate-x-[50%] text-white font-bold text-4xl/9 max-w-4xl z-10">
                {{ $data['frontMatter']['title'] }}</h1>

            <div class="spotlight"></div>
        </header>
        <main class="py-24 grid grid-cols-12 gap-4 max-w-[80dvw] mx-auto">
            <section class="col-span-8 bg-white rounded-lg shadow-lg p-6 py-8">

                <span
                    class="[&>p]:first-letter:text-7xl [&>p]:first-letter:font-bold [&>p]:first-letter:float-left [&>p]:first-letter:mr-2">{!! $data['first'] !!}</span>

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

    <script>
        document.querySelectorAll("p:has(> img)").forEach(img => {
            img.addEventListener("click", () => {
                img.classList.toggle("enlarged")
            });

            document.addEventListener("wheel", () => {
                if (img.classList.contains("enlarged")) {
                    img.classList.remove("enlarged");
                }
            })
        });

        const spotlight = document.querySelector(".spotlight");
        spotlight.addEventListener("mousemove", (e) => {
            const mouseX = e.clientX;
            const mouseY = e.clientY;

            const gradientCenterX = (mouseX / self.innerWidth) * 100;
            const gradientCenterY = (mouseY / self.innerHeight) * 100;

            const spotlight = document.querySelector(".spotlight");
            spotlight.style.background = `radial-gradient(circle 290px at ${gradientCenterX}% ${gradientCenterY}%, transparent 10%, rgba(255,255,255,0.45))`;
        })
    </script>
</body>

</html>
