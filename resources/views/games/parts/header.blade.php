<div class="slot-row flex-row gap-05">
    <span class="slot-number w-2">#</span>
    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles']) )
        <span class="w-2 role-container-title">Role</span>
    @endif
    <span class="w-4">&nbsp;</span>
    <span class="w-10">Player</span>


    @if($game->props['phase'] === 'shuffle-roles')

        <span class="w-10"></span>
    @endif

    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles','game-over', 'score', 'finished']) )
            <span class="w-2"></span>
    @endif
    @if($game->props['phase'] === 'score'  || $game->props['phase'] === 'game-over')
        <span class="w-4 hidden" title="Основной">Осн</span>
        <span class="w-4" title="Штраф">Штраф</span>
        <span class="w-4" title="Дополнительный">Мех</span>
        <span class="w-4 hidden" title="Компенсационные">CI</span>
        <span class="w-4" title="5-5">5 - 5</span>
        <span class="w-4" title="Дополнительный">Доп</span>


        <span class="w-4" title="Лучший ход">ЛХ</span>
        <span class="w-4">Всего</span>
    @endif

    @if($game->props['phase'] === 'finished')
        <span class="w-4 " title="Основной">Осн</span>
        <span class="w-4" title="Штраф">Штраф</span>
        <span class="w-4" title="Дополнительный">Мех</span>
        <span class="w-4" title="Компенсационные">CI</span>
        <span class="w-4" title="Дополнительный">Доп</span>
        <span class="w-4" title="Лучший ход">ЛХ</span>
        <span class="w-4">Всего</span>
    @endif
</div>
