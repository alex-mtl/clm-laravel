@extends('layouts.app')

@section('content')

    <h1>Create New Club</h1>

    <form action="{{ route('clubs.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" >
        </div>
{{--        <div>--}}
{{--            <label for="owner_id">Owner:</label>--}}
{{--            <select name="owner_id" id="owner_id" required>--}}
{{--                @foreach($owners as $owner)--}}
{{--                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
        <button type="submit" class="btn">Create Club</button>
    </form>
@endsection
