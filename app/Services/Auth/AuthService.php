<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = auth('api')->login($user);
        return ['user' => $user, 'token' => $token];
    }

    /**
     * @param array $credentials
     * @return array|null
     */
    public function login(array $credentials): ?array
    {
        $token = auth('api')->attempt($credentials);
        return !empty($token) ? ['token' => $token] : null;
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        auth('api')->logout();
    }
}
