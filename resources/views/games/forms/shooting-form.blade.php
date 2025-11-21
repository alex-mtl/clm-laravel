@extends($layout ?: 'layouts.app')

@section('content')
    <div class="form-wrapper">
        <form class="flex-column w-20 gap-1"
              x-data=""
              x-ref="shootingForm"
              btnid="shooting-btn"
              id="shooting-form" action="{{ route('games.shooting', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>
            @if(empty($mafTeam))

                <div>Странное дело, не обнаружено ни одного живого игрока из команды мафии!</div>

            @else
                <div>Стрельба!</div>

                <x-custom-dropdown
                    name="shootingDay"
                    :options="$dayOptions"
                    selected="{{ old('shootingDay', $shootingDay ) }}"
                    label="Ночь"
                />
                @foreach($mafTeam as $mafia => $target)

{{--                    <x-synchronized-input--}}
{{--                        name="mafia[{{$mafia}}]"--}}
{{--                        label="Игрок {{ $mafia }}"--}}
{{--                        value="{{ old('mafia.'.$mafia.'', $target) }}"--}}
{{--                        placeholder="0"--}}
{{--                        step="1"--}}
{{--                        max="10"--}}
{{--                        min="0"--}}
{{--                        type="number"--}}
{{--                    />--}}

                    <x-slot-selector
                        name="mafia[{{$mafia}}]"
                        label="Игрок {{ $mafia }}"
                        selected-slot="{{ old('mafia.'.$mafia.'', $target) }}"
{{--                        :slot-roles="$roles"--}}
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

                @endforeach

            @endif
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="$refs.shootingForm.requestSubmit()">Продолжить</span>

            </div>
        </div>
        </form>
    </div>
@endsection
