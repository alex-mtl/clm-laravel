@extends('layouts.app')

@section('content')
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        const PHASES_ORDER = @json(App\Models\Game::PHASES_ORDER);
        let toasts = @json($toasts ?? []);
    </script>
    @csrf
    <input type="hidden" name="phase_code" value="{{ $game->props['phase-code'] }}">
    <input type="hidden" name="phase" value="{{ $game->props['phase'] }}">
    <input type="hidden" name="sub_phase" value="{{ $game->props['sub-phase'] ?? 'none' }}">
    <input type="hidden" name="game_day" value="{{ $game->props['day'] ?? '0' }}">
    <input type="hidden" name="game_id" value="{{ $game->id }}">
    <input type="hidden" name="teamwin" value="{{ $game->props['teamwin'] ?? 'none' }}">
    <input type="hidden" name="props_stream_key" value="{{ $game->props['stream']['stream-key'] ?? 'XXX' }}">
    <input type="hidden" name="stream_show_roles" value="{{ $game->props['stream']['show-roles'] ?? 'on' }}">
    <input type="hidden" name="active_speaker" value="{{ $game->props['days']['D'.$day]['active_speaker'] ?? 0 }}">
    <input type="hidden" name="speakers" value="{{ json_encode($game->props['days']['D'.$day]['speakers'] ?? []) }}">
    <input type="hidden" name="nominees" value="{{ json_encode($game->props['days']['D'.$day]['nominees'] ?? []) }}">


    <div class="game-view flex-start gap-2">
        <div class="sidebar flex-column  w-20 gap-1">
            @include('games.parts.sidebar')
            <div class="flex-column w100 clm-border gap-1">
                <span class="ta-center">
                    {{ $game->props['phase'] === 'day' ? 'День '.$game->props['day'] : 'Ночь '.$game->props['day'] }}
                </span>
            </div>
        </div>
        <div class="flex-column clm-border">
            @include('games.parts.header')

            @for($i=1; $i < 11; $i++)
                @php
                    $slot = $slots[$i];
                @endphp
                @include('games.parts.slot')
            @endfor

        </div>
    </div>
@endsection
