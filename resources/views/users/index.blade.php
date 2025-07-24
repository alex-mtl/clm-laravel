@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Users</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create New User
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-max w-full table-auto">
            <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">ID</th>
                <th class="py-3 px-6 text-left">Name</th>
                <th class="py-3 px-6 text-left">Email</th>
                <th class="py-3 px-6 text-center">Actions</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
            @foreach($users as $user)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $user->id }}</td>
                    <td class="py-3 px-6 text-left">{{ $user->name }}</td>
                    <td class="py-3 px-6 text-left">{{ $user->email }}</td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center">
                            <a href="{{ route('users.show', $user) }}" class="btn">
                                View
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="btn">
                                Edit
                            </a>
                            <a class="btn" >Delete
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="" onclick="return confirm('Are you sure?')">

                                </button>
                            </form>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
@endsection