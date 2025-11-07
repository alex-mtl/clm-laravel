@extends('layouts.custom')

@section('content')
    <script>
        window.csrfToken = '{{ csrf_token() }}';
    </script>
<div class="stream-game">
    @csrf
    <div class="flex-start gap-05 space-between">
        <div class="flex-column mr-auto gap-01">
            <div class="flex-row gap-05">
                <div class="list" data-type="killed-list"></div>
                <div class="list" data-type="don-checks-list"></div>
                <div class="list" data-type="sheriff-checks-list"></div>
                <div class="list" data-type="voted-list"></div>
            </div>
            <div class="flex-row gap-05">


            </div>
            <div class="list" data-type="candidate-list"></div>
        </div>
        <div class="flex-column w-20 gap-01">
            <span class="list w100 ta-center game-name"><span class="list-content w100">{{ $game->name }}</span></span>
            <span class="list w100 ta-center phase"><span class="list-content w100">{{ $game->props['phase-title'] }}</span></span>
            <span class="list w100 ta-center judge"><span class="list-content w100">Судья: {{ $game->judge->name }}</span></span>

        </div>
    </div>

    <div class="main-area">
    </div>

    <div class="player-row w100 ml-auto mr-auto">
        <!-- 10 players -->
        @foreach($slots as $i => $slot)
            <div class="player-card" data-slot="{{ $i }}" data-role="{{ $slot['role'] }}">
                <img src="{{ $slot['avatar'] }}" />
                <div class="player-status"></div>
                <div class="protocol-color hidden"></div>
                <div class="role-icon"></div>
                <div class="warn"></div>
                <div class="best-guess list hidden"></div>
                <div class="number">{{ $i }}</div>
                <div class="name">{{ $slot['name'] ?? 'Player '.$i }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const currentGame = @json($currentGame);

    </script>
@endpush
