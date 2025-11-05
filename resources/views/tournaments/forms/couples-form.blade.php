@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form id="couples-form" action="{{ route('tournaments.couples.store', [$tournament]) }}" method="POST">
            @csrf
            <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">
            @php
                $i=1;
            @endphp
            @foreach($couples as $couple)
                <div class="flex-row gap-05">
                    @include('widgets.user-autocomplete', [
                        'name' => "couples[{$i}][user1]",
                        'label' => '',
                        'selected' => ['id' => $couple->user1->id ?? null, 'name' => $couple->user1->name ?? null],
                        'searchUrl' => route('users.search'),
                        'placeholder' => '...'
                    ])

                    @include('widgets.user-autocomplete', [
                        'name' => "couples[{$i}][user2]",
                        'label' => '',
                        'selected' => ['id' => $couple->user2->id ?? null, 'name' => $couple->user2->name ?? null],
                        'searchUrl' => route('users.search'),
                        'placeholder' => '...'
                    ])

                    @include('widgets.inline-btn', [
                        'title' => 'Удалить',
                        'icon' => 'cancel',
                        'class' => 'inline-btn',
                        'endpoint' => 'deleteCouple(this)'
                    ])
                </div>
                @php
                    $i++;
                @endphp
            @endforeach
            <div class="flex-row gap-05">
                @include('widgets.user-autocomplete', [
                   'name' => "couples[{$i}][user1]",
                   'label' => '',
                   'selected' => ['id' => null, 'name' =>  null],
                   'searchUrl' => route('users.search'),
                   'placeholder' => '...'
               ])

                @include('widgets.user-autocomplete', [
                    'name' => "couples[{$i}][user2]",
                    'label' => '',
                    'selected' => ['id' =>  null, 'name' =>  null],
                    'searchUrl' => route('users.search'),
                    'placeholder' => '...'
                ])

                @include('widgets.inline-btn', [
                    'title' => 'Удалить',
                    'icon' => 'cancel',
                    'class' => 'inline-btn',
                    'endpoint' => 'deleteCouple(this)'
                ])
            </div>

            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">
                @include('widgets.inline-btn', [
                    'title' => 'Добавить пару',
                    'icon' => 'add_circle',
                    'class' => 'inline-btn',
                    'endpoint' => 'addCouple()'
                ])
                <span class="btn"
                      x-data
                      @click="document.getElementById('couples-form').submit()">Сохранить</span>

            </div>
        </div>
    </div>
@endsection
