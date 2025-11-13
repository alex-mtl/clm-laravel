@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        @can('manage_tournament', $tournament)
{{--        <div>Подать заявку?</div>--}}
            <form id="judge-form" action="{{ route('tournaments.judges.store', [$tournament]) }}" method="POST">
                @csrf
                <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">

                <x-custom-dropdown
                    name="user_id"
                    class="w-10"
                    :options="$judges"
                    selected="{{ old('user_id', $judge->user_id ) }}"
                    placeholder="Select..."
                    :readonly="$mode === 'show'"
                    label="Судья"


                />

                <x-custom-dropdown
                    name="type"
                    class="w-10"
                    :options="$judgeTypes"
                    selected="{{ old('type', $judge->type ) }}"
                    placeholder="Select..."
                    :readonly="$mode === 'show'"
                    label="Тип (главный/судья/боковой)"


                />

                <button class="hidden" type="submit">Save</button>
            </form>
            <div class="flex-row ">
                <div class="flex items-center justify-between gap-2">

                    <span class="btn"
                          x-data
                          @click="document.getElementById('judge-form').submit()">Одобрить</span>

                </div>
            </div>
        @else
            <div>У вас нет прав для добавления судьи в этот турнир.</div>
        @endcan
    </div>
@endsection
