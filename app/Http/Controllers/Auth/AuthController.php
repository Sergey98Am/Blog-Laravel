<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);

            if (!$user) {
                throw new \Exception('Something went wrong');
            }

            if ($request->rememberMe) {
                $token = auth()->setTTL(86400 * 30)->fromUser($user);
            }

            return response()->json([
                'token' => $token,
                'user' => User::find($user->id),
                'ttl' => JWTAuth::factory()->getTTL(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
