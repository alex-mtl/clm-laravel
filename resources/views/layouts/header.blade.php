<header>
    <span class="home-logo">
        <a href="/" >
            <img src="/img/clm-logo.svg" alt="CLM">
        </a>
    </span>

    <nav class="main-nav">
        <span class="menu-item {{ request()->is('clubs*') ? 'active' : '' }}">
        <a href="/clubs">{{__('clm.clubs')}}</a>
        </span>
        <span class="menu-item {{ request()->is('players*') ? 'active' : '' }}">
        <a href="/players ">{{__('clm.users')}}</a>
        </span>
        <span class="menu-item {{ request()->is('tournaments*') ? 'active' : '' }}">
            <a href="/tournaments">Турниры </a>
        </span>
    </nav>

    @if (Route::has('login'))
        <nav class="action-buttons">
            @auth

                <a
{{--                        href="{{ url('/dashboard') }}"--}}
                        class=""
                        id="user-menu"
                >
                    Dashboard
                </a>

                <x-dropdown-menu
                    name="user-menu"
                    :menu-owner-id="'user-menu'"
                    :menu-items="$menuItems"
                    selected="self"

                />
            @else
                <span class="login">
                <a href="{{ route('login') }}" class="">
{{--                    {{ __('clm.loginBtn') }}--}}
                    <img src="/img/login.svg" alt="Login">
                </a>
                </span>
{{--                @if (!request()->is('login'))--}}
{{--                    <a href="{{ route('login') }}" class="btn btn-login">--}}
{{--                        {{ __('clm.loginBtn') }}--}}
{{--                    </a>--}}
{{--                @endif--}}

{{--                @if (Route::has('register'))--}}
{{--                    <a--}}
{{--                            href="{{ route('register') }}"--}}
{{--                            class="btn btn-register">--}}
{{--                        {{ __('clm.registerBtn') }}--}}
{{--                    </a>--}}
{{--                @endif--}}
            @endauth

            @include('layouts.langmenu')

        </nav>
    @endif
</header>
