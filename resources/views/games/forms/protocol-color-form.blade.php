@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form class="flex-column w-20 gap-1" x-data="" x-ref="protocolColorForm" id="protocol-color-form" action="{{ route('games.protocolColor', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>
            <x-custom-dropdown
                name="protocolColorDay"
                :options="$dayOptions"
                selected="{{ old('protocolColorDay', $protocolColorDay ) }}"
                label="Ночь"
            />

            <div>Цвет под протокол</div>

            <x-synchronized-input
                name="slot"
                label="Игрок"
                value="{{ old('slot', $slot) }}"
                placeholder="0"
                step="1"
                max="10"
                min="0"
                type="number"
            />

            <x-custom-dropdown
                name="color"
                :options="['red' => 'Красный', 'black' => 'Черный']"
                selected="{{ old('color', $color ) }}"
                label="Цвет"
            />

        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="$refs.protocolColorForm.requestSubmit()">Продолжить</span>

            </div>
        </div>
        </form>
    </div>
@endsection
