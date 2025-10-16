@extends('layouts.app')

@section('content')
{{--    <div class="club-view">--}}
    <div class="flex-start gap-2 mt-1 mb-1">

        <div class="flex-column gap-1 w-30 clm-border relative">

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
                    <div class=""> {!! nl2br(e($club->description)) !!} </div>
                </div>
                <div class="prop-line">
                    <div class="prop-value">Город:</div>
                    <div class="prop-label">{{ $club?->city?->name }}</div>
                </div>
                <div class="prop-line">
                    <div class="prop-value">Веб-сайт:</div>
                    <div class="prop-label">
                        {{ str_replace(['https://', 'http://'], '', $club->website ?? $club->name . '.clm.com') }}
                        <a href="{{ $club->website ?? '#' }}" target="_blank">&#x1F517;</a>
                    </div>
                </div>
                <div class="prop-line">
                    <div class="prop-value">Email:</div>
                    <div class="prop-label">{{ $club->email ?? $club->name.'@clm.com' }}</div>
                </div>

                <div class="prop-line">
                    <div class="prop-value">Телефон:</div>
                    <div class="prop-label">{{ $club->phone_number }} <a href="tel:{{ $club->phone_number }}" target="_blank">&nbsp &#x260F;</a></div>
                </div>

                <div class="prop-line">
                    <div class="prop-value">Языки:</div>
                    <div class="prop-label">Русский, English</div>
                </div>


        </div>


{{--        <div class="club-details">--}}
        <div class="flex-column gap-1 w-50 clm-border">
            <div class="club-metrics">
            <div class="clm-border flex-column gap-1 ta-center w-10">

                <span><b class="">{{ $club->members->count() }}</b></span>
                <div class="">Участников</div>

            </div>
            <div class="clm-border flex-column gap-1 ta-center w-10">

                <span><b class="">{{ $club->rating ?? 0 }}</b></span>
                <div class="">рейтинг</div>

            </div>
            <div class="clm-border flex-column gap-1 ta-center w-10">

                <span><b class="">{{ $club->tournaments->count() ?? 0 }}</b></span>
                <div class="">Турниров</div>

            </div>
            <div class="clm-border flex-column gap-1 ta-center w-10">

                <span><b class="">{{ $club->owner->name }}</b></span>
                <div class="">Глава</div>

            </div>
            </div>
            <div class="club-content">
                <div class="club-menu">
                    <div class="menu-item {{ session('tab') ? (session('tab') === 'members' ? 'active' : '') : 'active' }}"  data-action="members">
                        Участники

                    </div>
                    @can('manage_club', $club)
                        <div class="menu-item {{ session('tab') === 'requests' ? 'active' : '' }}" data-action="requests">
                            Запросы
                        </div>
                    @endcan
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
