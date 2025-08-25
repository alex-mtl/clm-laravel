<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')
<body>
@include('layouts.header')
@if($errors->any())
    <p>{{ $errors->first() }}</p>
@endif
<div class="content-layout">
    @yield('content')
</div>

@unless(isset($noFooter) && $noFooter)
    @include('layouts.footer')
@endunless

@include('layouts.services')
</body>
</html>
