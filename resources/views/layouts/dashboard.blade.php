<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')
<body>
@include('layouts.header')
@if($errors->any())
    <p>{{ $errors->first() }}</p>
@endif
<div class="content-layout">
    <div class="dashboard-view flex-start gap-2">
        <div class="sidebar clm-border w-20">
            @include('widgets.sidebar',
                [
                    'menu' => $sidebarMenu,
                ]
            )
        </div>

        @yield('content')

    </div>

</div>

@include('layouts.services')
</body>
</html>
