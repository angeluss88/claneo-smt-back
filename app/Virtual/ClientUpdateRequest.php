<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Client Update Request",
 *     description="Client Update Request",
 * )
 */
class ClientUpdateRequest
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
}
