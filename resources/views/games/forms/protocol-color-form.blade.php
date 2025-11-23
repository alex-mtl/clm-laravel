@extends($layout ?: 'layouts.app')

@section('content')
    <div class="form-wrapper">
        <form class="flex-column w-20 gap-1"
              x-data=""
              x-ref="protocolColorForm"
              btnid="protocol-color-btn"
              id="protocol-color-form" action="{{ route('games.protocolColor', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>
            <x-custom-dropdown
                name="protocolColorDay"
                :options="$dayOptions"
                selected="{{ old('protocolColorDay', $protocolColorDay ) }}"
                label="Ночь"
            />

            <div>Цвет под протокол</div>

{{--            <x-synchronized-input--}}
{{--                name="slot"--}}
{{--                label="Игрок"--}}
{{--                value="{{ old('slot', $slot) }}"--}}
{{--                placeholder="0"--}}
{{--                step="1"--}}
{{--                max="10"--}}
{{--                min="0"--}}
{{--                type="number"--}}
{{--            />--}}

            <x-slot-selector
                name="slot"
                label="Игрок"
                selected-slot="{{ old('slot', $slot) }}"
                :slot-availability="[
                                0 => true,
                                1 => $slots[1]['status'] === 'alive',
                                2 => $slots[2]['status'] === 'alive',
                                3 => $slots[3]['status'] === 'alive',
                                4 =>  $slots[4]['status'] === 'alive',
                                5 =>  $slots[5]['status'] === 'alive',
                                6 =>  $slots[6]['status'] === 'alive',
                                7 =>  $slots[7]['status'] === 'alive',
                                8 => $slots[8]['status'] === 'alive',
                                9 =>  $slots[9]['status'] === 'alive',
                                10 =>  $slots[10]['status'] === 'alive'
                            ]"
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
