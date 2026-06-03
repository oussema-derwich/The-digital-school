<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
{
    try {
        $result = $this->userService->createUser($request);
        $user = $result['user'];
        $password = $result['password']; // Get the password from the returned data

        return response()->json([
            'message' => 'User created successfully. An email has been sent.',
            'user' => $user,
            'generated_password' => $password,
            'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while creating the user.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = $this->userService->updateUser($request, $id);

            return response()->json([
                'message' => 'User updated successfully.',
                'user' => $user,
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            return $this->userService->deleteUser($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
