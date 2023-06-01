<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        // Authentication successful
        return response()->json(['message' => 'Login successful'], 200);
    } else {
        // Authentication failed
        return response()->json(['message' => 'Login successful'],401);
    }
}

}
