@extends($layout ?: 'layouts.dashboard')

@section('content')
    <div class="content-main">

        <form id="assign-role-form" class="w-20" action="{{ route('users.club-roles.store', [$user, $club]) }}" method="POST">
            @csrf
{{--            <input type="hidden" name="user_id" value="{{ $user->id }}">--}}
{{--            <input type="hidden" name="club_id" value="{{ $club->id }}">--}}
            @include('widgets.prop-line', [
                'label' => 'Клуб',
                'value' => $club->name,
            ])
            @include('widgets.prop-line', [
                'label' => 'Пользователь',
                'value' => $user->name,
            ])
{{--            Пользователь: {{ $user->name }}</div>--}}
            <x-custom-dropdown
                name="role_id"
                :options="$roles"
                selected="{{ old('role_id', $role->id ) }}"
                placeholder="Select..."
                :readonly="$mode === 'show'"
                label="Роль"
            />

            <button class="hidden" type="submit">Save</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">

                <span class="btn"
                      x-data
                      @click="document.getElementById('assign-role-form').submit()">Сохранить</span>

            </div>
        </div>
    </div>
@endsection
