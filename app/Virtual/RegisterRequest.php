<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Register Request",
 *     description="Register Request",
 * )
 */
class RegisterRequest
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
}
