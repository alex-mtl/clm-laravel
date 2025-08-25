@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <div>Игрок номер: {{ $slot['slot'] }} > {{ $slot['name'] }}</div>
        <div>Восстановить игрока?</div>
        <form  x-data="" x-ref="restoreForm" id="restore-player-form" action="{{ route('games.slots.restore', [$game->id, $slot['slot']]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>

        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">

                <span class="btn"
                      x-data
{{--                      @click="document.getElementById('eliminate-player-form').submit()">Удалить</span>--}}
                      @click="$refs.restoreForm.requestSubmit()">Восстановить</span>

            </div>
        </div>
        </form>
    </div>
@endsection
