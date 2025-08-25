@extends('layouts.app')

@section('content')
    <div class="club-view">

        <div class="frame-parent">
            <div class="frame-wrapper">
                @include('clubs.join-leave-edit')
                <div class="frame-group">
                    <div class="rectangle-parent">
                        <div class="rectangle">
                            @include('clubs.logo', ['club' => $club])
                        </div>
                        <div class="auto-layout-vertical2">
                            <b class="club-name">{{ $club->name }}</b>
                            <div class="div7">{{ $club?->country?->name }} • Основан в 2023 году</div>
                        </div>
                    </div>
                    <div class="div8">Профессиональный клуб мафии с многолетней историей. Мы объединяем игроков всех уровней
                        для участия в турнирах и регулярных игровых вечерах. Наша цель — развитие спортивной мафии и
                        создание дружественного сообщества.
                    </div>
                    <div class="prop-line">
                        <div class="prop-value">Город:</div>
                        <div class="prop-label">{{ $club?->city?->name }}</div>
                    </div>
                    <div class="prop-line">
                        <div class="prop-value">Веб-сайт:</div>
                        <div class="prop-label">{{ $club->website ?? $club->name.'.clm.com' }} <a href="{{ $club->website ?? '#' }}" target="_blank">&#x1F517;</a></div>
                    </div>
                    <div class="prop-line">
                        <div class="prop-value">Email:</div>
                        <div class="prop-label">{{ $club->email ?? $club->name.'@clm.com' }}</div>
                    </div>

                    <div class="prop-line">
                        <div class="prop-value">Телефон:</div>
                        <div class="prop-label">{{ $club->phone_number }}</div>
                    </div>

                    <div class="prop-line">
                        <div class="prop-value">Языки:</div>
                        <div class="prop-label">Русский, English</div>
                    </div>

                </div>
            </div>
{{--            <div class="auto-layout-horizontal5">--}}
{{--                <b class="b">Участник</b>--}}
{{--            </div>--}}


        </div>
        <div class="club-details">
            <div class="club-metrics">
            <div class="metric">
                <div class="parent">
                    <b class="soul-club">{{ $club->members->count() }}</b>
                    <div class="div14">Участников</div>
                </div>
            </div>
            <div class="metric">
                <div class="container">
                    <b class="toronto">2456</b>
                    <div class="div7">рейтинг</div>
                </div>
            </div>
            <div class="metric">
                <div class="parent1">
                    <b class="soul-club">23</b>
                    <div class="div14">Турниров проведено</div>
                </div>
            </div>
            <div class="metric">
                <div class="din-ais-parent">
                    <b class="soul-club">{{ $club->owner->name }}</b>
                    <div class="div14">Глава клуба</div>
                </div>
            </div>
            </div>
            <div class="club-content">
                <div class="club-menu">
                    <div class="menu-item {{ session('tab') ? (session('tab') === 'members' ? 'active' : '') : 'active' }}"  data-action="members">
                        Участники

                    </div>
                    @if (auth()->id() === $club->owner_id)
                        <div class="menu-item {{ session('tab') === 'requests' ? 'active' : '' }}" data-action="requests">
                            Запросы
                        </div>
                    @endif
                    <div class="menu-item {{ session('tab') === 'events' ? 'active' : '' }}" data-action="events">Мероприятия</div>
                    <div class="menu-item {{ session('tab') === 'tournaments' ? 'active' : '' }}" data-action="tournaments">Турниры</div>
                    <div class="menu-item {{ session('tab') === 'games' ? 'active' : '' }}" data-action="games">Игры</div>

                </div>

                @include('clubs.members')

                @include('clubs.requests')

                @include('clubs.events')

                @include('clubs.tournaments')

                @include('clubs.games')

            </div>


        </div>
    </div>
@endsection
