@extends('layouts.app')

@section('content')
    <div class="tournament-view flex-start gap-2">
        <div class="sidebar clm-border w-20">
            <div class="club-avatar-wrapper" title="{{ $tournament->club->name }}">
                @if($tournament->club->avatar)
                    <div class="club-avatar">
                        <a href="/clubs/{{ $tournament->club->id }}" >
                            <img src="{{ asset('storage/' . $tournament->club->avatar) }}">
                        </a>
                    </div>
                @else
                    <div class="user-avatar">
                        <a href="/clubs/{{ $tournament->club->id }}" >
                            <img src="/img/no-club.svg">
                        </a>
                    </div>
                @endif
            </div>

{{--            <div>Club: <a href="/clubs/{{ $tournament->club->id }}?tab=tournaments">{{ $tournament->club->name }}</a></div>--}}
            <div><a href="/clubs/{{ $tournament->club->id }}/tournaments/{{ $tournament->id }}">{{ $tournament->name }}</a></div>
{{--            @include('tournaments.sidebar', ['tournament' => $tournament])--}}
            @include('widgets.sidebar',
                [
                    'menu' => $sidebarMenu,
                ]
            )
        </div>

        <div class=" clm-border w-50">
            @include('tournaments.info')

            @include('tournaments.requests')

{{--            @include('tournaments.games')--}}

            @include('tournaments.participants')

            @include('tournaments.games')

            @include('tournaments.results')

            @include('tournaments.judges')


        </div>

    </div>
@endsection
