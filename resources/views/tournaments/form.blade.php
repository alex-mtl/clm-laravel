@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form id="tournament-form"
              action="{{ route('clubs.tournaments.'.($mode==='create' ? 'store' : 'update'), [$club, $tournament]) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="flex-row alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="flex-start gap-2">

                <div class="flex-column gap-1">
                    @include('tournaments.logo', ['tournament' => $tournament])


                </div>
                <div class="flex-column gap-1 w-30">

                    <x-synchronized-input
                        name="name"
                        label="Турнир"
                        value="{{ old('name', $tournament->name) }}"
                        required
                        placeholder="Big Apple Cup"
                        :readonly="$mode === 'show'"
                    />

                    <x-synchronized-input
                        name="location"
                        label="Локация (адрес)"
                        value="{{ old('location', $tournament->location) }}"
                        placeholder=""
                        :readonly="$mode === 'show'"
                    />

                    <div class="flex-row gap-1">
                        <x-synchronized-input
                            name="players_quota"
                            label="Количество участников"
                            value="{{ old('players_quota', $tournament->players_quota) }}"
                            placeholder=""
                            :readonly="$mode === 'show'"
                        />

                        <x-synchronized-input
                            name="games_quota"
                            label="Количество игр"
                            value="{{ old('games_quota', $tournament->games_quota) }}"
                            placeholder=""
                            :readonly="$mode === 'show'"
                        />
                    </div>

                    {{--            <x-synchronized-input--}}
                    {{--                name="description"--}}
                    {{--                label="Описание"--}}
                    {{--                value="{{ old('description', $tournament->description) }}"--}}
                    {{--                placeholder="Дополнительная информация (опционально)"--}}
                    {{--                :readonly="$mode === 'show'"--}}
                    {{--            />--}}

                    <div class="flex-row gap-1">
                        <x-input-date
                            name="date_start"
                            label="С"
                            value="{{ old('date_start', $tournament->date_start_display ?? '') }}"
                            required
                            :readonly="$mode === 'show'"
                        />

                        <x-input-date
                            name="date_end"
                            label="По"
                            value="{{ old('date_end', $tournament->date_end_display ?? '') }}"
                            required
                            :readonly="$mode === 'show'"
                        />
                    </div>

                    <x-synchronized-input
                        name="participation_fee"
                        label="Стоимость участия"
                        value="{{ old('participation_fee', $tournament->participation_fee) }}"
                        placeholder="200"
                        :readonly="$mode === 'show'"
                    />

                    <x-custom-dropdown
                        name="phase"
                        :options="$tournamentPhases"
                        selected="{{ old('phase', $tournament->phase ?? 'draft') }}"
                        placeholder="<empty>"
                        label="Этап"
                        :readonly="$mode === 'show'"
                    />
                    {{--        <input type="text" name="name" placeholder="City Name" required>--}}

                    <button class="hidden" type="submit">Save</button>
                </div>
            </div>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">
                @if($mode === 'create' || $mode === 'edit')
                    <span class="btn"
                          x-data
                          @click="document.getElementById('tournament-form').submit()">Сохранить</span>

                @endif

                @if($mode !== 'create')
                    <span class="btn"
                          x-data
                          @click="window.location.href = '{{ route('clubs.show', $club) }}' ">На страницу клуба</span>

                @endif
                <span class="btn"
                      x-data
                      @click="window.location.href = '{{ route('clubs.show', $club) }}?tab=tournaments'">Список турниров</span>
            </div>
        </div>
    </div>
@endsection
<style>
    .tournament-logo {
        margin-left: auto;
        margin-right: auto;
        position: relative;
    }

    .tournament-logo  img{
        height: 10rem;
        width: 10rem;
        object-fit: contain;
        min-width: 0;
        border-radius: 1rem;
        border: none;
        /*margin: 0.2rem;*/

    }




    .tournament-banner  img{
        height: 10rem;
        width: 30rem;
        object-fit: contain;
        min-width: 0;
        border-radius: 1rem;
        border: none;
        margin: 0.2rem;
    }
    </style>
