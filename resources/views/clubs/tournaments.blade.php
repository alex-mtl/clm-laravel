<div class="data-wrapper {{ session('tab') === 'tournaments' ? '' : 'hidden' }}"  id="club-tournaments-data" >
@php
    $adminVisibility = auth()->user()->can('manage_club', $club);
    $tournamentCols = [
            [
                'name' => 'Название',
                'prop' => 'name',
                'link' => route('tournaments.show', '%s')
            ],
            [
                'name' => 'С',
                'prop' => 'date_start_display',
                'class' => 'w-10 center'
            ],
            [
                'name' => 'По',
                'prop' => 'date_end_display',
                'class' => 'w-10 center'
            ],
        ];
    if ($adminVisibility) {
        $tournamentCols[] = [
            'name' => 'Действия',
            'class' => 'w-10',
            'prop' => 'actions'
        ];
    }

@endphp
    @can('manage_club', $club)
        @include('widgets.list-title', [
                'title' => 'Турниры',
                 'resource' => 'clubs.tournaments',
                 'resourceItem' => $club,
                 'endpoint' => route('clubs.tournaments.create', $club),
                 'ajax' => true
            ])
    @endcan
    @include('widgets.index-table',
    [
        'cols' => json_decode(json_encode($tournamentCols), false),
        'collection' => $club->tournaments()->orderBy('date_start', 'DESC')->paginate(10),
        'parent' => $club,
        'resourceItem' => $club,
        'resource' => 'clubs.tournaments'

    ])
</div>
