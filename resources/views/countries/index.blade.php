@extends('layouts.dashboard')

@section('content')
    <div class="clm-border gap-1">
        @include('widgets.list-title', [
            'title' => 'Страны',
             'resource' => 'countries'
        ])

        @include('widgets.index-table', [
                    'cols' => $cols,
                    'collection' => $countries,
                    'resource' => 'countries'
        ])

    </div>
@endsection
