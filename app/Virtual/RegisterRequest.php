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

    /**
     * @OA\Property(
     *     title="first_name",
     *     description="first_name of user",
     *     example="John",
     * )
     *
     * @var string
     */
    public $first_name;

    /**
     * @OA\Property(
     *     title="last_name",
     *     description="last_name of user",
     *     example="Doe",
     * )
     *
     * @var string
     */
    public $last_name;

    /**
     * @OA\Property(
     *     title="client",
     *     description="Company name",
     *     example="Client",
     * )
     *
     * @var string
     */
    public $client;

    /**
     * @OA\Property(
     *     title="roles",
     *     description="Roles of user",
     *     example={1, 2},
     * )
     *
     * @var string
     */
    public $roles;
}
