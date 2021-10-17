<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoleRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $fields = $request->validate([
            'name' => 'required|unique:roles,name|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $role = Role::create([
            'name' => $fields['name'],
            'description' => $fields['description'],
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
            'role' => $role,
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
     *         @OA\JsonContent(ref="#/components/schemas/RoleRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @param Role $role
     * @return Response
     */
    public function update(Request $request, Role $role): Response
    {
        $fields = $request->validate([
            'name' => 'unique:roles,name|string|max:255',
            'description' => 'string|max:255',
        ]);

        $role->fill($fields)->save();

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
        $role->delete();

        return response([], 204);
    }
}
