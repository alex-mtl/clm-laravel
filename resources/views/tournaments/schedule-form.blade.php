@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form id="wizard-form" action="{{ route('tournaments.events.update', [$tournament]) }}" method="POST">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif

            @foreach($schedule as $r => $round)
                <div class="flex-row ta-center w100 mb-1">
                    <span class="w100">Раунд {{ $r }}</span>
                </div>
                <div class="flex-row gap-1">

                    @foreach($round as $t => $participants)
                        <div class="flex-column">
                            @foreach($participants as $slot => $participant)
                                <div class="flex-row gap-05">
                                    <span class="w-1"> {{ $slot+1 }}</span>
                                    <span class="w-10">
                                    @include('widgets.user-autocomplete', [
                                        'name' => "round[{$r}][table][{$t}][slot][{$slot}]",
                                        'label' => '',
                                        'selected' => ['id' => $participant['id']?? null, 'name' => $participant['name']?? null],
                                        'searchUrl' => route('users.search'),
                                    //                            'placeholder' => 'Type to search users...'
                                        'placeholder' => '...'
                                    ])
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">

                <span class="btn"
                      x-data
                      @click="document.getElementById('wizard-form').submit()">Сгенерировать расписание</span>

            </div>
        </div>
    </div>

    <script>
        const schedule = @json($schedule);
        const stats = @json($stats);
        console.log(schedule, stats);
    </script>
@endsection
