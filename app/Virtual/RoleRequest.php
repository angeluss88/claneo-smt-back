<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Role Request",
 *     description="Role Request",
 * )
 */
class RoleRequest
{
    /**
     * @OA\Property(
     *     title="name",
     *     description="Name of role",
     *     example="user",
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Description of user",
     *     example="SImple User",
     * )
     *
     * @var string
     */
    public $description;
}
