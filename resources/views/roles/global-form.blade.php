@extends($layout ?: 'layouts.dashboard')

@section('content')
    <div class="content-main">

        <form id="assign-role-form" action="{{ route('users.roles.store', [$user]) }}" method="POST">
            @csrf

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
        <div class="flex-row">
            <div class="flex items-center justify-between gap-2">
                <span class="btn" x-data
                      @click="
                        document.getElementById('assign-role-form').submit();
                    ">
                    Назначить роль
                </span>

                <span class="btn danger" x-data
                      @click="
                        document.getElementById('assign-role-form').action = '{{ route('users.roles.retract', [$user]) }}';
                        document.getElementById('assign-role-form').submit();
                    ">
                    Отозвать роль
                </span>
            </div>
        </div>
    </div>
@endsection
