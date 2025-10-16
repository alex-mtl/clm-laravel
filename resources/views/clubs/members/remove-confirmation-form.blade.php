@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <div>Исключить участника: {{ $user->name }} ?</div>
        <form id="clubs-members-remove-form" action="{{ route('clubs.members.destroy', [$club, $user]) }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="club_id" value="{{ $club->id }}">
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">

                <span class="btn"
                      x-data
                      @click="document.getElementById('clubs-members-remove-form').submit()">Исключить</span>

            </div>
        </div>
    </div>
@endsection
