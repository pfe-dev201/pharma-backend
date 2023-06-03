<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $role = User::find(Auth::id())->getRole();
        // Authentication successful
        return response()->json(['message' => 'Login successful', "user" => $user, "role" => $role], 200);
    } else {
        // Authentication failed
        return response()->json(['error' => 'email ou mot de passe incorrect'],401);
    }
}

}
