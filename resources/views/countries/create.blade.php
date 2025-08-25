
@extends('layouts.app')

@section('content')
    <div class="content-main ">
        <form action="{{ route('countries.store') }}" method="POST">
            @csrf
            <x-synchronized-input
                name="name"
                label="Cтрана"
                value="{{ old('country', '') }}"
                required
                placeholder="USA"
            />
            <x-synchronized-input
                name="code"
                label="Код"
                value="{{ old('code', '') }}"
                required
                maxlength="3"
                placeholder="usa"
            />
            <button type="submit">Save</button>
        </form>
    </div>
@endsection
