<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="User Update Request",
 *     description="User Request for update action",
 * )
 */
class UserUpdateRequest
{
    /**
     * @OA\Property(
     *     title="first_ame",
     *     description="First Name of user",
     *     example="John",
     * )
     *
     * @var string
     */
    public $first_name;

    /**
     * @OA\Property(
     *     title="last_ame",
     *     description="Last Name of user",
     *     example="Doe",
     * )
     *
     * @var string
     */
    public $last_name;

    /**
     * @OA\Property(
     *     title="Email",
     *     description="Email of user",
     *     example="user@loc",
     * )
     *
     * @var string
     */
    public $email;

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

    /**
     * @OA\Property(
     *     title="roles",
     *     description="Roles of user",
     *     example={5, 6},
     * )
     *
     * @var string
     */
    public $roles;
}
