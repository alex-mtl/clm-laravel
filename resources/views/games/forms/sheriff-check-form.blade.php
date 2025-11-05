@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form class="flex-column w-20 gap-1" x-data="" x-ref="sheriffCheckForm" id="sheriff-check-form" action="{{ route('games.sheriffCheck', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>

            <x-custom-dropdown
                name="sheriffCheckDay"
                :options="$dayOptions"
                selected="{{ old('sheriffCheckDay', $sheriffCheckDay ) }}"
                label="Ночь"
            />

            <div>Шериф проверяет</div>
            <div>Кто мафия?</div>

{{--            <x-synchronized-input--}}
{{--                name="sheriffCheck"--}}
{{--                label="Проверить"--}}
{{--                value="{{ old('sheriffCheck', $sheriffCheck) }}"--}}
{{--                placeholder="0"--}}
{{--                step="1"--}}
{{--                max="10"--}}
{{--                min="0"--}}
{{--                type="number"--}}
{{--            />--}}

@php
//dd($roles);
@endphp
            <x-slot-selector
                name="sheriffCheck"
                selected-slot="{{ old('sheriffCheck', $sheriffCheck) }}"
                :slot-roles="$roles"
                :slot-availability="[
                                0 => true,
                                1 => true,
                                2 => true,
                                3 => true,
                                4 => true,
                                5 => true,
                                6 => true,
                                7 => true,
                                8 => true,
                                9 => true,
                                10 => true
                            ]"
            />

        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="$refs.sheriffCheckForm.requestSubmit()">Продолжить</span>

            </div>
        </div>
        </form>
    </div>
@endsection
