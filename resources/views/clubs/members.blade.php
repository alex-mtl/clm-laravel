<div class="data-wrapper {{ session('tab') ? (session('tab') !== 'members' ? 'hidden' : '') : '' }}"  id="club-members-data" >
    {{--                    <div class="parent2">--}}
        {{--                        <div class="div7">{{ $club->members->count() }} участников</div>--}}
        {{--                    </div>--}}
    <div class="data" >
        @foreach($club->members as $user)
        <div class="user-row">
            <div class="rectangle-group">
                @if($user->avatar)
                <div class="user-avatar">
                    <a href="/users/{{ $user->id }}" >
                        <img src="{{ asset('storage/' . $user->avatar) }}">
                    </a>
                </div>
                @else
                <div class="user-avatar">
                    <a href="/users/{{ $user->id }}" >
                        <img src="/img/no-avatar.svg">
                    </a>
                </div>
                @endif
            </div>
            <div class="user-details">
                <div class="div23">{{ $user->name }}</div>
                <div class="div24">Рейтинг: {{$user->user->rating ?? 0}} • Игр: {{ $user->user->games ?? 0 }}</div>
            </div>
            <div class="user-status">Онлайн</div>
            @can('manage_club', $club)
                <div class="ml-auto">
                    @include('widgets.btn', ['btn' => (object)[
                        'name' => 'Исключить',
                        'icon' => 'cancel',
                        'endpoint' => route('clubs.members.removeForm', ['%s','%s']),
                        'endpoint_params' => [$club->id, $user->id],

                        ]
                    ])

                </div>
            @endcan


        </div>
        @endforeach

    </div>
</div>
