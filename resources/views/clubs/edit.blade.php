@extends('layouts.app')

@section('content')
    <h1>Edit Club</h1>

    <form action="{{ route('clubs.update', $club) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="{{ $club->name }}" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ $club->email }}" required>
        </div>
{{--        <div>--}}
{{--            <label for="owner_id">Owner:</label>--}}
{{--            <select name="owner_id" id="owner_id" required>--}}
{{--                @foreach($owners as $owner)--}}
{{--                    <option value="{{ $owner->id }}" {{ $club->owner_id == $owner->id ? 'selected' : '' }}>--}}
{{--                        {{ $owner->name }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
        <button type="submit" class="btn">Update Club</button>
    </form>
@endsection