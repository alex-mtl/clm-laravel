@extends('layouts.app')

@section('content')
    <div class="content-main gap-1">
        @include('widgets.list-title', [
            'title' => 'Роли',
             'resource' => 'clubs.roles',
             'resourceItem' => $club,
        ])

        @include('widgets.index-table', [
            'cols' => $cols,
            'collection' => $roles,
            'resource' => 'clubs.roles',
            'parent' => $club
        ])

    </div>
@endsection
