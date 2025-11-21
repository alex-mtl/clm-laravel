@extends($layout ?: 'layouts.app')

@section('content')
    <div class="form-wrapper">
        <form class="flex-column w-20 gap-1" x-data="" x-ref="bestGuessForm"
              btnid="best-guess-btn" id="best-guess-form" action="{{ route('games.bestGuess', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>
                <div>Лучший ход</div>
                @foreach($bestGuess as $m => $target)

{{--                    <x-synchronized-input--}}
{{--                        name="bestGuess[{{$m}}]"--}}
{{--                        label="Подозреваемый"--}}
{{--                        value="{{ old('bestGuess.'.$m.'', $target) }}"--}}
{{--                        placeholder="0"--}}
{{--                        step="1"--}}
{{--                        max="10"--}}
{{--                        min="0"--}}
{{--                        type="number"--}}
{{--                    />--}}

                    <x-slot-selector
                        name="bestGuess[{{$m}}]"
                        label="Подозреваемый"
                        selected-slot="{{ old('bestGuess.'.$m.'', $target) }}"
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


        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="$refs.bestGuessForm.requestSubmit()">Продолжить</span>

            </div>
        </div>
        </form>
    </div>
@endsection
