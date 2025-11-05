
@extends('layouts.app')

@section('content')
    <div class="content-main">

    <p>Введите новый пароль:</p>
    <p>({{ $email }})</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">


        <x-synchronized-input
            name="password"
            type="password"
            label="Пароль"
            value="{{ old('password', '') }}"
            placeholder=""
            required
            :readonly="false"
        />

        <x-synchronized-input
            name="password_confirmation"
            type="password"
            label="Подтверждение пароля"
            value="{{ old('password_confirmation', '') }}"
            placeholder=""
            required
            :readonly="false"
        />



        <button class='btn'type="submit">Изменить</button>
    </form>
    </div>


@endsection
