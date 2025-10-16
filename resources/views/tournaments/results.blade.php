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
            <span class="w-3 ta-right">Балл</span>
            <span class="w-2 ta-right">{{ formatNumber($participant->score_1 ?? 0) }}</span>
            <span class="w-2 ta-right">{{ formatNumber($participant->score_2 ?? 0) }}</span>
            <span class="w-2 ta-right">{{ formatNumber($participant->score_3 ?? 0) }}</span>
            <span class="w-2 ta-right">ЛХ</span>
            <span class="w-2 ta-right">{{ formatNumber($participant->score_5 ?? 0) }}</span>
            <span class="w-2">Д</span>
            <span class="w-2">Ш</span>
            <span class="w-2">П</span>
            <span class="w-2">ПУ</span>
            <span class="w-2">И</span>

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
                <span class="w-3 ta-right">{{ number_format($participant->score_total ?? 0, 2,'.', '') }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_1 ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_2 ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_3 ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_4 ?? 0) }}</span>
                <span class="w-2 ta-right">{{ formatNumber($participant->score_5 ?? 0) }}</span>
                <span class="w-2">{{ $participant->don_wins ?? 0}}</span>
                <span class="w-2">{{ $participant->sheriff_wins ?? 0}}</span>
                <span class="w-2">{{ $participant->wins ?? 0}}</span>
                <span class="w-2">{{ $participant->first_kills ?? 0}}</span>
                <span class="w-2">{{ $participant->games_played ?? 0}}</span>

            </div>
            @php
                $i++;
            @endphp
        @endforeach

    </div>
</div>
