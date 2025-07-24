<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validatedData =
        [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $path;
        }

        User::create($validatedData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8|confirmed',
//            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Generate random filename with .png extension
            $filename = Str::random(8).'.png';

//            $manager = new ImageManager(new Driver());

            // read image from file system
//            $image = $manager->read($request->file('avatar'));
            // Image Crop
//            $image->fit(500,500)->encode('png', 90);
            // insert watermark
//            $image->place('uploads/water_mark.png');
//            //Save the file
//            $image->save(public_path('uploads/crop.jpeg'));

            // Process image
//            $image = Image::read($request->file('avatar'))
//                ->fit(500, 500) // Square crop
//                ->encode('png', 90); // 90% quality PNG
//
//            // Save to storage
//            Storage::disk('public')->put("avatars/{$filename}", $image);
//
//            // Delete old avatar if exists
//            if ($user->avatar) {
//                Storage::disk('public')->delete($user->avatar);
//            }

//            $data['avatar'] = "avatars/{$filename}";
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}