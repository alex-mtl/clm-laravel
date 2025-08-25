@extends('layouts.app')

@section('content')
    <div class="content-main user-management">
        <div class="search-filters">
            <div class="search filter-search">
                <input  class="search " type="search" placeholder="Поиск по никнейму, email или клубу...">
            </div>
            <div class="filter-role">
                <x-custom-dropdown
                    name="role_filter"
                    :options="['all' => 'Все роли', 'admin' => 'Администратор', 'user' => 'Игрок', 'judge' => 'Судья']"
                    selected="all"
                />

            </div>
            <div class="filter-status">
                <x-custom-dropdown
                    name="status_filter"
                    :options="['all' => 'Все статусы', 'active' => 'Активный', 'banned' => 'Заблокированный', 'online' => 'Онлайн']"
                    selected="all"
                />

            </div>
            <div class="result-num">
                Найдено: {{ count($users) }} пользователей
            </div>

        </div>
        <div class="table-head">
            <div class="col-user user-props">Пользователь</div>
            <div class="col-rol user-roles">Роль</div>
            <div class="col-status user-status">Статус</div>
            <div class="col-rating user-rating">Рейтинг</div>
            <div class="col-reg user-reg-date">Регистрация</div>
            <div class="col-last-activity user-last-activity">Последняя активность</div>
            <div class="col-actions user-actions">Действия</div>
        </div>
{{--        @foreach($users as $user)--}}
{{--            <div class="user-row">--}}
{{--                <div class="user-props">--}}
{{--                    <div class="user-avatar"><img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '/img/no-avatar.svg'}}" ></div>--}}
{{--                    <div class="user-details">--}}
{{--                        <div class="user-name">{{ $user->name }}</div>--}}
{{--                        <div class="user-email">{{ $user->email }}</div>--}}
{{--                        <div class="user-club">{{ $user->club ?? 'No club' }}</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="user-roles"></div>--}}
{{--                <div class="user-status"></div>--}}
{{--                <div class="user-rating"></div>--}}
{{--                <div class="user-last-activity"></div>--}}
{{--                <div class="user-reg-date"></div>--}}
{{--                <div class="user-actions"></div>--}}
{{--            </div>--}}
{{--        @endforeach--}}

        @foreach($users as $user)
            <div class="user-row">
                <div class="user-props">
                    <div class="user-avatar"><img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '/img/no-avatar.svg'}}" ></div>
                    <div class="user-details">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                        <div class="user-club">{{ $user->club ?? 'No club' }}</div>
                    </div>
                </div>
                <div class="user-roles">{{ $user->roles }}</div>
                <div class="user-status">{{ $user->status }}</div>
                <div class="user-rating">{{ $user->rating }}</div>
                <div class="user-reg-date">{{ $user->reg_date }}</div>
                <div class="user-last-activity">{{ $user->last_activity }}</div>

                <div class="user-actions material-symbols-outlined">
                    <div class=" action-btn dark"><span>supervisor_account</span></div>
                    <div class=" action-btn dark "><span>block</span></div>
                    <div class=" action-btn dark "><span>person_edit</span></div>
                    <div class=" action-btn dark-red "><span>person_cancel</span></div>
                </div>
            </div>


        @endforeach

{{--        <div class="auto-layout-vertical">--}}
{{--        </div>--}}
{{--        <div class="rectangle">--}}
{{--        </div>--}}
{{--        <div class="wrapper">--}}
{{--            <div class="div">Роль</div>--}}
{{--        </div>--}}
{{--        <div class="container">--}}
{{--            <div class="div">Роль</div>--}}
{{--        </div>--}}
{{--        <div class="frame">--}}
{{--            <div class="div">Роль</div>--}}
{{--        </div>--}}
{{--        <div class="frame-div">--}}
{{--            <div class="div">Роль</div>--}}
{{--        </div>--}}
{{--        <div class="rectangle1">--}}
{{--        </div>--}}
{{--        <div class="div4">Активный</div>--}}
{{--        <div class="proplayer2024-parent">--}}
{{--            <div class="newbieplayer">ProPlayer2024</div>--}}
{{--            <div class="playerexamplecom">player@example.com</div>--}}
{{--            <div class="soul-club">Soul Club</div>--}}
{{--        </div>--}}
{{--        <div class="div5">15.06.2024</div>--}}
{{--        <div class="div6">2847</div>--}}
{{--        <div class="rectangle2">--}}
{{--        </div>--}}
{{--        <div class="rectangle3">--}}
{{--        </div>--}}
{{--        <div class="wrapper1">--}}
{{--            <div class="div7">Заблокировать</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper2">--}}
{{--            <div class="div7">Удалить</div>--}}
{{--        </div>--}}
{{--        <div class="div9">2 часа назад</div>--}}
{{--        <div class="rectangle4">--}}
{{--        </div>--}}
{{--        <div class="wrapper3">--}}
{{--            <div class="div7">Разблокировать</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper4">--}}
{{--            <div class="div">Заблокирован</div>--}}
{{--        </div>--}}
{{--        <div class="div12">20.07.2024</div>--}}
{{--        <div class="div13">1456</div>--}}
{{--        <div class="newbieplayer-parent">--}}
{{--            <div class="newbieplayer">NewbiePlayer</div>--}}
{{--            <div class="playerexamplecom">newbie@mail.ru</div>--}}
{{--            <div class="soul-club">Начинающие</div>--}}
{{--        </div>--}}
{{--        <div class="rectangle5">--}}
{{--        </div>--}}
{{--        <div class="wrapper5">--}}
{{--            <div class="div7">Удалить</div>--}}
{{--        </div>--}}
{{--        <div class="rectangle6">--}}
{{--        </div>--}}
{{--        <div class="div16">5 дней назад</div>--}}
{{--        <div class="rectangle7">--}}
{{--        </div>--}}
{{--        <div class="div17">Активный</div>--}}
{{--        <div class="div18">01.03.2024</div>--}}
{{--        <div class="div19">3120</div>--}}
{{--        <div class="rectangle8">--}}
{{--        </div>--}}
{{--        <div class="wrapper6">--}}
{{--            <div class="div7">Заблокировать</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper7">--}}
{{--            <div class="div7">Удалить</div>--}}
{{--        </div>--}}
{{--        <div class="div22">30 минут назад</div>--}}
{{--        <div class="mafiajudge-parent">--}}
{{--            <div class="newbieplayer">MafiaJudge</div>--}}
{{--            <div class="playerexamplecom">judge@clm.com</div>--}}
{{--            <div class="soul-club">CLM Official</div>--}}
{{--        </div>--}}
{{--        <div class="rectangle9">--}}
{{--        </div>--}}
{{--        <div class="div23">Активный</div>--}}
{{--        <div class="div24">01.01.2024</div>--}}
{{--        <div class="div25">2950</div>--}}
{{--        <div class="rectangle10">--}}
{{--        </div>--}}
{{--        <div class="wrapper8">--}}
{{--            <div class="div7">Заблокировать</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper9">--}}
{{--            <div class="div7">Удалить</div>--}}
{{--        </div>--}}
{{--        <div class="div28">Онлайн</div>--}}
{{--        <div class="adminuser-parent">--}}
{{--            <div class="newbieplayer">AdminUser</div>--}}
{{--            <div class="playerexamplecom">admin@clm.com</div>--}}
{{--            <div class="soul-club">CLM Official</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper10">--}}
{{--            <div class="div">Администратор</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper11">--}}
{{--            <div class="div">Судья</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper12">--}}
{{--            <div class="div31">Игрок</div>--}}
{{--        </div>--}}
{{--        <div class="wrapper13">--}}
{{--            <div class="div31">Игрок</div>--}}
{{--        </div>--}}
{{--        <div class="admin-child">--}}
{{--        </div>--}}
{{--        <div class="admin-item">--}}
{{--        </div>--}}
{{--        <div class="admin-inner">--}}
{{--        </div>--}}
{{--        <div class="rectangle-div">--}}
{{--        </div>--}}

{{--        <img class="frame-icon" alt="" src="Frame.svg">--}}

{{--        <img class="frame-icon1" alt="" src="Frame.svg">--}}

{{--        <img class="frame-icon2" alt="" src="Frame.svg">--}}

{{--        <img class="frame-icon3" alt="" src="Frame.svg">--}}

{{--        <img class="frame-icon4" alt="" src="Frame.svg">--}}





    </div>
@endsection
