@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form id="request-type-form" action="{{ route('request-types.'.($mode==='create' ? 'store' : 'update'), [$requestType]) }}" method="POST">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif


            <div class="flex-row gap-1">
                <x-synchronized-input
                    name="name"
                    label="Название"
                    value="{{ old('name', $requestType->name) }}"
                    required
                    placeholder="Вступить в клуб"
                    :readonly="$mode === 'show'"
                />
                <x-synchronized-input
                    name="slug"
                    label="Код"
                    value="{{ old('slug', $requestType->slug) }}"
                    required
                    placeholder="club_join"
                    :readonly="$mode === 'show'"
                />
            </div>


            {{--        <input type="text" name="name" placeholder="City Name" required>--}}

            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">
                @if($mode === 'create' || $mode === 'edit')
                    <span class="btn"
                          x-data
                          @click="document.getElementById('request-type-form').submit()">Сохранить</span>

                @endif

                <span class="btn"
                      x-data
                      @click="window.location.href = '{{ route('request-types.index') }}'">Все типы</span>
            </div>
        </div>
    </div>
@endsection
