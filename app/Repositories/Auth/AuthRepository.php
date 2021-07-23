<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    public function register($request): array
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::fromUser($user);

        if ($request->remember_me) {
            $token = auth()->setTTL(86400 * 30)->fromUser($user);
        }

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login($request): array
    {
        $credentials = $request->only('email', 'password');

        if ($request->remember_me) {
            $token = auth()->setTTL(86400 * 30)->attempt($credentials);
        } else {
            $token = Auth::attempt($credentials);
        }

        if (!$token) {
            throw new \Exception('Unauthorized');
        }

        $user = Auth::user();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout()
    {
        Auth::invalidate();
    }
}
