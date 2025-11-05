<div class="data-wrapper {{ session('tab') === 'participants' ? '' : 'hidden' }}"  id="tournament-participants-data" >
    {{--                    <div class="parent2">--}}
        {{--                        <div class="div7">{{ $club->members->count() }} участников</div>--}}
        {{--                    </div>--}}
    <div class="title">
        @can('manage_tournament', $tournament)
            @if($tournament->phase === 'registration')
                @include('widgets.btn', ['btn' => (object)[
                    'name' => 'Исключить игровые пары',
                    'icon' => 'social_distance',
                    'class' => 'inline-btn ml-auto',
                    'endpoint' => route('tournaments.couples.create', ['%s']),
                    'endpoint_params' => [$tournament->id],

                    ]
                ])
            @endif
        @endcan
    </div>
    <div class="data" >
        @foreach($tournament->participants as $user)
        <div class="user-row">
            <div class="rectangle-group">
                @if($user->avatar)
                <div class="user-avatar">
                    <a href="/players/{{ $user->id }}" >
                        <img src="{{ asset('storage/' . $user->avatar) }}">
                    </a>
                </div>
                @else
                <div class="user-avatar">
                    <a href="/players/{{ $user->id }}" >
                        <img src="/img/no-avatar.svg">
                    </a>
                </div>
                @endif
            </div>
            <div class="user-details">
                <div >{{ $user->name }}</div>
                <div >Рейтинг: {{$user->user->rating ?? 0}} • Игр: {{ $user->user->games ?? 0 }}</div>
            </div>

            @can('manage_tournament', $tournament)
                @if($tournament->phase === 'registration')
                    <div class="ml-auto">
                        @include('widgets.btn', ['btn' => (object)[
                            'name' => 'Исключить',
                            'icon' => 'cancel',
                            'endpoint' => route('tournaments.partcipants.removeForm', ['%s','%s']),
                            'endpoint_params' => [$tournament->id, $user->id],

                            ]
                        ])

                    </div>
                @endif
            @endcan




        </div>
        @endforeach

    </div>
</div>
