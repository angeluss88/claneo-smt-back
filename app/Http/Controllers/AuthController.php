<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
        ]);

        /**
         * @var $user User
         */
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        //@TODO send email with password_reset link

        $response = [
            'user' => $user,
        ];

        return response($response, 201);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        /**
         * @var $user User
         */
        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'status' => 'failed',
                'message' => 'Bad credentials',
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function logout(Request $request): array
    {
        auth()->user()->tokens()->delete();

        return [
            'status' => 'success',
            'message' => 'Logged out',
        ];
    }
}
