<div class="data-wrapper {{ session('tab') === 'requests' ? session('tab') : 'hidden' }}"  id="tournament-requests-data" >

{{--    @if($tournament->getUserStatus(auth()->user()->id) === 'not_joined')--}}
        @include('widgets.list-title', [
            'title' => 'Запросы на участие',
             'resource' => 'tournaments.requests',
             'resourceItem' => $tournament,
             'endpoint' => route('tournaments.requests.create', $tournament),
             'ajax' => true
        ])
{{--    @endif--}}

        <div class="data ">

            @foreach($tournament->joinRequests()->where('status', 'pending')->orderBy('created_at', 'desc')->get() as $req)
                <div class="user-row space-between">
                    <div class="flex-row gap-1">
                        <div class="rectangle-group">
                            @if($req->applicant->avatar)
                                <div class="user-avatar">
                                    <a href="/players/{{ $req->applicant->id }}" >
                                        <img src="{{ asset('storage/' . $req->applicant->avatar) }}">
                                    </a>
                                </div>
                            @else
                                <div class="user-avatar">
                                    <a href="/players/{{ $req->applicant->id }}" >
                                        <img src="/img/no-avatar.svg">
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="user-details">
                            <div class="div23">{{ $req->applicant->name }}</div>
                            <div class="div24">Рейтинг: {{ $req->applicant->rating ?? 0}} • Игр: {{ $req->applicant->games()->count() ?? 0 }}</div>
    {{--                        • Рег: {{ $req->user->created_at->format('Y.m.d')  }}--}}
                        </div>
                    </div>
                    <div>
                        <span x-data="{ localTime: new Date('{{ $req->created_at->toIso8601String() }}').toLocaleString() }"
                              x-text="localTime"></span>
                    </div>
                    @can('manage_tournament', $tournament)
                        <div class="request-actions material-symbols-outlined">
                            @if($req->status === 'pending')
                                <form method="POST" class="hidden" id="approve-request-{{ $req->id }}" action="{{ route('tournaments.requests.approve', [$tournament, $req]) }}">
                                    @csrf
                                    <button type="submit"></button>
                                </form>
                                <div
                                    x-data
                                    @click="document.getElementById('approve-request-{{ $req->id }}').submit()"
                                    class="action-btn dark success"
                                    title="Принять"
                                >
                                    <span>check_circle</span>
                                </div>

                                <form method="POST" class="hidden" id="decline-request-{{ $req->id }}" action="{{ route('tournaments.requests.decline', [$tournament, $req]) }}">
                                    @csrf
                                    <button type="submit" >
                                    </button>
                                </form>
                                <div
                                    x-data
                                    @click="document.getElementById('decline-request-{{ $req->id }}').submit()"
                                    class="action-btn dark"
                                    title="Отклонить"
                                >
                                    <span>block</span>
                                </div>
    {{--                            <div class=" action-btn dark success "><span>check_circle</span></div>--}}

                            @endif
                        </div>
                    @endcan

                </div>
            @endforeach
        </div>

</div>

