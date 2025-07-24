@extends('layouts.app')

@section('content')
    <h1>Club Details</h1>

    <div>
        <strong>ID:</strong> {{ $club->id }}
    </div>
    <div>
        <strong>Name:</strong> {{ $club->name }}
    </div>
    <div>
        <strong>Email:</strong> {{ $club->email }}
    </div>
    <div>
        <strong>Owner:</strong> {{ $club->owner->name }}
    </div>
    <div>
        <strong>Created At:</strong> {{ $club->created_at }}
    </div>

    <a href="{{ route('clubs.edit', $club) }}" class="btn">Edit</a>
    <a href="{{ route('clubs.index') }}" class="btn">Back to list</a>
@endsection