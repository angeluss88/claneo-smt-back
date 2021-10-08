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
     *     title="first_name",
     *     description="First Name of user",
     *     example="John",
     * )
     *
     * @var string
     */
    public $first_name;

    /**
     * @OA\Property(
     *     title="last_name",
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

    /**
     * @OA\Property(
     *     title="roles",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/RoleResource")
     * )
     */
    public $roles;
}
