@extends('layouts.dashboard')

@section('content')
    <div class="clm-border gap-1">
        @include('widgets.list-title', [
            'title' => 'Города',
             'resource' => 'cities'
        ])

        @include('widgets.index-table', [
            'cols' => $cols,
            'collection' => $cities,
            'resource' => 'cities'
        ])

    </div>
@endsection
