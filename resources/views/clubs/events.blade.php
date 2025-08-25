<div class="data-wrapper {{ session('tab') === 'events' ? '' : 'hidden' }}"  id="club-events-data" >

    @include('widgets.list-title', [
            'title' => 'Наши игровые вечера',
             'resource' => 'clubs.events',
             'resourceItem' => $club,
             'endpoint' => route('clubs.events.create', $club),
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
                'name' => 'Начало',
                'prop' => 'date_start_display'
            ],
            [
                'name' => 'Окончание',
                'prop' => 'date_end_display'
            ],
            [
                'name' => 'Описание',
                'prop' => 'description'
            ],
             [
                'name' => 'Действия',
                'class' => 'w-10',
                'prop' => 'actions'
            ],

        ]), false),
        'collection' => $club->events()->orderBy('date_start', 'DESC')->paginate(10),
        'parent' => $club,
        'resourceItem' => $club,
        'resource' => 'clubs.events'
    ])
</div>
