@extends('layouts.app')

@section('content')
    <div class="content-main">
        <div class="tiles gap-2">
            @foreach($tournaments as $tournament)
                @include('tournaments.tile', ['tournament' => $tournament])
            @endforeach
        </div>
        @if($tournaments->hasPages())
            <div class="pagination">
                {{ $tournaments->links() }}
            </div>
        @endif
    </div>
@endsection
