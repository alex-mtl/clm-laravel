
@extends('layouts.app')



@section('content')
    <div class="content-main">
        <script>
            @php
                $toasts = session('toasts') ?? [];
            @endphp

            let toasts = @json($toasts);

        </script>

    <p>Забыли пароль?</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-synchronized-input
            name="email"
            type="email"
            label="Email"
            value="{{ old('email', '') }}"
            placeholder="john.doe@example.com"
            required
            :readonly="false"
        />
        <button class='btn'type="submit">Сбросить</button>
    </form>
    </div>


@endsection
