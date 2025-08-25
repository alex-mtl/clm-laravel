@extends('layouts.app')

@section('content')

        <div class="content-main clubs-parent">
{{--            <b class="b">Клубы</b>--}}
{{--            <div class="div7">Найдите свой клуб или создайте новый</div>--}}
            <div class="search">
                <div class="auto-layout-horizontal5">
                    <div class="div8">Поиск клубов по названию или городу...</div>
                </div>
                <div class="auto-layout-horizontal6">
                    <div class="auto-layout-horizontal7">
                        <div class="div8">По рейтингу</div>
                        <img class="vector-icon1" alt="" src="Vector.svg">

                    </div>
                </div>
            </div>
            <div class="frame-parent">
                @foreach($clubs as $club)
                    <div class="club-row">
                        <div class="frame-group">
                            <div class="frame-container">
                                <div class="image-parent">
                                    <img class="image-icon" alt="" src="{{ asset('storage/' . $club->avatar) ?? 'img/clm-logo.svg' }}">

                                    <div class="mafia-masters-parent">
                                        <div class="club-name">{{ $club->name }}</div>
                                        <div class="container">
                                            <div class="club-city">{{ $club->city->name ?? 'New York' }}</div>
                                            <div class="div11">•</div>
                                            <div class="div12">{{ $club->members->count() ?? 0 }} участников</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="frame-div">
                                    <div class="club-rate">{{ $club->rating ?? 0 }}</div>
                                    <div class="rating-label">рейтинг</div>
                                </div>
                            </div>
                            <div class="frame-parent1">
                                <div class="wrapper">
                                    <div class="div15">Участник</div>
                                </div>
                                <div class="frame btn">
                                    <div class="div15"><a href="{{ route('clubs.show', $club) }}">Подробнее</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="parent3">
                    <b class="b1">Создать свой клуб</b>
                    <div class="div24">Соберите команду единомышленников и участвуйте в турнирах вместе</div>
                    <div class="frame-parent5">
                        <div class="wrapper3 btn">
                            <div class="div8"><a href="{{ route('clubs.create') }}">Создать клуб</a></div>
                        </div>
                        <div class="wrapper4">
                            <div class="div8">Узнать больше</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </body>
    </html>
@endsection
