<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     operationId="register",
     *     tags={"Auth"},
     *     summary="Register new user by admin",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ]);

        $fields['password'] = '12345';

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
     * @OA\Post(
     *     path="/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Login to get Auth Token",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(ref="#/components/schemas/LoginResource")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Bad Credentials",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     * )
     *
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
                'message' => 'Bad credentials',
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        return response($response, 201);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Logout to remove Auth Token",
     *     @OA\Response(
     *         response="200",
     *         description="Logged out",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @return array
     */
    public function logout(Request $request): array
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }
}
