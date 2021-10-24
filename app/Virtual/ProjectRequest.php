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
     *     title="user_id",
     *     description="ID or user to assign",
     *     example="1",
     * )
     *
     * @var string
     */
    public $user_id;
}
