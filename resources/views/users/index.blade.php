@extends('layouts.dashboard')

@section('content')
    <div class="content-main gap-1 w-50">
        @include('widgets.list-title', [
            'title' => 'Пользователи',
             'resource' => 'users'
        ])

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @include('widgets.index-table', [
            'cols' => $cols,
            'collection' => $users,
            'resource' => 'users',
            'ajax' => true,
        ])

    </div>
@endsection
