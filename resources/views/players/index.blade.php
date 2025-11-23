@extends('layouts.app')

@section('content')
    <div class="content-wrapper flex-row">
        <!-- Sidebar -->
        <div class="sidebar-sticky">
            @include('widgets.sidebar', [
                'menu' => $sidebarMenu,
            ])
        </div>
        <div class="content-main w-40">
            <!-- остальной контент без изменений -->
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
                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
                <div class="flex-column ">
                    @foreach($users as $user)
                        <div class="flex-row gap-1 border-bottom">
                            <div class="user-row w-15">
                                <div class="avatars-wrapper">
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
                                        @endif
                                    </div>
                                </div>
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
                            <div  class="w-5 center">{{ $user->games->count() }}</div>
                            <div  class="w-5 center">{{ $user->tournaments->count() }}</div>
                        </div>
                    @endforeach
                </div>

                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .content-wrapper {
            position: relative;
            min-height: 100vh;
            align-items: flex-start; /* Важно: выравнивание по верху */
        }

        .sidebar-sticky {
            position: sticky;
            top: 0;
            align-self: flex-start; /* Выравниваем по верху */
            height: 100vh;
            overflow-y: auto;
            z-index: 10;
            /* Если есть шапка, добавьте отступ: */
            /* top: 80px; */
            /* height: calc(100vh - 80px); */
        }

        .content-main {
            flex: 1;
            overflow-x: auto;
            min-height: 100vh;
        }

        .user-row {
            grid-template-columns: auto auto 1fr auto;
            align-items: center;
            gap: 0.5rem;
            padding: 0.2rem;
        }

        .user-row:last-child {
            border-bottom: none;
        }

        .avatars-wrapper {
            position: relative;
        }

        .user-avatar {
            position: relative;
            width: 5rem;
            height: 5rem;
        }

        .club-avatar {
            position: absolute;
            bottom: -0.3rem;
            right: -0.3rem;
            width: 2rem;
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

        /* Для мобильных устройств */
        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
            }

            .sidebar-sticky {
                position: relative;
                height: auto;
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush
