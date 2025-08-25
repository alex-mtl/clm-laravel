@extends('layouts.app')

@section('content')
    <div class="content-main gap-1">
        <form id="club-form" action="{{ route('clubs.'.($mode==='create' ? 'store' : 'update'), $club) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($mode === 'create')
                @method('POST')
            @elseif($mode === 'edit')
                @method('PUT')
            @endif
            @if ($errors->any())
                <div class="flex-row alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="flex-row gap-2">

                <div class="flex-column gap-1">
                    @include('clubs.logo', ['club' => $club])

                    @if($mode !== 'show')
                        <div class="">
                            <x-avatar-upload
                                :initial-avatar=" asset('storage/' . $club->avatar)"
                                name="avatar"
                                target-selector=".club-avatar img"
                            />
                        </div>
                    @endif
                </div>
                <div class="flex-column gap-1 w-20">
                    <x-synchronized-input
                        name="name"
                        label="Название клуба"
                        value="{{ old('name', $club->name ?? '') }}"
                        placeholder="Клуб мафии"
                        required
                        :readonly="$mode === 'show'"
                    />


                    <x-synchronized-input
                        name="email"
                        type="email"
                        label="Электронная почта"
                        value="{{ old('email', $club->email ?? '') }}"
                        placeholder="john.doe@club.com"
                        required
                        :readonly="$mode === 'show'"
                    />


                    <div class="flex-row gap-1">
                        <x-custom-dropdown
                            name="country_id"
                            :options="$countrySelector"
                            selected="{{ old('country_id', $club->country_id ) }}"
                            placeholder="USA"
                            :readonly="$mode === 'show'"
                            label="Cтрана"
                        />

                        <x-custom-dropdown
                            name="city_id"
                            :options="$citySelector"
                            selected="{{ old('city_id', $club->city_id ) }}"
                            placeholder="New York"
                            :readonly="$mode === 'show'"
                            label="Город"
                        />
                    </div>
                </div>

            </div>
            <button type="submit" class="hidden">Сохранить</button>
        </form>
        <div class="flex-row ">
            <div class="flex items-center justify-between gap-2">
                @if($mode === 'create' || $mode === 'edit')
                    <span class="btn"
                          x-data
                          @click="document.getElementById('club-form').submit()">Сохранить</span>

                @endif

                @if($mode !== 'create')
                    <span class="btn"
                          x-data
                          @click="window.location.href = '{{ route('clubs.show', $club) }}' ">На страницу клуба</span>

                @endif
                <span class="btn"
                      x-data
                      @click="window.location.href = '{{ route('clubs.index') }}'">Все клубы</span>
            </div>
        </div>


    </div>

@endsection

