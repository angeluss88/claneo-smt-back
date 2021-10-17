<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Client Resource",
 *     description="Client Resource",
 * )
 */
class ClientResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of Client",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="name",
     *     description="Name of client",
     *     example="Client",
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="created_at",
     *     description="created date of client",
     *     example="2021-10-07T19:34:40.000000Z"
     * )
     *
     * @var string
     */
    public $created_at;

    /**
     * @OA\Property(
     *     title="updated_at",
     *     description="updated date of client",
     *     example="2021-10-07T19:34:40.000000Z"
     * )
     *
     * @var string
     */
    public $updated_at;

    /**
     * @OA\Property(
     *     title="user",
     *     type="object",
     *     @OA\Schema (ref="#/components/schemas/UserResource")
     * )
     */
    public $user;
}
