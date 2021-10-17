<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Login Resource",
 *     description="Login Resource",
 * )
 */
class LoginResource
{
    /**
     * @OA\Property(
     *     title="user",
     *     description="Logged in user",
     *     @OA\Schema(ref="#/components/schemas/UserResource"),
     * )
     *
     *  @var ClientResource
     */
    public $user;

    /**
     * @OA\Property(
     *     title="token",
     *     description="Auth Token",
     *     example="17|1VtteyF1QBiywXwNLQiK80pq7ioJvVAq1C6C6VwO",
     * )
     *
     * @var string
     */
    public $token;
}
