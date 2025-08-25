@extends('layouts.app')

@section('content')
    <form action="{{ route('cities.update', $city->id) }}" method="POST">
        @csrf
        @method('PUT')
{{--        <select name="country_id" required>--}}
{{--            @foreach($countries as $country)--}}
{{--                <option value="{{ $country->id }}">{{ $country->name }}</option>--}}
{{--            @endforeach--}}
{{--        </select>--}}

        <x-custom-dropdown
            name="country_id"
            :options="$countries->pluck('name', 'id')"
            selected="{{ $city->country_id ?? 1}}"
            placeholder="USA"
            required
            label="Cтрана"
        />

{{--        <input type="text" name="name" placeholder="City Name" required>--}}
        <x-synchronized-input
            name="name"
            label="Город"
            value="{{ old('name', $city->name) }}"
            required
            placeholder="New York"
        />
        <button type="submit">Save</button>
    </form>
@endsection
