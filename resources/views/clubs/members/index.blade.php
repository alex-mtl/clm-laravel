@extends($layout ?: 'layouts.app')

@section('content')
    <div class="container">
        <h1>Участники клуба {{ $club->name }}</h1>

        <div class="mb-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addMemberModal">
                Добавить участника
            </button>
        </div>

        <div class="data-wrapper">

            <div class="flex-row gap-1 space-between">
                <span>Имя</span>
                <span>Роли</span>
                <span>Действия</span>
            </div>


            @foreach($members as $member)
                <div class="flex-row gap-1 space-between">
                    <div>{{ $member->name }}</div>
                    <div>
                        @foreach($member->roles as $role)
                            @if($role->pivot->club_id == $club->id || $role->scope == 'global')
                                <span class="badge badge-primary">
                                    {{ $role->name }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                    <div>
                        @include('widgets.btn', ['btn' => (object)[
                            'name' => 'Назначить роль',
                            'icon' => 'add_moderator',
                            'endpoint' => route('users.roles.club-assign', ['%s','%s']),
                            'endpoint_params' => [$member->id, $club->id],

                            ]
                        ])
                    </div>
                </div>
            @endforeach

        </div>
    </div>

@endsection


