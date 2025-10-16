@extends('layouts.dashboard')

@section('content')
    <div class="clm-border gap-1">
        @include('widgets.list-title', [
            'title' => 'Города',
             'resource' => 'cities',
             'ajax' => true,
             'endpoint' => route('cities.create')
        ])

        @include('widgets.index-table', [
            'cols' => $cols,
            'collection' => $cities,
            'resource' => 'cities'
        ])

    </div>
@endsection
