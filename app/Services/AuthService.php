<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function attempt(string $email, string $password, array $roles): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password) || ! in_array($user->role, $roles, true)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        return $user;
    }

    public function registerCustomer(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function issueToken(User $user): string
    {
        return $user->createToken('spa')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }
}
