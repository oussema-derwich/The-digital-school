<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens;
    /**
     * Register user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:60',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role')
        ]);


        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json(['data' => $user, 'token' => $token, 'message' => "User registered successfully"], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);


        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            return response()->json(['data' => $user, 'token' => $token, 'message' => "User logged in successfully"]);
        } else {
            return response()->json(['error' => "Email or Password is incorrect"], 401);
        }
    }


    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'logout success']);
    }
}
