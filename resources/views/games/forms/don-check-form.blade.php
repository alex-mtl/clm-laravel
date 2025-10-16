@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form class="flex-column w-20 gap-1" x-data="" x-ref="donCheckForm" id="don-check-form" action="{{ route('games.donCheck', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>

            <x-custom-dropdown
                name="donCheckDay"
                :options="$dayOptions"
                selected="{{ old('donCheckDay', $donCheckDay ) }}"
                label="Ночь"
            />

            <div>Дон проверяет</div>
            <div>Кто шериф?</div>

            <x-synchronized-input
                name="donCheck"
                label="Проверить"
                value="{{ old('donCheck', $donCheck) }}"
                placeholder="0"
                step="1"
                max="10"
                min="0"
                type="number"
            />

        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="$refs.donCheckForm.requestSubmit()">Продолжить</span>

            </div>
        </div>
        </form>
    </div>
@endsection
