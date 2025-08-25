@extends('layouts.app')

@section('content')
    <div class="content-main">
        <form action="{{ route('countries.update', $country) }}" method="POST">
            @csrf
            @method('PUT') <!-- Add this for update requests -->

            <x-synchronized-input
                name="name"
                label="Cтрана"
                value="{{ old('name', $country->name) }}"
            required
            placeholder="USA"
            />

            <x-synchronized-input
                name="code"
                label="Код"
                value="{{ old('code', $country->code) }}"
            required
            maxlength="3"
            placeholder="usa"
            />

            <button type="submit">Update</button>
        </form>
    </div>
@endsection
