<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Retrieve user profile information.
     */
    public function getProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
        ];
    }

    /**
     * Update user profile.
     */
    public function updateProfile(User $user, array $data): array
    {
        // Handle profile photo
        if (isset($data['photo'])) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $photoPath = $data['photo']->store('photos', 'public');
            $data['photo'] = $photoPath;
        }

        // Check if email_verified_at is null and update if necessary
        if ($user->email_verified_at === null) {
            $data['email_verified_at'] = now(); // Current date
        }

        // Update user
        $user->update($data);

        return [
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => $this->getProfile($user),
            'email_verified_at' => $user->email_verified_at,
        ];
    }

    /**
     * Change user password.
     */
    public function changePassword(User $user, array $data): array
    {
        if (!Hash::check($data['old_password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'The current password is incorrect.',
            ];
        }

        if (Hash::check($data['new_password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'The new password must be different from the old one.',
            ];
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $data['new_password'])) {
            return [
                'success' => false,
                'message' => 'The password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character.',
            ];
        }

        $user->update(['password' => Hash::make($data['new_password'])]);

        return [
            'success' => true,
            'message' => 'Password changed successfully.',
        ];
    }
}
