<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CLM')</title>
{{--    <link rel="stylesheet" href="/css/app.css"> --}}{{-- Optional --}}
{{--    @vite(['resources/js/app.js', 'resources/css/app.css'])--}}

    <link rel="stylesheet" href="/css/clm.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"/>
</head>
<body>
@include('layouts.header')
@if($errors->any())
    <p>{{ $errors->first() }}</p>
@endif
<div class="content-layout">
    @yield('content')
</div>

@include('layouts.footer')
</body>
</html>
