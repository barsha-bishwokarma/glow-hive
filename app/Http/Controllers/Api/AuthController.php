<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successfull.',
            'user'    => new UserResource($user),
        ]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'token' => null,
                'message' => 'Invalid credentials.'
            ]);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Login successfull.',

        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function login_response()
    {
        return response()->json([
            'success' => false,
            'token' => null,
            'message' => 'User not loggedIn.',

        ]);
    }

    public function logout(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }
}
