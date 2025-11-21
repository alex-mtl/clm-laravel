<div class="data-wrapper {{ session('tab') === 'results' ? '' : 'hidden' }}"  id="tournament-results-data" >
    <h1>Результаты</h1>
    <div class="data" >
        @php
            function formatNumber($number) {
                return (float)$number == (int)$number
                    ? (string)(int)$number
                    : number_format($number, 1, '.', '');
            }
                $i = 1;
        @endphp
        <div class="user-row space-between">
            <span class="w-1">#</span>
            <div class="w-5">

            </div>
            <div class="w-10">
                <div class="">Игрок</div>
            </div>
            <span class="w-3 ta-right">БT</span>
            <span class="w-3 ta-right">Б</span>
            <span class="w-3 ta-center">ПИ</span>
            <span class="w-2 ta-right">-</span>
            <span class="w-2 ta-right">М</span>
            <span class="w-2 ta-right">+</span>
            <span class="w-2 ta-right">ЛХ</span>
            <span class="w-2 ta-right">Ci</span>
            <span class="w-2 ta-right">ПУ</span>
{{--            <span class="w-2 ta-right">{{ formatNumber($participant->score_5 ?? 0) }}</span>--}}
            <span class="w-2 ta-center">К</span>
            <span class="w-2 ta-center">Ш</span>
            <span class="w-2 ta-center">Ч</span>
            <span class="w-2 ta-center">Д</span>


        </div>
        @foreach($results as $participant)
            <div class="user-row space-between">
                <span class="w-1">{{ $i }}</span>
                <div class="rectangle-group">
                    <div class="user-avatar">
                        <a href="/players/{{ $participant->user_id }}" >
                            <img src="{{ ($participant->avatar) ? asset('storage/' . $participant->avatar) : '/img/no-avatar.svg' }}">
                        </a>
                    </div>
                </div>
                <div class="user-details w-10">
                    <div >{{ $participant->name }}</div>
                </div>
                <span class="w-3 ta-right">{{ number_format($tournament->getPointsForPlace($i), 1,'.', '') }}</span>
                <span class="w-3 ta-right">{{ number_format($participant->score_total ?? 0, 2,'.', '') }}</span>
                <span class="w-3 ta-center">{{ ($participant->wins ?? 0).'/'.($participant->games_played ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_1 ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_2 ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_3 ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->ci ?? 0) }}</span>
                <span class="w-2 ta-right">{{ $participant->first_kills ?? 0}}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_4 ?? 0) }}</span>
{{--                <span class="w-2 ta-right">{{ formatNumber($participant->score_5 ?? 0) }}</span>--}}
                <span class="w-2 ta-center">{{ ($participant->citizen_wins ?? 0).'/'.($participant->citizen_games ?? 0)}}</span>
                <span class="w-2 ta-center">{{ ($participant->sheriff_wins ?? 0).'/'.($participant->sheriff_games ?? 0)}}</span>
                <span class="w-2 ta-center">{{ ($participant->mafia_wins ?? 0).'/'.($participant->mafia_games ?? 0)}}</span>
                <span class="w-2 ta-center">{{ ($participant->don_wins ?? 0).'/'.($participant->don_games ?? 0)}}</span>


            </div>
            @php
                $i++;
            @endphp
        @endforeach

        <div class="flex-column">
            <span>БT = Баллы турнира</span>
            <span>Б  = Баллы итого</span>
            <span>ПИ = Побед/Игр</span>
            <span>-  = Штраф</span>
            <span>М  = Механические допы</span>
            <span>+  = Допы</span>
            <span>ЛХ = Допы за лучший ход</span>
            <span>Ci = Компенсационные баллы для первого убиенного игрока</span>
            <span>ПУ = Первый убиенный</span>
            {{--            <span class="w-2 ta-right">{{ formatNumber($participant->score_5 ?? 0) }}</span>--}}
            <span>К - Побед/Игр на карте мирного жителя</span>
            <span>Ш - Побед/Игр на карте шерифа</span>
            <span>Ч - Побед/Игр на карте мафии</span>
            <span>Д - Побед/Игр на карте дона</span>

        </div>



    </div>
</div>
