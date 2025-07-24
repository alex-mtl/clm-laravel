{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <div id="login-form" class="frame-parent">
        <div class="frame-group">
            <form method="post" action="/login">
                @csrf
                <div class="instance-parent">

                    <div class="vector-parent">
                        <img class="vector-icon" alt="" src="img/clm-logo.svg">
                    </div>

                    <div class="auto-layout-horizontal">
                        <div class="auto-layout-horizontal1">
                            <div class="div"> {{  __('clm.loginBtn') }}</div>
                        </div>
                        <div class="div1">{{  __('clm.registerBtn') }} </div>
                    </div>

                    <div class="auto-layout-vertical">
                        <div class="div2">{{ __('Username') }} </div>
                        <input name="email" type="email" class="auto-layout-horizontal2">
                    </div>
                    <div class="auto-layout-vertical">
                        <div class="div2"> {{__('Password')}}</div>
                        <input name="password" type="password" class="auto-layout-horizontal2">
                    </div>
                    <div class="auto-layout-horizontal6">
{{--                        <div class="auto-layout-horizontal7">--}}
{{--                            <input type="checkbox" class="rectangle">--}}
{{--                            <div class="div3">{{__('RememberMe')}}</div>--}}
{{--                        </div>--}}
                        <div class="div7">Забыли пароль?</div>
                    </div>
                </div>
                <div class="auto-layout-horizontal-parent">
                    <button class="login-submit-button">
                        <div class="div">{{  __('clm.loginBtn') }}</div>
                    </button>
                    <div class="auto-layout-horizontal9">
                        <div class="rectangle1">
                        </div>
                        <div class="div3">или</div>
                        <div class="rectangle1">
                        </div>
                    </div>
                    <div class="auto-layout-horizontal10">
                        <div class="auto-layout-horizontal11">
                            <img class="frame-icon" alt="" src="google.svg">

                            <div class="div">Google</div>
                        </div>
                    </div>
                    <div class="auto-layout-horizontal12">
                        <div class="auto-layout-horizontal13">
                            <img class="frame-icon" alt="" src="telegram.svg">

                            <div class="div">Telegram</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

{{--<div>--}}
{{--    <form method="POST" action="/login">--}}
{{--        @csrf--}}
{{--        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>--}}
{{--        <input type="password" name="password" placeholder="Password" required>--}}
{{--        <button type="submit">Login</button>--}}
{{--    </form>--}}

{{--    <a href="/register">Register</a>--}}
{{--</div>--}}
@endsection
