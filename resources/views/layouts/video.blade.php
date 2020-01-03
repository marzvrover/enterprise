<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $title }} | Midwest Institute for Sexuality and Gender Diversity</title>
    <meta name="description" content="event.discription">
    <meta name="title" content="event.name">
    <meta name="author" content="MBLGTACC, Midwest Institute for Sexuality and Gender Diversity">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicons/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('favicons/favicon-16x16.png') }}" sizes="16x16">
    <link rel="manifest" href="{{ asset('favicons/manifest.json') }}">
    <link rel="mask-icon" href="{{ asset('favicons/manifest.json') }}" color="#38afad">
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}">
    <meta name="msapplication-config" content="{{ asset('favicons/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @yield('styles')

    <script defer src="{{ asset('js/fontawesome.min.js') }}"></script>

    <script>
        window.Spark = {};

        window.SGDInstitute = @json([
            'mblgtaccStripe' => getStripeKey('mblgtacc'),
            'instituteStripe' => getStripeKey('institute'),
            'user' => Auth::user()
        ]);
    </script>
</head>

<body class="bg-mint-800">
    <div id="app">
        @include('layouts.partials.nav', ['light' => true])

        <div class="fullscreen-video">
            <div class="overlay"></div>
            <video autoplay loop muted id="backgroundVideo">
                <source src="{{ asset('video/background.mp4') }}" type="video/mp4">
            </video>
        </div>

        @yield('content')

    </div>

    <script src="{{ mix('js/app.js') }}"></script>

</body>

</html>