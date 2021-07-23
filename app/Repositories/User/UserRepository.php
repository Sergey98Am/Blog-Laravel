<?php

namespace App\Repositories\User;

use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    public function changeDetails($request): object
    {
        $user = Auth::user();

        if ($request->filled('password')) {
            $user->update([
                'name' => $request->name,
                'password' => $user->password,
            ]);
        } else {
            $user->update([
                'name' => $request->name,
            ]);
        }

        $user->password = bcrypt($request->password);

        return $user;
    }
}
