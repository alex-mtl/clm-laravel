<div class="flex-column gap-05">
    <div class="flex-column w100">
        @include('games.parts.timer')
    </div>



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
    <span class="btn center" onclick="toggleRoleVisibility()">Скрыть роли</span>

    <span class="btn center">ППК</span>

</div>
