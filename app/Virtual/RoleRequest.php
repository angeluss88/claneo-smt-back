<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Role Request",
 *     description="Role Request",
 *     required={"name", "description"}
 * )
 */
class RoleRequest
{
    /**
     * @OA\Property(
     *     title="name",
     *     description="Name of role",
     *     example="role",
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Description of role",
     *     example="Simple Role",
     * )
     *
     * @var string
     */
    public $description;
}
