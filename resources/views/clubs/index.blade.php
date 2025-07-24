@extends('layouts.app')

@section('content')
    <h1>Clubs</h1>
    <a href="{{ route('clubs.create') }}" class="btn">Create New Club</a>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Owner</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($clubs as $club)
            <tr>
                <td>{{ $club->id }}</td>
                <td>{{ $club->name }}</td>
                <td>{{ $club->email }}</td>
                <td>{{ $club->owner->name }}</td>
                <td>
                    <a href="{{ route('clubs.show', $club) }}" class="btn">View</a>
                    @if($club->owner_id == auth()->user()->id)
                        <a href="{{ route('clubs.edit', $club) }}" class="btn">Edit</a>
                        <form action="{{ route('clubs.destroy', $club) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    @endif

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $clubs->links() }}
@endsection