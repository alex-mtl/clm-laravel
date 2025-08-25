<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CLM')</title>

    <link rel="stylesheet" href="/css/clm.css?v={{ filemtime(public_path('css/clm.css')) }}">
    <link rel="stylesheet" href="/css/lang-menu.css?v={{ filemtime(public_path('css/lang-menu.css')) }}">
    @isset($styles)
        @foreach($styles as $style)
            <link rel="stylesheet" href="{{ asset("css/{$style}") }}?v={{ filemtime(public_path('css/'.$style)) }}">
        @endforeach
    @endisset

    @stack('styles')

    @isset($scripts)
        @foreach($scripts as $script)
            <script src="{{ asset("js/{$script}") }}?v={{ filemtime(public_path('js/'.$script)) }}" defer></script>
        @endforeach
    @endisset

    @stack('scripts')

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
