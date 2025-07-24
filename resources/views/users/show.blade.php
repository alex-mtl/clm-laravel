@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
        <div class="p-8">
            <h1 class="text-2xl font-bold mb-6">User Details</h1>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ID</label>
                <p class="text-gray-900">{{ $user->id }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                <p class="text-gray-900">{{ $user->name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <p class="text-gray-900">{{ $user->email }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                <p class="text-gray-900">{{ $user->created_at->format('m/d/Y H:i') }}</p>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('users.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Back to list
                </a>
            </div>
        </div>
    </div>
@endsection