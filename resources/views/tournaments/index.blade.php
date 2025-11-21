@extends('layouts.app')

@section('content')
    <div class="content-main">
        <div class="tournaments-div">
            <div class="club-menu">
                <a href="{{ route('tournaments.index') }}" class="menu-item {{ request()->routeIs('tournaments.index') ? 'active' : '' }}">
                    {{__('tournaments.upcoming')}}
                </a>
                <a href="{{ route('tournaments.past') }}" class="menu-item {{ request()->routeIs('tournaments.past') ? 'active' : '' }}">
                    {{__('tournaments.past')}}
                </a>
            </div>
        </div>
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
