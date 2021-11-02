<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * @OA\Post(
     *     path="/account/edit",
     *     operationId="editAccount",
     *     tags={"Account"},
     *     summary="Edit current user's account",
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
     *         @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $fields = $request->validate([
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'email' => [
                'email',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
            'privacy_policy_flag' => 'boolean',
            'password' => 'string',
            'roles' => 'array',
        ]);

        $user = User::with(['roles', 'client', 'projects',])->find(auth()->user()->id);

        if(isset($fields['password'])) {
            $user->password = Hash::make($fields['password']);
        }

        $user->fill($fields)->save();

        return response([
            'user' => $user,
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/account",
     *      operationId="getCurrentUser",
     *      tags={"Account"},
     *      summary="Get current user info",
     *      description="Returns current user's info",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      security={
     *       {"bearerAuth": {}},
     *     },
     *     )
     */
    public function show(Request $request)
    {
        $user = User::with(['roles', 'client', 'projects',])->find($request->user()->id);
        return response([
            'user' => $user,
        ], 200);
    }
}
