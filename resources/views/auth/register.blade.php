@extends('layouts.app')

@section('content')
        <div class="auth-container">
            <div class="logo">
                <img class="vector-icon" alt="" src="img/clm-logo.png">
            </div>

            <div class="auth-tabs">
                <button class="tab {{ request()->is('login') ? 'active' : '' }}" data-tab="login"><a href="{{ request()->is('login') ? '#' : '/login' }}">Вход </a></button>
                <button class="tab {{ request()->is('register') ? 'active' : '' }}" data-tab="register"><a href="{{ request()->is('register') ? '#' : '/register' }}">Регистрация</a></button>
            </div>

            @if ( request()->is('login') )
            <!-- Login form -->
            <form action="/login" method="post" id="login-form" class="auth-form">
                @csrf
                <label for="nickname">Никнейм</label>
                <input type="email" name="email" id="nickname" placeholder="Введите email">

                <label for="password">Пароль</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Введите ваш пароль">
                    <img src="../../img/eye.png" alt="Показать пароль" class="toggle-password" data-target="password">
                </div>

                <button type="submit" class="submit-btn">Войти</button>


            </form>
            @else

            <!--Registration form -->
            <form id="register-form" class="auth-form" action="/register" method="post">
                @csrf
                <label for="new-nickname">Никнейм</label>
                <input type="text" name="name"id="new-nickname" autocomplete="off" placeholder="Введите никнейм">

                <label for="email">Email</label>
                <input type="email" name="email" id="email" autocomplete="off" placeholder="Введите ваш email">

                <label for="new-password">Пароль</label>
                <div class="password-wrapper">
                    <input type="password" name="password" autocomplete="off" id="new-password" placeholder="Введите ваш пароль">
                    <img src="/img/eye.png" alt="Показать пароль" class="toggle-password" data-target="new-password">
                </div>

                <label for="confirm-password">Подтвердите пароль</label>
                <div class="password-wrapper">
                    <input type="password" autocomplete="off" name="password_confirmation" id="confirm-password" placeholder="Подтвердите пароль">
                    <img src="/img//eye.png" alt="Показать пароль" class="toggle-password" data-target="confirm-password">
                </div>

                <button type="submit" class="submit-btn">Регистрация</button>

            </form>
            @endif
            <div>
                <div class="form-footer ml-auto">
{{--                    <label><input type="checkbox"> Запомнить меня</label>--}}
                    <a href="/forgot-password" class="forgot-link ml-auto">Забыли пароль?</a>
                </div>
                <div class="divider"><span>или</span></div>

                <button type="button" class="social-btn google" onclick="window.location.href = '{{ route('auth.google') }}';">
                    <img src="/img/google-icon.png" alt="Google icon" class="social-icon">
                    <span>Войти через Google</span>
                </button>
                <button type="button" class="social-btn facebook" onclick="window.location.href = '{{ route('auth.facebook') }}';">
                    <img src="/img/facebook-icon.png" alt="Facebook icon" class="social-icon">
                    <span>Войти через Facebook</span>
                </button>
            </div>
        </div>

@endsection
