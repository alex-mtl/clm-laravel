<script>
    window.csrfToken = '{{ csrf_token() }}';
</script>
@extends('layouts.app')

@section('content')
    @csrf
    <input type="hidden" name="phase" value="{{ $game->props['phase'] }}">
    <input type="hidden" name="sub_phase" value="{{ $game->props['sub-phase'] ?? 'none' }}">
    <input type="hidden" name="game_day" value="{{ $game->props['day'] ?? '0' }}">
    <input type="hidden" name="game_id" value="{{ $game->id }}">

    <div class="game-view flex-start gap-2">
        <div class="sidebar flex-column clm-border w-20">
            @include('games.parts.sidebar')

        </div>
        <div class="flex-column clm-border">
                @for($i=1; $i < 11; $i++)
                    @php
                        $slot = $slots[$i];
                    @endphp
                    @include('games.parts.slot')
                @endfor
            </div>
        </div>
    </div>
@endsection
