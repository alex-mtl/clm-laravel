<div class="flex-column clm-border gap-05">
    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles', 'score', 'finished']) )
        <div class="flex-column w100">
            @include('games.parts.timer')
        </div>


    @endif




    <div class="flex-row w100 space-between gap-1">
        @include('widgets.inline-btn', [
            'title' => 'Назад',
            'icon' => 'keyboard_double_arrow_left',
            'class' => 'inline-btn',
            'endpoint' => 'submitGamePhaseBack()'
        ])
        <span class="">
            {{ $game->props['phase-title'] }}
        </span>
        @include('widgets.inline-btn', [
            'title' => 'Далее',
            'icon' => 'keyboard_double_arrow_right',
            'class' => 'inline-btn',
            'endpoint' => 'submitGameState()'
        ])
    </div>
    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles', 'score', 'finished']) )
        @include('games.parts.actions')
    @endif
    @if($game->props['phase'] === 'shuffle-roles')
        <span class="btn center" onclick="shuffleRoles(this)">Раздать роли</span>
    @elseif(in_array($game->props['phase'], ['day', 'night']))
        <span class="btn center" onclick="toggleRoleVisibility(this)">Скрыть роли</span>
    @endif

{{--    <span class="btn center">ППК</span>--}}
    @if(!in_array($game->props['phase'], [ 'shuffle-slots', 'shuffle-roles', 'score']))
        <span class="btn center" onclick="setScorePhase()">Результаты</span>
    @endif

    @if($game->props['phase'] === 'score' || $game->props['phase'] === 'game-over')
        <div class="flex-row space-between gap-1">
            <span class="btn ta-center " onclick="teamWin('red')">Победа Города</span>
            <span class="btn ta-center " onclick="teamWin('black')">Победа Мафии</span>
        </div>
        <span class="btn center" onclick="submitScores()">Сохранить результаты игры</span>
    @endif
    @if($game->props['phase'] === 'finished')
        <span class="btn center" onclick="window.location.href = '{{ route('tournaments.show', $game->event->tournament). '?tab=games' }}';">Турнир</span>
    @endif

    <div class="flex-row space-between gap-1">

        @include('widgets.btn', ['btn' => (object)[
            'name' => 'Настройки трансляции',
            'icon' => 'video_settings',
            'endpoint' => route('games.stream.settings', ['%s']),
            'endpoint_params' => [$game->id],
            'callback' => 'streamSettingsCallback',
//                                'class' => 'btn'

            ]
        ])


        @include('widgets.inline-btn', [
           'title' => 'Скопировать',
           'class' => 'inline-btn',
           'icon' => 'content_copy',
           'endpoint' => 'copyStreamLink()',
        ])

        @include('widgets.inline-btn', [
           'title' => 'Открыть',
           'class' => 'inline-btn',
           'icon' => 'open_in_new',
           'endpoint' => 'openStreamLink()',
       ])

        @include('widgets.inline-btn', [
            'title' => 'Начать трансляцию',
            'icon' => 'cast',
            'btnid' => 'start-stream-btn',
            'class' => 'inline-btn '. (($game->props['stream']['enabled'] ?? false) === 'live' ? 'success' : ''),
            'endpoint' => 'startStream(' . $game->id .')',
            'endpoint_params' => [$game->id],
            'callback '=> "startStreamHandler",
        ])
    </div>
</div>
