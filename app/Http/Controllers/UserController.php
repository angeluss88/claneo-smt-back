<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function edit(Request $request, $id)
    {
        $fields = $request->validate([
            'name' => 'string',
            'email' => 'email|unique:users,email',
            'password' => 'string',
        ]);

        $user = User::find($id);

        if(!$user) {
            return response([
                'status' => 'failed',
                'message' => 'User not found',
            ], 401);
        }

        $user->fill($fields)->save();

        return response([
            'status' => 'success',
            'user' => $user,
        ], 201);
    }
}
