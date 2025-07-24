<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
public function showLogin()
{
return view('auth.login');
}

public function login(Request $request)
{
$credentials = $request->only('email', 'password');

if (Auth::attempt($credentials)) {
return redirect('/dashboard');
}

return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
}

public function showRegister()
{
return view('auth.register');
}

public function register(Request $request)
{
$request->validate([
'name' => 'required|string|max:255',
'email' => 'required|email|unique:users',
'password' => 'required|min:6|confirmed',
]);

User::create([
'name' => $request->name,
'email' => $request->email,
'password' => Hash::make($request->password),
]);

return redirect('/login')->with('success', 'Registration successful. You can now login.');
}

public function dashboard()
{
return view('dashboard');
}

public function logout()
{
Auth::logout();
return redirect('/');
}
}
