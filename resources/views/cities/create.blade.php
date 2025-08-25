@extends('layouts.app')

@section('content')
    <form action="{{ route('cities.store') }}" method="POST">
        @csrf
        <select name="country_id" required>
            @foreach($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
        </select>
        <input type="text" name="name" placeholder="City Name" required>
        <button type="submit">Save</button>
    </form>
@endsection
