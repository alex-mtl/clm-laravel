@extends('layouts.dashboard')

@section('content')
    <div class="clm-border gap-1">
        @include('widgets.list-title', [
            'title' => 'Типы запросов',
             'resource' => 'request-types'
        ])

        @include('widgets.index-table', [
                    'cols' => $cols,
                    'collection' => $requestTypes,
                    'resource' => 'request-types'
        ])

    </div>
@endsection
