@extends('layouts.app')

@section('content')
        <div class="content-main w-40">
            <div class="content-table">
                <div class="table-head flex-row gap-1 border-bottom">
                    <div class="col-1 w-15">Игрок</div>
                    @foreach ($cols as $idx => $col)
                        @if($col->html ?? false)
                            <div class="col-{{ $idx }} {{ $col->class ?? 'w-10' }}" title="{{ $col->name ?? '' }}">{!! $col->html !!} </div>
                        @else
                            <div class="col-{{ $idx }} {{ $col->class ?? 'w-10' }}">{{ $col->name ?? '' }}</div>
                        @endif

                    @endforeach
                    <div  class="w-5 center">Игры</div>
                    <div  class="w-5 center">Турниры</div>
                </div>
                <div class="flex-column ">
                    @foreach($users as $user)
                        <div class="flex-row gap-1 border-bottom">
                        <div class="user-row w-15">
                            <div class="avatars-wrapper">
                            <!-- Column 1: User Avatar -->
                                <div class="user-avatar">
                                    <a href="{{ route('players.show', $user->id) }}">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/'.$user->avatar) }}"
                                                 alt="{{ $user->name }}"

                                                 class="rounded-full h-16 w-16 object-cover"
                                                 onerror="this.onerror=null; this.src='/img/no-avatar.svg';"
                                            >
                                        @else
                                            <img src="/img/no-avatar.svg"
                                                 alt="{{ $user->name }}"
                                                 class="rounded-full h-16 w-16 object-cover">
                                        @endif
                                    </a>
                                </div>


                                <!-- Column 2: Club Avatar -->
                                <div class="club-avatar ml-4">
                                    @if($user->club)
                                        <a href="{{ route('clubs.show', $user->club->id) }}">
                                            @if($user->club->avatar)
                                                <img src="{{ asset('storage/'.$user->club->avatar) }}"
                                                     alt="{{ $user->club->name }}"
                                                     class="rounded-full h-16 w-16 object-cover border border-gray-600">
                                            @else
                                                <img src="/img/no-club.svg"
                                                     alt="{{ $user->club->name }}"
                                                     class="rounded-full h-16 w-16 object-cover border border-gray-600">
                                            @endif
                                        </a>
                                    @else
                                        {{--                                    <div class="rounded-full h-16 w-16 bg-gray-800 flex items-center justify-center text-gray-500 border border-dashed border-gray-600">--}}
                                        {{--                                        <span class="material-symbols-outlined">group</span>--}}
                                        {{--                                    </div>--}}
                                    @endif
                                </div>
                            </div>
                            <!-- Column 3: User Details -->
                            <div class="user-details ml-4 flex-1">
                                <div class="flex-column items-center">
                                    <a href="{{ route('players.show', $user->id) }}" class="">
                                        {{ $user->name }}
                                    </a>
                                    @if($user->first_name || $user->last_name)
                                        <span class="text-xs">
                                            {{ trim($user->first_name.' '.$user->last_name) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs">
                                    <span>{{ $user->country->name ?? '???'}}</span>
                                    <span class="text-gray-500">•</span>
                                    <span>{{ $user->city->name ?? '???'}}</span>
                                </div>
                            </div>

                        </div>

                        @foreach ($cols as $idx => $col)
                            <div  class="{{ $col->class ?? 'w-10' }}">{{ data_get($user, $col->prop, $col->default ?? '') }}</div>
                        @endforeach
                            <div  class="w-5 center">{{ $user->glob_games ?? 0 }}</div>
                            <div  class="w-5 center">{{ $user->glob_tournaments ?? 0 }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif

        </div>
@endsection

@push('styles')
    <style>
        .user-row {
            /*display: grid;*/
            grid-template-columns: auto auto 1fr auto;
            align-items: center;
            gap: 0.5rem;
            padding: 0.2rem;
            /*border-bottom: 1px solid var(--clm-content-border);*/
        }

        .user-row:last-child {
            border-bottom: none;
        }
        .avatars-wrapper {
            position: relative;
        }

        .user-avatar {
            position: relative;
            width:5rem;,
            height: 5rem;
        }

        .club-avatar {
            position: absolute;
            bottom:-0.3rem;
            right:-0.3rem;
            width:2rem;,
            height: 2rem;

        }
        .club-avatar img {
            border-radius: 0.5rem;
        }

        .user-avatar img, .club-avatar img {
            transition: transform 0.2s ease;
            height: 100%;
            width: 100%;
        }

        .user-avatar:hover img, .club-avatar:hover img {
            transform: scale(1.05);
        }
    </style>
@endpush
