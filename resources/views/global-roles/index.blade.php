@extends('layouts.dashboard')

@section('content')
    <div class="clm-border gap-1">

            @include('widgets.list-title', [
                'title' => 'Роли',
                 'resource' => 'roles',
                 'endpoint' => route('roles.create'),
                 'ajax' => true,
            ])

            @include('widgets.index-table',
            [
                'cols' => $cols,
                'collection' => $globalRoles,
                'resource' => 'roles',
                'ajax' => true,
            ])

    </div>
@endsection
