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
     *     title="client_id",
     *     description="Client id to assign",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $client_id;

    /**
     * @OA\Property(
     *     title="client",
     *     description="Client name to assign",
     *     example="Company",
     * )
     *
     * @var string
     */
    public $client;

    /**
     * @OA\Property(
     *     title="ga_property_id",
     *     description="ga_property_id",
     *     example=""
     * )
     *
     * @var string
     */
    public $ga_property_id;

    /**
     * @OA\Property(
     *     title="ua_property_id",
     *     description="ua_property_id",
     *     example=""
     * )
     *
     * @var string
     */
    public $ua_property_id;

    /**
     * @OA\Property(
     *     title="ua_view_id",
     *     description="ua_view_id",
     *     example=""
     * )
     *
     * @var string
     */
    public $ua_view_id;

    /**
     * @OA\Property(
     *     title="strategy",
     *     description="Expand Strategy ('ga_property', 'ua_property' or 'no_expand')",
     *     example="no_expand"
     * )
     *
     * @var string
     */
    public $strategy;
}

