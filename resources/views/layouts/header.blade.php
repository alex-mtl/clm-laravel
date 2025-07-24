<header>
    <span class="home-logo">
        <a href="/" >
            <img src="/img/clm-logo.svg" alt="CLM">
        </a>
    </span>

    <nav class="main-nav">
        <a href="/clubs">{{__('clm.clubs')}}</a>
        <a href="#">Item 2</a>
        <a href="#">Item 3</a>
    </nav>

    @if (Route::has('login'))
        <nav class="action-buttons">
            @auth
                <a
                        href="{{ url('/dashboard') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                >
                    Dashboard
                </a>
            @else
                @if (!request()->is('login'))
                    <a href="{{ route('login') }}" class="btn btn-login">
                        {{ __('clm.loginBtn') }}
                    </a>
                @endif

                @if (Route::has('register'))
                    <a
                            href="{{ route('register') }}"
                            class="btn btn-register">
                        {{ __('clm.registerBtn') }}
                    </a>
                @endif
            @endauth

            @include('layouts.langmenu')

        </nav>
    @endif
</header>