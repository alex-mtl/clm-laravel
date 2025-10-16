@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form class="flex-column w-20 gap-1" x-data="" x-ref="shootingForm" id="shooting-form" action="{{ route('games.shooting', [$game->id]) }}" method="POST">
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

                    <x-synchronized-input
                        name="mafia[{{$mafia}}]"
                        label="Игрок {{ $mafia }}"
                        value="{{ old('mafia.'.$mafia.'', $target) }}"
                        placeholder="0"
                        step="1"
                        max="10"
                        min="0"
                        type="number"
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
