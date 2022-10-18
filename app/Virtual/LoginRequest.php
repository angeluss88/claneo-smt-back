<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Login Request",
 *     description="Login Request",
 *     required={"email", "password"}
 * )
 */
class LoginRequest
{
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
     *     example="password",
     * )
     *
     * @var string
     */
    public $password;
}
