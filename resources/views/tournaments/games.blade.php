<div class="data-wrapper {{ session('tab') === 'games' ? '' : 'hidden' }}"  id="tournament-games-data" >
    <div class="flex-row gap-2 space-between w100">
        <div class="flex">
            <h1>Игры</h1>

        </div>
        <x-ajax-modal
            endpoint="{{ route('tournaments.games.wizard', $tournament) }}"
            title="Сгенерировать расписание"
            icon="wand_stars"
            {{--                class="inline-btn"--}}
        />

    </div>
    <div class="data">
        @php
            $tablesCount = max(1, floor($tournament->players_quota / 10));
            $gameNumber = 0;
        @endphp

        @foreach($tournament->events as $event)
            <div > {{ $event->name }} </div>
            <div > {{ $event->date_start_display }}  <span class="text-gray-500">({{ $event->date_start->translatedFormat('l') }})</span></div>

            <div > {{ $event->description }} </div>
            <div class="flex-column gap-1">
                @foreach($event->games as $game)
{{--                    <div class="flex-row gap-1 space-between">--}}
                    <div class="prop-line">
{{--                        @for($i=0; $i < $tablesCount; $i++)--}}
                        <div class="flex-row gap-1">
                            @if($game->judge->avatar)
                                <div class="user-avatar">
                                    <a href="/users/{{ $game->judge->id }}" >
                                        <img src="{{ asset('storage/' . $game->judge->avatar) }}">
                                    </a>
                                </div>
                            @else
                                <div class="user-avatar">
                                    <a href="/users/{{ $game->judge->id }}" >
                                        <img src="/img/no-avatar.svg">
                                    </a>
                                </div>
                            @endif
                            <div class="flex-column">
                                <div > {{ $game->judge->name }} </div>
                                <div > {{ \App\Models\TournamentJudges::types[$game->judge_type] }} </div>
                            </div>
                        </div>

                                <div > {{ $game->name }} </div>

{{--                        <x-ajax-modal--}}
{{--                            endpoint="/games/{{ $game->id }}"--}}
{{--                            title="Провести игру"--}}
{{--                            icon="play_circle"--}}
{{--                            class="inline-btn"--}}
{{--                        />--}}
{{--                        --}}
                        @include('widgets.inline-btn', [
                            'title' => 'Провести игру',
                            'icon' => 'play_circle',
                            'class' => 'inline-btn',
                            'endpoint' => 'window.location.href=\''.sprintf('/games/%s/host', $game->id).'\';'
                        ])

{{--                        @endfor--}}
                    </div>
                @endforeach
            </div>

        @endforeach
    </div>
</div>
