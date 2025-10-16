@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main ">
    <form id="city-form" action="{{ route('cities.store') }}" method="POST">
        @csrf
{{--        <select name="country_id" required>--}}
{{--            @foreach($countries as $country)--}}
{{--                <option value="{{ $country->id }}">{{ $country->name }}</option>--}}
{{--            @endforeach--}}
{{--        </select>--}}

        <x-custom-dropdown
            name="country_id"
            :options="$countrySelector"
            selected="{{ old('country_id', $city->country_id ?? 1) }}"
            placeholder="USA"
            required
            :readonly="$mode === 'show'"
            label="Cтрана"
        />

        <x-synchronized-input
            name="name"
            label="Город"
            value="{{ old('name', $city->name) }}"
            required
            placeholder="USA"
        />
        <button class="hidden" type="submit">Save</button>
    </form>
    <div class="flex-row ">
        <div class="flex items-center ta-center">
            @if($mode === 'create' || $mode === 'edit')
                <span class="btn ml-auto mr-auto"
                      x-data
                      @click="document.getElementById('city-form').submit()">Сохранить</span>

            @endif
        </div>
    </div>
    </div>
@endsection
