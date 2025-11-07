@extends($layout ?: 'layouts.app')

@section('content')
    <div class="form-wrapper">
        <form class="flex-column w-20 gap-1" x-data="" x-ref="bestGuessForm" id="best-guess-form" action="{{ route('games.bestGuess', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>
                <div>Лучший ход</div>
                @foreach($bestGuess as $m => $target)

                    <x-synchronized-input
                        name="bestGuess[{{$m}}]"
                        label="Подозреваемый"
                        value="{{ old('bestGuess.'.$m.'', $target) }}"
                        placeholder="0"
                        step="1"
                        max="10"
                        min="0"
                        type="number"
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
