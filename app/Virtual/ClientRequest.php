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
}
