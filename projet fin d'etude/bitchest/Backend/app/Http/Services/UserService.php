<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function createUser($request)
    {
        // Validate the data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate a random password
        $password = Str::random(10);

        // Create user data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($password),
        ];

        // Handle photo upload if present
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $userData['photo'] = $path;
        }

        // Create the user
        $user = User::create($userData);

        // Send password email
        try {
            Mail::to($user->email)->send(new SendPasswordMail($user->name, $password));
            Log::debug('Email sent successfully to ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Error sending email', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'User created, but an error occurred while sending the email.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return ['user' => $user, 'password' => $password]; // Return both user and password
    }

    public function updateUser($request, $id)
    {
        // Validate data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,client',
            'photo' => 'nullable|mimes:jpeg,jpg,png,bmp,gif,svg,webp|max:2048',
        ]);

        // Find user
        $user = User::findOrFail($id);

        // Prepare data for updating
        $userData = $request->only(['name', 'email', 'role']);

        // Handle photo upload if present
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $path = $photo->storeAs('photos', 'photo_' . time() . '.' . $photo->getClientOriginalExtension(), 'public');
            $userData['photo'] = $path;
        }

        // Update user
        $user->update($userData);

        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Delete associated wallets if needed
        $user->wallets()->delete();

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
