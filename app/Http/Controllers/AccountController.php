<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountEditRequest;
use App\Models\Event;
use App\Models\URL;
use App\Models\User;
use Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

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
     * @param AccountEditRequest $request
     * @return Response
     */
    public function edit(AccountEditRequest $request): Response
    {
        $fields = $request->validated();
        /**
         * @var User $user
         */
        $user = User::with(['roles', 'client', 'projects',])->find(auth()->id());

        if(isset($fields['password'])) {
            $user->password = Hash::make($fields['password']);
        }

        if(isset($fields['roles']) && !empty($fields['roles'])) {
            $user->roles()->sync($fields['roles']);
        }

        if(!$user->hasRole('Client')) {
            $user->client_id = null;
        }

        $oldAttributes = $user->getOriginal();
        $user->fill($fields)->save();

        Event::create([
            'user_id' => Auth::user()->id,
            'entity_type' => User::class,
            'entity_id' => $user->id,
            'action' => Event::UPDATE_ACTION,
            'data' =>  $fields,
            'oldData' => $oldAttributes,
        ]);

        return response([
            'user' => User::with(['roles', 'client', 'projects',])->find(auth()->id()),
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
    public function show()
    {
        return response([
            'user' => User::with(['roles', 'client', 'projects',])->find(auth()->id()),
        ], 200);
    }
}
