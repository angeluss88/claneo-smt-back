<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Event;
use App\Models\Role;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/roles",
     *     operationId="roles_index",
     *     tags={"Roles"},
     *     summary="List of Roles",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="role",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/RoleRequest",ref="#/components/schemas/RoleResource")
     *         )),
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
     * @return Response
     */
    public function index(): Response
    {
        $roles = Role::all();

        return response([
            'roles' => $roles,
        ], 200);
    }

    /**
     *
     * @OA\Post (
     *     path="/roles",
     *     operationId="roles_store",
     *     tags={"Roles"},
     *     summary="Create Role",
     *     @OA\Response(
     *         response="201",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="role",
     *             type="object",
     *             ref="#/components/schemas/RoleResource",
     *         ))
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
     *         @OA\JsonContent(ref="#/components/schemas/RoleRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param RoleStoreRequest $request
     * @return Response
     */
    public function store(RoleStoreRequest $request): Response
    {
        $fields = $request->validated();

        $role = Role::create($fields);

        Event::create([
            'user_id' => \Auth::user()->id,
            'entity_type' => Role::class,
            'entity_id' => $role->id,
            'action' => Event::CREATE_ACTION,
            'data' =>  $fields,
            'oldData' => [],
        ]);

        return response([
            'role' => $role
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/roles/{role}",
     *     operationId="roles_show",
     *     tags={"Roles"},
     *     summary="Show Role",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="role",
     *             type="object",
     *             ref="#/components/schemas/RoleResource",
     *         )),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="The role id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Role $role
     * @return Response
     */
    public function show(Role $role): Response
    {
        return response([
            'role' => Role::with('users')->find($role->id),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/roles/{role}",
     *     operationId="roles_update",
     *     tags={"Roles"},
     *     summary="Update Role",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="role",
     *             type="object",
     *             ref="#/components/schemas/RoleResource",
     *         )),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="The role id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoleUpdateRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param RoleUpdateRequest $request
     * @param Role $role
     * @return Response
     */
    public function update(RoleUpdateRequest $request, Role $role): Response
    {
        $fields = $request->validated();
        $oldData = $role->getOriginal();

        $role->fill($fields)->save();

        Event::create([
            'user_id' => \Auth::user()->id,
            'entity_type' => Role::class,
            'entity_id' => $role->id,
            'action' => Event::UPDATE_ACTION,
            'data' =>  $request->validated(),
            'oldData' => $oldData,
        ]);

        return response([
            'role' => $role,
        ], 200);
    }

    /**
     * @OA\Delete (
     *     path="/roles/{role}",
     *     operationId="roles_delete",
     *     tags={"Roles"},
     *     summary="Delete Role",
     *     @OA\Response(
     *         response="204",
     *         description="Everything is fine",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="The role id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Role $role
     * @return Response
     */
    public function destroy(Role $role): Response
    {
        if($role->id > 4) { // admin, Seo, Researcher and Client couldn't be deleted
            $role->delete();

            Event::create([
                'user_id' => \Auth::user()->id,
                'entity_type' => Role::class,
                'entity_id' => $role->id,
                'action' => Event::DELETE_ACTION,
                'data' =>  [],
                'oldData' => [],
            ]);
        }

        return response([], 204);
    }
}
