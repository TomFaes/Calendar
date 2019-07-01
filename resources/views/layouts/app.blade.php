<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel 123') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <div class ="container-fluid">
            @include('layouts/navigationBar')
            <main class="py-4">
                <div class="row">
                    <div class="col-sm-0 col-md-0 col-lg-1"></div>
                    <div class="col-sm-12 col-md-12 col-lg-10">
                        @include('layouts/statusMessage')
                        @yield('content')
                    </div>
                    <div class="col-sm-0 col-md-0 col-lg-1"></div>
                </div>
            </main>
        </div>
       
    </div>
    
</body>
</html> 
<!-- Cookie warning(needs to be outside the body tag -->
<div class="row" style="position: fixed;bottom: 0px; background-color: #343a40; color: white; width: 105%">
    <div class="col-sm-0 col-md-0 col-lg-2"></div>
    <div class="col-sm-0 col-md-0 col-lg-8">@include('cookieConsent::index')</div>
    <div class="col-sm-0 col-md-0 col-lg-2"></div>
</div>