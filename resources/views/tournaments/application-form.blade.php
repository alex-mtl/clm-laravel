@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <div>Подать заявку?</div>
        <form id="tournament-application-form" action="{{ route('tournaments.requests.store', [$tournament]) }}" method="POST">
            @csrf
            <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">

            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">

                <span class="btn"
                      x-data
                      @click="document.getElementById('tournament-application-form').submit()">Зарегистрироваться</span>

            </div>
        </div>
    </div>
@endsection
