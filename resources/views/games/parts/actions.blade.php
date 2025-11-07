
<div class="flex-row w100 space-between gap-05">
    <x-ajax-modal
        btnid="best-guess-btn"
        hidden="{{ 'false' }}"
        endpoint="{{ route('games.bestGuessForm', ['game' => $game->id]) }}"
        title="Лучший ход"
        class="inline-btn {{  $game->props['phase-code'] === 'BEST-GUESS' ? 'active' : '' }}"
        callback="bestGuessHandler"
        icon="question_mark"
        container="game-action-form-container"
    />
    <x-ajax-modal
        btnid="voting-btn"
        hidden="{{ 'false' }}"
        endpoint="{{ route('games.votingForm', ['game' => $game->id]) }}"
        title="Голосование"
        class="inline-btn {{ $game->props['phase-code'] === 'VOTING' ? 'active' : '' }}"
        callback="votingHandler"
        icon="thumbs_up_down"
        container="game-action-form-container"
    />
    <x-ajax-modal
        btnid="shooting-btn"
        hidden="{{ 'false' }}"
        endpoint="{{ route('games.shootingForm', ['game' => $game->id]).($game->props['phase-code'] === 'LAST-SPEECH-VOTED' ? '?addDay=true' : '') }}"
        title="Стрельба"
        class="inline-btn {{  in_array($game->props['phase-code'], ['SHOOTING', 'LAST-SPEECH-VOTED'])  ? 'active' : '' }}"
        callback="shootingHandler"
        icon="my_location"
        container="game-action-form-container"
    />

    <x-ajax-modal
        btnid="don-check-btn"
        hidden="{{ 'false' }}"
        endpoint="{{ route('games.donCheckForm', ['game' => $game->id]) }}"
        title="Проверка дона"
        class="inline-btn {{  $game->props['phase-code'] === 'DON-CHECK' ? 'active' : '' }}"
        callback="donCheckHandler"
        icon="domino_mask"
        container="game-action-form-container"
    />

    <x-ajax-modal
        btnid="sheriff-check-btn"
        hidden="{{ 'false' }}"
        endpoint="{{ route('games.sheriffCheckForm', ['game' => $game->id]) }}"
        title="Проверка шерифа"
        class="inline-btn {{ $game->props['phase-code'] === 'SHERIFF-CHECK' ? 'active' : '' }}"
        callback="sheriffCheckHandler"
        icon="local_police"
        container="game-action-form-container"
    />

    <x-ajax-modal
        btnid="protocol-color-btn"
        hidden="{{ 'false' }}"
        endpoint="{{ route('games.protocolColorForm', ['game' => $game->id]) }}"
        title="Цвет под протокол"
        class="inline-btn {{ in_array($game->props['phase-code'], ['PROTOCOL-COLOR','LAST-SPEECH-KILLED']) ? 'active' : '' }}"
        callback="protocolColorHandler"
        icon="invert_colors"
        container="game-action-form-container"
    />
</div>
