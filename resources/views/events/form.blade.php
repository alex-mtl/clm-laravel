@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form id="event-form" action="{{ route('clubs.events.'.($mode==='create' ? 'store' : 'update'), [$club, $event]) }}" method="POST">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif

            <x-synchronized-input
                name="name"
                label="Мероприятие"
                value="{{ old('name', $event->name) }}"
                required
                placeholder="Игровой вечер"
                :readonly="$mode === 'show'"
            />

            <div class="flex-row gap-1">
                <x-input-date
                    name="date_start"
                    label="Начало"
                    value="{{ old('date_start', $event->date_start_display ?? '') }}"
                    required
                    :readonly="$mode === 'show'"
                />

                <x-input-date
                    name="date_end"
                    label="Окончание"
                    value="{{ old('date_end', $event->date_end_display ?? '') }}"
                    required
                    :readonly="$mode === 'show'"
                />

            </div>
            <x-synchronized-input
                name="description"
                label="Описание"
                value="{{ old('description', $event->description) }}"
                placeholder="Дополнительная информация (опционально)"
                :readonly="$mode === 'show'"
            />





    {{--        'club_id',--}}
    {{--        'tournament_id',--}}
    {{--        'name',--}}
    {{--        'date',--}}
    {{--        'description',--}}
    {{--        'logo'--}}

            <x-custom-dropdown
                name="tournament_id"
{{--                :options="$club->tournaments()->pluck('name', 'id')"--}}
                :options="$tournaments"
                selected="{{ old('$tournament_id', $event->tournament_id ?? 'null') }}"
                placeholder=""
                label="Турнир"
            />

            {{--        <input type="text" name="name" placeholder="City Name" required>--}}

            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">
                @if($mode === 'create' || $mode === 'edit')
                    <span class="btn"
                          x-data
                          @click="document.getElementById('event-form').submit()">Сохранить</span>

                @endif

                @if($mode !== 'create')
                    <span class="btn"
                          x-data
                          @click="window.location.href = '{{ route('clubs.show', $club) }}' ">На страницу клуба</span>

                @endif
                <span class="btn"
                      x-data
                      @click="window.location.href = '{{ route('clubs.show', $club) }}?tab=events'">Список мероприятий</span>
            </div>
        </div>
    </div>
@endsection
