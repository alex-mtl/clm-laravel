@extends('layouts.app')

@section('content')
    <div class="content-main">

        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="user-profile">
            @csrf
            @method('PUT')
            <div class="user-avatar-area">
                @include('users.avatar', ['user' => $user])
                <div class="">
{{--                    <label for="avatar" class="">Avatar</label>--}}
{{--                    <input type="file" name="avatar" accept="image/*">--}}

                    <!-- Where you want the upload controls -->
                    <x-avatar-upload
                        :initial-avatar=" asset('storage/' . $user->avatar)"
                        name="avatar"
                    />

                </div>
            </div>
            <div class="user-main-info">
                <div class="">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                {{--                <div class="mb-4">--}}
                {{--                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">New Password (leave blank to keep current)</label>--}}
                {{--                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">--}}
                {{--                </div>--}}

                {{--                <div class="mb-6">--}}
                {{--                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>--}}
                {{--                    <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">--}}
                {{--                </div>--}}

                <div class="">
                    <button type="submit" class="">
                        Update User
                    </button>
                    <a href="{{ route('users.index') }}" class="">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
        <div class="user-action-buttons"> </div>

    </div>

@endsection
