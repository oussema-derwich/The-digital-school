<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Authenticate user by email and password.
     *
     * @param string $email
     * @param string $password
     * @return \App\Models\User|null
     */
    public function authenticate($email, $password)
    {
        // Find the user by email
        $user = User::where('email', $email)->first();

        // If the user is not found, return null
        if (!$user) {
            return null;
        }

        // If the password does not match, return null
        if (!Hash::check($password, $user->password)) {
            return null;
        }

        // Return the user object if authentication is successful
        return $user;
    }

}
