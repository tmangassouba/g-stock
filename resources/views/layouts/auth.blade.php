<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: #f8f8f8;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css"> --}}
        <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.3.45/css/materialdesignicons.min.css">

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.0/dist/alpine.js" defer></script>
    </head>
    <body>
        <section class="section hero is-fullheight is-error-section">
            <div class="hero-body">
                <div class="container">
                    <div class="columns is-centered">
                        <div class="column is-two-fifths">
                            <div class="card has-card-header-background">
                                <header class="card-header" style="background-color: #f5f5f5;">
                                    <p class="card-header-title">
                                        <span class="icon"><i class="mdi mdi-lock default"></i></span>
                                        <span>@yield('card-header-title')</span>
                                    </p>
                                </header>
                                <div class="card-content">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-foot has-text-centered">
                <div class="logo"></div>
            </div>
        </section>
    </body>
</html>
