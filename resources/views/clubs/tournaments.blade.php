<div class="data-wrapper {{ session('tab') === 'tournaments' ? '' : 'hidden' }}"  id="club-tournaments-data" >

    @include('widgets.list-title', [
            'title' => 'Турниры',
             'resource' => 'clubs.tournaments',
             'resourceItem' => $club,
             'endpoint' => route('clubs.tournaments.create', $club),
             'ajax' => true
        ])
    @include('widgets.index-table',
    [
        'cols' => json_decode(json_encode([
            [
                'name' => 'Название',
                'prop' => 'name'
            ],
            [
                'name' => 'С',
                'prop' => 'date_start_display'
            ],
            [
                'name' => 'По',
                'prop' => 'date_end_display'
            ],
            [
                'name' => 'Действия',
                'class' => 'w-10',
                'prop' => 'actions'
            ],

        ]), false),
        'collection' => $club->tournaments()->orderBy('date_start', 'DESC')->paginate(10),
        'parent' => $club,
        'resourceItem' => $club,
        'resource' => 'clubs.tournaments'
    ])
</div>
