@extends('layouts.app')

@section('content')
<div class="content-main">
    <x-synchronized-input
        name="country"
        label="Cтрана"
        value="{{ $city->country->name ?? '' }}"
        readonly

    />


{{--        <input type="text" name="name" placeholder="City Name" required>--}}
        <x-synchronized-input
            name="name"
            label="Город"
            value="{{ $city->name }}"
            readonly

        />
        <div class="btn" onclick="window.location.href = '{{ route('cities.index') }}';" title="Назад">
            <span>Назад</span>
        </div>

</div>
@endsection
