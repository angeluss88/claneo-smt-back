<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Project Resource",
 *     description="Project Resource",
 * )
 */
class ProjectResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of Project",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

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
     *     description="ID of client",
     *     example=1,
     * )
     *
     * @var string
     */
    public $client_id;

    /**
     * @OA\Property(
     *     title="client",
     *     type="object",
     *     @OA\Schema (ref="#/components/schemas/ClientResource")
     * )
     */
    public $client;

    /**
     * @OA\Property(
     *     title="created_at",
     *     description="created date of project",
     *     example="2021-10-07T19:34:40.000000Z"
     * )
     *
     * @var string
     */
    public $created_at;

    /**
     * @OA\Property(
     *     title="updated_at",
     *     description="updated date of project",
     *     example="2021-10-07T19:34:40.000000Z"
     * )
     *
     * @var string
     */
    public $updated_at;

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
     *     description="Expand Strategy",
     *     example=""
     * )
     *
     * @var string
     */
    public $strategy;

    /**
     * @OA\Property(
     *     title="expand_gsc",
     *     description="Expand GSC",
     *     example=1
     * )
     *
     * @var string
     */
    public $expand_gsc;

}
