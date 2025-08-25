
@extends('layouts.app')

@section('content')
    <div class="content-main ">
        <form
            id="role-form"
            class="flex-column "
            action="{{ route('clubs.roles.'.($mode==='create' ? 'store' : 'update'), [$club, $role]) }}"
            method="POST">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif

            <x-synchronized-input
                name="name"
                label="Роль"
                value="{{ old('name', $role->name) }}"
                required
                :readonly="$mode === 'show'"
                placeholder="Игрок"
            />
            <x-synchronized-input
                name="slug"
                label="Код"
                value="{{ old('slug', $role->slug) }}"
                required
                :readonly="$mode === 'show'"
                placeholder="club_player"
            />
            <x-synchronized-input
                name="description"
                label="Описание"
                value="{{ old('description', $role->description) }}"
                :readonly="$mode === 'show'"
                placeholder="Ограниченный доступ"
            />

            <div class="flex-row mt-1">
                <div class="flex items-center justify-between gap-2">
                    @if($mode === 'create' || $mode === 'edit')
                        <span class="btn"
                              x-data
                              @click="document.getElementById('role-form').submit()">Сохранить</span>
                    @endif
                    <span class="btn"
                          x-data
                          @click="window.location.href = '{{ route('clubs.roles.index', $club) }}'">Все роли</span>

                </div>
            </div>
        </form>
    </div>
@endsection
