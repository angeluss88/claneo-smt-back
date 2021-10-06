<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function edit(Request $request)
    {
        $fields = $request->validate([
            'password' => 'string',
        ]);

        $user = $request->user();

        if(!$user) {
            return response([
                'status' => 'failed',
                'message' => 'User not found',
            ], 401);
        }

        $user->password = Hash::make($fields['password']);
        $user->save();

        return response([
            'status' => 'success',
            'user' => $user,
        ], 201);
    }

    public function show(Request $request)
    {
        return response([
            'status' => 'success',
            'user' => $request->user(),
        ], 200);
    }
}
