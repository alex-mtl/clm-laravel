@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <div>Вы уверены что хотите удалить Судью?</div>
        <div>{{ $judge->name }}</div>
        <form class="flex-column w-15" x-data="" x-ref="deleteJudgeForm" id="delete-judge-form" action="{{ route('tournaments.judges.delete', [$tournament->id,$judge->id]) }}" method="POST">
            @csrf

            <button class="hidden" type="submit">Save</button>


            <div class="flex-row ">
                <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="$refs.deleteJudgeForm.requestSubmit()">Удалить</span>

                </div>
            </div>
        </form>
    </div>
@endsection
