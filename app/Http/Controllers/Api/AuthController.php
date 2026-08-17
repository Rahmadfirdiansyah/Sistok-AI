<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $user->createToken('auth_Token')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau passwordnya Benerin Ya Sayang'], 401);
        }

        $token = $user->createToken('auth_Token')->plainTextToken;

        return response()->json([
            'massage' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function guestStaffLogin()
    {
        $user = User::firstOrCreate(
            ['email' => 'staff@dasen.id'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('dasendco'),
                'role' => 'user'
            ]
        );

        $token = $user->createToken('auth_Token')->plainTextToken;

        return response()->json([
            'message' => 'Guest staff login successful',
            'token' => $token,
            'user' => $user
        ]);
    }
}

