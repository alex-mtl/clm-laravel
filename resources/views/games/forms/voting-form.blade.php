@extends($layout ?: 'layouts.app')

@section('content')
    <div class="form-wrapper">

        <form class="flex-column w-20 gap-1" x-data=""
              x-ref="votingForm"
              btnid="voting-btn"
              id="voting-form" action="{{ route('games.voting', [$game->id]) }}" method="POST">
            @csrf
{{--            <button class="hidden" type="submit">Save</button>--}}
{{--            <span>{{ $votingDay }}</span>--}}
            <x-custom-dropdown
                name="votingDay"
                :options="$dayOptions"
                selected="{{ old('votingDay', $votingDay ) }}"
                label="День"
            />

            <input type="hidden" name="night" value="off" x-ref="extraActionField">
            @if(empty($nominees))
                <input type="hidden" name="no_voting" value="1">
                <input type="hidden" name="no_voting_reason" value="Сегодня никто не был выставлен!">
                <div>Сегодня никто не был выставлен!</div>
            @elseif($game->props['day'] === 0 && count($nominees) === 1)
                <div>На голосование выставлена одна кандидатура.</div>
                <div>Голосование не проводится!</div>
                <input type="hidden" name="no_voting" value="1">
                <input type="hidden" name="no_voting_reason" value="На голосование выставлена одна кандидатура.\nГолосование не проводится!">
            @elseif(count($nominees) === 1)
                <div>На голосование выставлена одна кандидатура.</div>
                <div>Игрок номер {{ $nominees[0]['candidate'] }} автоматически заголосован!</div>
                <input type="hidden" name="voting_result" value="{{ $nominees[0]['candidate'] }}">

            @else
                <div>Должны проголосовать {{ $alive }} игроков</div>
                <input type="hidden" name="alive" value="{{ $alive }}">
                <input type="hidden" name="max_idx" value="{{ count($nominees) }}">

            @php
                $idx = 1;
            @endphp
                @foreach($nominees as $nominee)

{{--                    <x-synchronized-input--}}
{{--                        name="candidate[{{$nominee}}][{{$idx}}]"--}}
{{--                        label="Против {{ $nominee }}"--}}
{{--                        value="{{ old('candidate.'.$nominee.'', 0) }}"--}}
{{--                        placeholder="0"--}}
{{--                        step="1"--}}
{{--                        max="10"--}}
{{--                        min="0"--}}
{{--                        onchange="updateNominee('candidate[{{$nominee}}]')"--}}
{{--                        type="number"--}}
{{--                        idx="{{$idx}}"--}}
{{--                    />--}}

                    <x-slot-selector
                        name="candidate[{{$nominee['candidate']}}][{{$idx}}]"
                        label="Против {{ $nominee['candidate'] }}"
                        selected-slot="{{ old('candidate.'.$nominee['candidate'].'', 0) }}"
                        callback="updateNominee"
                        :slot-availability="[
                                0 => true,
                                1 => true,
                                2 => true,
                                3 => true,
                                4 => (4 <= $alive),
                                5 => (5 <= $alive),
                                6 => (6 <= $alive),
                                7 => (7 <= $alive),
                                8 => (8 <= $alive),
                                9 => (9 <= $alive),
                                10 => (10 <= $alive)
                            ]"
                    />
                    @php
                        $idx++;
                    @endphp
                @endforeach



            @endif
{{--            @php--}}
{{--            dd($votedList);--}}
{{--            @endphp--}}
            <x-synchronized-input
                name="voted_list"
                label="Заголосовать игроков"
                value="{{ $votedList ? implode(', ', $votedList ?? []) : implode(', ', array_column($nominees ?? [], 'candidate')) }}"

            />
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="$refs.votingForm.requestSubmit()">Заголосовать</span>

                <span  class="btn ml-auto mr-auto"
                       x-data="{ isSubmitting: false }"
                      @click="
                      console.log(isSubmitting);
                             if (!isSubmitting) {
                                isSubmitting = true;
                                $refs.extraActionField.value = 'on';
                                $refs.votingForm.requestSubmit();
{{--                                setTimeout(() => isSubmitting = false, 2000);--}}
                            }
                          ">Ночь</span>

            </div>
        </div>
        </form>
    </div>
@endsection
