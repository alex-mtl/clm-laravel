<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.custom-head')
<body>
@yield('content')

@include('layouts.services')
</body>
</html>
