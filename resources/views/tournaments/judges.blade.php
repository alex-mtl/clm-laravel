<div class="data-wrapper {{ session('tab') === 'judges' ? '' : 'hidden' }}"  id="tournament-judges-data" >
{{--    @include('widgets.list-title', [--}}
{{--        'title' => '',--}}
{{--         'resource' => 'tournaments.judges',--}}
{{--         'resourceItem' => $tournament,--}}
{{--         'endpoint' => route('tournaments.judges.create', $tournament),--}}
{{--         'ajax' => true--}}
{{--    ])--}}
    <div class="flex-row gap-2 space-between w100">
        <div class="flex">
            <h1>Судьи</h1>

        </div>
        @can('manage_tournament', $tournament)
            <x-ajax-modal
                endpoint="{{ route('tournaments.judges.create', $tournament) }}"
                title="Добавить судью"
                icon="add_circle"
                {{--                class="inline-btn"--}}
            />
        @endcan

    </div>

    <div class="data" >
        @foreach($tournament->judges as $judge)
            <div class="user-row">
                <div class="">
                    @if($judge->avatar)
                        <div class="user-avatar">
                            <a href="/users/{{ $judge->id }}" >
                                <img src="{{ asset('storage/' . $judge->avatar) }}">
                            </a>
                        </div>
                    @else
                        <div class="user-avatar">
                            <a href="/users/{{ $judge->id }}" >
                                <img src="/img/no-avatar.svg">
                            </a>
                        </div>
                    @endif
                </div>
                <div class="user-details">
                    <div class="div23">{{ $judge->name }}</div>
                    <div class="div24">Рейтинг: {{$judge->rating ?? 0}} • Игр: {{ $judge->games ?? 0 }}</div>
                </div>

                <div class="ml-auto">{{ \App\Models\TournamentJudges::types[$judge->pivot->type ?? 'judge'] }}</div>


            </div>
        @endforeach

    </div>
</div>
