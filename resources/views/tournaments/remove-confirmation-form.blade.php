@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        @can('manage_tournament', $tournament)
            <div>Исключить участника: {{ $user->name }} ?</div>
            <form id="tournament-confirmation-form" action="{{ route('tournaments.partcipants.remove', [$tournament, $user]) }}" method="POST">
                @csrf
                <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <button class="hidden" type="submit">Save</button>
            </form>
            <div class="flex-row ">
                <div class="flex items-center justify-between gap-2">

                    <span class="btn"
                          x-data
                          @click="document.getElementById('tournament-confirmation-form').submit()">Исключить</span>

                </div>
            </div>
        @else
            <div>У вас нет прав для исключения участника из этого турнира.</div>
        @endcan
    </div>
@endsection
