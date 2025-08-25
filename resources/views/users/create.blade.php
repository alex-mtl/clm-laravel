@extends('layouts.app')

@section('content')

    <div class="">
        <div class="p-8">
            <h1 class="text-2xl font-bold mb-6">Create New User</h1>
            @if($errors->any())
                <p>{{ $errors->first() }}</p>
            @endif
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="">Name</label>
                    <input type="text" name="name" id="name" required class="s">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" id="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="password" class="">Password</label>
                    <input type="password" name="password" id="password" required class="">
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="">
                        Create User
                    </button>
                    <a href="{{ route('users.index') }}" class="">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
