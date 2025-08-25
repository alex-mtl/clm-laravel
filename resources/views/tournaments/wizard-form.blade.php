@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
{{--        <div>Подать заявку?</div>--}}
        <form id="wizard-form" action="{{ route('tournaments.events.update', [$tournament]) }}" method="POST">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif

            @foreach($tournament->events as $event)
                @php
                    $day = $event->date_start->diffInDays($tournament->date_start) + 1;
                @endphp
                <div class="flex-row gap-1">
                    <input type="hidden" name="events[{{ $day }}][id]" value="{{ $event->id }}">
                    <x-synchronized-input
                        name="events[{{ $day }}][name]"
                        label="День"
                        value="{{ old('name', $event->name ?? '') }}"
                        placeholder="День"
                        :readonly="$mode === 'show'"
                    />
                    <x-synchronized-input
                        name="events[{{ $day }}][date_start]"
                        label="Дата"
                        type="date"
                        value="{{ old('date_start', $event->date_start_display ?? '') }}"
                        :readonly="true"
                    />
                    <x-synchronized-input
                        name="events[{{ $day }}][description]"
                        label="Описание"
                        value="{{ old('description', $event->description ?? '') }}"
                        placeholder="Дополнительная информация (опционально)"
                        :readonly="$mode === 'show'"
                    />

                    <x-synchronized-input
                        name="events[{{ $day }}][games_quota]"
                        label="Количество игр"
                        value="{{ old('games_quota', $event->games_quota ?? '') }}"
                        placeholder="4"
                        type="number"
                        :readonly="$mode === 'show'"
                    />

                </div>

            @endforeach

            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">

                <span class="btn"
                      x-data
                      @click="document.getElementById('wizard-form').submit()">Сгенерировать расписание</span>

            </div>
        </div>
    </div>
@endsection
