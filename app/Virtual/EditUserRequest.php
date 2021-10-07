<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Edit User Request",
 *     description="Edit User Request",
 * )
 */
class EditUserRequest
{
    /**
     * @OA\Property(
     *     title="Password",
     *     description="Password of user",
     *     example="12345",
     * )
     *
     * @var string
     */
    public $password;
}
