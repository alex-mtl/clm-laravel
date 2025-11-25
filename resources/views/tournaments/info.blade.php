<div class="data-wrapper {{ session('tab') ? (session('tab') !== 'info' ? 'hidden' : '') : '' }}"  id="tournament-info-data" >
    <h1>{{ $tournament->name }}</h1>
    <div class="tournament-description">{{ $tournament->description ?? '' }}</div>
    @foreach($tournamentInfo as $info)
        @include('widgets.prop-line', [ 'label' => $info->label, 'value' => $info->value ])
    @endforeach

    @if($tournament->phase === 'in_progress')
        @can('calculate_scores', $tournament)
            <div class="flex-row gap-1" style="margin-top: 20px;">
                <span class="btn"
                      id="calculate-scores-btn"
                      x-data="{ loading: false }"
                      @click="calculateScores"
                      :disabled="loading"
                      x-text="loading ? 'Подсчет...' : 'Посчитать результаты'">
                    Посчитать результаты
                </span>
            </div>
        @endcan
    @endif
</div>
