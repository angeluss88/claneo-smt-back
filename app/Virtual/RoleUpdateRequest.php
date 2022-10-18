<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Role Update Request",
 *     description="Role Update Request",
 * )
 */
class RoleUpdateRequest
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
