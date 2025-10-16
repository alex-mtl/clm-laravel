@extends('layouts.app')

@section('content')
    <div class="tournament-view flex-start gap-2 mt-1 mb-1">
        <div class="sidebar clm-border w-20">
            <div class="player-avatar-wrapper" title="{{ $player->name }}">
                @if($player->avatar)
                    <div class="user-avatar">

                            <img src="{{ asset('storage/' . $player->avatar) }}">

                    </div>
                @else
                    <div class="user-avatar">

                            <img src="/img/no-avatar.svg">

                    </div>
                @endif
            </div>

{{--            <div>Club: <a href="/clubs/{{ $tournament->club->id }}?tab=tournaments">{{ $tournament->club->name }}</a></div>--}}
            <div>{{ $player->name }}</div>
{{--            @include('tournaments.sidebar', ['tournament' => $tournament])--}}
            @include('widgets.sidebar',
                [
                    'menu' => $sidebarMenu,
                ]
            )
        </div>

        <div class=" clm-border w-50">
            @include('players.info')


        </div>

    </div>
@endsection
