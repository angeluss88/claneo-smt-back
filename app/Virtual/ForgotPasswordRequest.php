<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Reset Password Request",
 *     description="Reset Password Request",
 *     required={"email"}
 * )
 */
class ForgotPasswordRequest
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
