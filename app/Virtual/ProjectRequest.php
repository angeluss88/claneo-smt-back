<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Project Request",
 *     description="Project Request",
 * )
 */
class ProjectRequest
{
    /**
     * @OA\Property(
     *     title="domain",
     *     description="Project Domain",
     *     example="example.com",
     * )
     *
     * @var string
     */
    public $domain;

    /**
     * @OA\Property(
     *     title="client",
     *     description="CLient name to assign",
     *     example="Company",
     * )
     *
     * @var string
     */
    public $client;
}
