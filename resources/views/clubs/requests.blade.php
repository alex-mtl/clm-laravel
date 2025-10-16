<div class="data-wrapper hidden"  id="club-requests-data" >
    @can('manage_club', $club)
        <div class="data ">

            @foreach($club->joinRequests()->where('status', 'pending')->orderBy('created_at', 'desc')->get() as $req)
                <div class="user-row">
                    <div class="rectangle-group">
                        @if($req->user->avatar)
                            <div class="user-avatar">
                                <a href="/users/{{ $req->user->id }}" >
                                    <img src="{{ asset('storage/' . $req->user->avatar) }}">
                                </a>
                            </div>
                        @else
                            <div class="user-avatar">
                                <a href="/users/{{ $req->user->id }}" >
                                    <img src="/img/no-avatar.svg">
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="user-details">
                        <div class="div23">{{ $req->user->name }}</div>
                        <div class="div24">Рейтинг: {{ $req->user->rating ?? 0}} • Игр: {{ $req->user->games ?? 0 }}</div>
{{--                        • Рег: {{ $req->user->created_at->format('Y.m.d')  }}--}}
                    </div>
                    <div>
                        <span x-data="{ localTime: new Date('{{ $req->created_at->toIso8601String() }}').toLocaleString() }"
                              x-text="localTime"></span>
                    </div>
                    <div class="request-actions material-symbols-outlined">
                        @if($req->status === 'pending')
                            <form method="POST" class="hidden" id="approve-request-{{ $req->id }}" action="{{ route('join-requests.approve', $req) }}">
                                @csrf
                                <button type="submit"></button>
                            </form>
                            <div
                                x-data
                                @click="document.getElementById('approve-request-{{ $req->id }}').submit()"
                                class="action-btn dark success"
                            >
                                <span>check_circle</span>
                            </div>

                            <form method="POST" class="hidden" id="decline-request-{{ $req->id }}" action="{{ route('join-requests.decline', $req) }}">
                                @csrf
                                <button type="submit" >
                                </button>
                            </form>
                            <div
                                x-data
                                @click="document.getElementById('decline-request-{{ $req->id }}').submit()"
                                class="action-btn dark"
                            >
                                <span>block</span>
                            </div>
{{--                            <div class=" action-btn dark success "><span>check_circle</span></div>--}}

                        @endif
                    </div>
                    <div class="request-status material-symbols-outlined {{ $req->status }}">
                        @if($req->status === 'approved')
                            <span>person_check</span>
                        @elseif($req->status === 'rejected')
                            <span>person_cancel</span>
                        @elseif($req->status === 'pending')
                            <span>person_add</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endcan
</div>

{{--@if (auth()->id() === $club->owner_id)--}}
{{--    <div class="frame-wrapper4">--}}
{{--        <h3>Запросы на вступление</h3>--}}
{{--        @foreach($club->joinRequests()->pending()->get() as $request)--}}
{{--            <div>--}}
{{--                {{ $request->user->name }} wants to join--}}
{{--                <form method="POST" class="hidden" id="approve-request-{{ $request->id }}" action="{{ route('join-requests.approve', $request) }}">--}}
{{--                    @csrf--}}
{{--                    <button type="submit">Approve</button>--}}
{{--                </form>--}}
{{--            </div>--}}


{{--            <form method="POST" action="{{ route('join-requests.decline', $request) }}">--}}
{{--                @csrf--}}
{{--                <button type="submit" class="btn">--}}
{{--                    Decline Request--}}
{{--                </button>--}}
{{--            </form>--}}

{{--        @endforeach--}}
{{--    </div>--}}
{{--@endif--}}
