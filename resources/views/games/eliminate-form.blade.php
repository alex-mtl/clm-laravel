@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <div>Игрок номер: {{ $slot['slot'] }} > {{ $slot['name'] }}</div>
        <div>Удалить игрока?</div>
        <form class="flex-column w-15" x-data="" x-ref="eliminateForm" id="eliminate-player-form" action="{{ route('games.slots.eliminate', [$game->id, $slot['slot']]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>

            <x-custom-dropdown
                name="status"
                :options="['voted' => 'Заголосован', 'killed' => 'Убит', 'eliminated' => 'Удален']"
                selected="{{ old('status', $slot['status'] ?? 'eliminated' ) }}"
                label="Вид удаления"
            />

        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">

                <span class="btn"
                      x-data
{{--                      @click="document.getElementById('eliminate-player-form').submit()">Удалить</span>--}}
                      @click="$refs.eliminateForm.requestSubmit()">Удалить</span>

            </div>
        </div>
        </form>
    </div>
@endsection
