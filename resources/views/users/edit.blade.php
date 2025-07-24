@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">

        <div class="p-8">
            <h1 class="text-2xl font-bold mb-6">Edit User</h1>

            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if($user->avatar)
                    <div class="mb-4 user-avatar">
                        <img src="{{ asset('storage/' . $user->avatar) }}" >
                    </div>
                @endif
                <div class="mb-4">
                    <label for="avatar" class="">Avatar</label>
                    <input type="file" name="avatar" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

{{--                <div class="mb-4">--}}
{{--                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">New Password (leave blank to keep current)</label>--}}
{{--                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">--}}
{{--                </div>--}}

{{--                <div class="mb-6">--}}
{{--                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>--}}
{{--                    <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">--}}
{{--                </div>--}}

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update User
                    </button>
                    <a href="{{ route('users.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection