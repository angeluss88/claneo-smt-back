<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="User Resource",
 *     description="User Resource",
 * )
 */
class UserResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of User",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Name",
     *     description="Name of user",
     *     example="User",
     * )
     *
     * @var string
     */
    public $name;

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
     *     title="email_verified_at",
     *     description="Email verified date of user",
     *     example="NULL",
     * )
     *
     * @var string
     */
    public $email_verified_at;

    /**
     * @OA\Property(
     *     title="created_at",
     *     description="created date of user",
     *     example="2021-10-07T19:34:40.000000Z"
     * )
     *
     * @var string
     */
    public $created_at;

    /**
     * @OA\Property(
     *     title="updated_at",
     *     description="updated date of user",
     *     example="2021-10-07T19:34:40.000000Z"
     * )
     *
     * @var string
     */
    public $updated_at;
}
