{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="content-main">

    <p>{{ auth()->user()->name }} уверены что хотите выйти?</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class='btn'type="submit">Сбросить</button>
    </form>
    </div>


@endsection
