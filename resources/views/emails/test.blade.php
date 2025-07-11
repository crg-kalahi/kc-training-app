<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">

<div class="max-w-4xl mx-auto p-8 border border-gray-300 shadow-lg bg-white relative font-serif">
    <!-- Side ribbon -->
    <div class="absolute left-0 top-0 h-full w-36">
        <img src="{{ asset('storage/images/new/cert-design.png') }}" alt="DSWD Logo" class="h-full" />
    </div>

    <!-- Header with inline logo -->
    <div class="flex justify-between items-center">
        <div class="text-left space-y-1 ml-40 font-[Arial]">
            <p class="text-sm font-light">Republic of the Philippines</p>
            <p class="text-sm font-light">Department of Social Welfare and Development</p>
            <p class="text-sm font-light">Field Office Caraga, Capitol Site, Butuan City</p>
        </div>
        <div class="flex space-x-4">
            <img src="{{ asset('images/dswd.jpg') }}" alt="DSWD Logo" class="h-10" />
            <img src="{{ asset('images/bp.png') }}" alt="Bagong Pilipinas Logo" class="h-10" />
        </div>
    </div>

    <!-- Certificate Content -->
    <div class="text-left mt-4 space-y-2 ml-40 mb-5">
        <h1 class="text-4xl font-bold font-montserrat">Certificate of Participation</h1>
        <h2 class="text-5xl text-red-600 font-bold font-monotype">{{ $name }}</h2>
        <p class="text-sm max-w-2xl mx-auto">
            for having successfully participated during the <b>{{ $training }}</b>.<br>
            held on {{ $date }} at <br> {{ $venue }}. <br><br>
            Given this <b>{{ $givenDate }}</b>.
        </p>
    </div>

    <!-- Signature -->
    <div class="mt-16 text-center leading-tight">
        <div class="relative">
            <img src="{{ asset('storage/images/e-sig.png') }}" alt="DSWD Signature" class="h-16 mx-auto mt-2 absolute bottom-4 left-1/2 transform -translate-x-1/2" />
            <p class="font-bold m-0 leading-tight">MARI-FLOR A. DOLLAGA-LIBANG</p>
            <p class="text-sm m-0 leading-tight">Regional Director</p>
        </div>
    </div>

    <!-- Footer logos -->
    <div class="absolute bottom-4 right-4 flex space-x-2">
        <img src="{{ asset('storage/images/insignia.jpg') }}" alt="ISO Logo" class="h-10" />
    </div>
</div>

</body>
</html>