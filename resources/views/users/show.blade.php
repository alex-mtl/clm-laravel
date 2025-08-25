@extends($layout ?: 'layouts.dashboard')

@section('content')
    <div class="content-main gap-1">
        <form id="user-form" action="{{ route('users.'.($mode==='create' ? 'store' : 'update'), $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif
            <div class="flex-start gap-2 ">

                <div class="user-avatar-area flex-column gap-1">
                    <div class="flex-column gap-1">
                        @include('users.avatar', ['user' => $user])

                        @if($mode !== 'show')
                            <div class="center ">
                                <x-avatar-upload
                                    :initial-avatar=" asset('storage/' . $user->avatar)"
                                    name="avatar"
                                />
                            </div>
                        @endif
                    </div>
                </div>
                <div class="user-main-info">
                    <div class="flex-row">
                        <x-synchronized-input
                            name="first_name"
                            label="Имя"
                            value="{{ old('first_name', $user->first_name ?? '') }}"
                            placeholder="Имя"
                            :readonly="$mode === 'show'"
                        />
                        <x-synchronized-input
                            name="last_name"
                            label="Фамилия"
                            value="{{ old('last_name', $user->last_name ?? '') }}"
                            placeholder="Фамилия"
                            :readonly="$mode === 'show'"
                        />
                    </div>
                    <div class="flex-row">
                        <x-synchronized-input
                            name="name"
                            label="Никнейм"
                            value="{{ old('name', $user->name ?? '') }}"
                            placeholder="Выберите никнейм"
                            required
                            :readonly="$mode === 'show'"
                        />

                        <x-custom-dropdown
                            name="club_id"
                            :options="$clubSelector"
                            selected="{{ old('club_id', $user->club_id ?? 0) }}"
                            placeholder="<empty>"
                            label="Клуб"
                            :readonly="$mode === 'show'"
                        />
                    </div>

                    <x-synchronized-input
                        name="email"
                        type="email"
                        label="Электронная почта"
                        value="{{ old('email', $user->email ?? '') }}"
                        placeholder="john.doe@example.com"
                        required
                        :readonly="$mode === 'show'"
                    />

                    <div class="flex-row">
                        <x-custom-dropdown
                            name="country_id"
                            :options="$countrySelector"
                            selected="{{ old('country_id', $user->country_id ?? 1) }}"
                            placeholder="USA"
                            :readonly="$mode === 'show'"
                            label="Cтрана"
                        />

                        <x-custom-dropdown
                            name="city_id"
                            :options="$citySelector"
                            selected="{{ old('city_id', $user->city_id ?? 2) }}"
                            placeholder="New York"
                            :readonly="$mode === 'show'"
                            label="Город"
                        />
                    </div>

                    @if($mode === 'create')
                        <div class="flex-row">
                            <x-synchronized-input
                                name="password"
                                label="Пароль"
                                value=""
                                placeholder="p@ssw0rd123!"
                                type="password"
                                required
                            />

                            <x-synchronized-input
                                name="password_confirmation"
                                label="Подтверждение"
                                value=""
                                placeholder="p@ssw0rd123!"
                                type="password"
                                required
                            />

                        </div>
                    @endif



                </div>

{{--            </form>--}}
            </div>
            <button type="submit" class="hidden">Сохранить</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">
                @if($mode === 'create' || $mode === 'edit')
                    {{--                        <a href="{{ route('users.store', $user) }}" class="">--}}
                    {{--                            Edit--}}
                    {{--                        </a>--}}
                    {{--                    @elseif($mode === 'edit')--}}
                    <span class="btn"
                          x-data
                          @click="document.getElementById('user-form').submit()">Сохранить</span>
                    {{--                        <a href="{{ route('users.store', $user) }}" class="">--}}
                    {{--                            Edit--}}
                    {{--                        </a>--}}
                @endif
                <span class="btn"
                      x-data
                      @click="window.location.href = '{{ ($userListLink) ?? route('users.index') }}'">Все пользователи</span>
                {{--                    <a href="{{ route('users.index') }}" class="">--}}
                {{--                        Back to list--}}
                {{--                    </a>--}}
            </div>
        </div>


    </div>



@endsection
