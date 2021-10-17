<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Client Request",
 *     description="Client Request",
 * )
 */
class ClientRequest
{
    /**
     * @OA\Property(
     *     title="name",
     *     description="Name of Client Company",
     *     example="Company",
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="description",
     *     description="ID of user to assign",
     *     example="2",
     * )
     *
     * @var integer
     */
    public $user_id;
}
