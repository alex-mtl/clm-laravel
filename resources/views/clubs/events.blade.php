<div class="data-wrapper {{ session('tab') === 'events' ? '' : 'hidden' }}"  id="club-events-data" >
    @php
        $adminVisibility = auth()->user()->can('manage_club', $club);
        $eventCols = [
            [
                'name' => 'Название',
                'prop' => 'name'
            ],
            [
                'name' => 'Дата',
                'class' => 'w-10 center',
                'multiple' => true,
                'prop' => ['date_start_display','date_end_display']
            ],
//            [
//                'name' => 'Окончание',
//                'prop' => 'date_end_display'
//            ],
            [
                'name' => 'Описание',
                'prop' => 'description'
            ],

        ];
        if ($adminVisibility) {
            $eventCols[] = [
                'name' => 'Действия',
                'class' => 'w-10',
                'prop' => 'actions'
            ];
        }
    @endphp
    @can('manage_club', $club)
        @include('widgets.list-title', [
                'title' => 'Наши игровые вечера',
                 'resource' => 'clubs.events',
                 'resourceItem' => $club,
                 'endpoint' => route('clubs.events.create', $club),
                 'ajax' => true
            ])
    @endcan
    @include('widgets.index-table',
    [
        'cols' => json_decode(json_encode($eventCols), false),
        'collection' => $club->events()->orderBy('date_start', 'DESC')->paginate(10),
        'parent' => $club,
        'resourceItem' => $club,
        'resource' => 'clubs.events'
    ])
</div>
