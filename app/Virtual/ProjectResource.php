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
     *     title="user_id",
     *     description="ID of user",
     *     example=1,
     * )
     *
     * @var string
     */
    public $user_id;

    /**
     * @OA\Property(
     *     title="user",
     *     type="object",
     *     @OA\Schema (ref="#/components/schemas/UserResource")
     * )
     */
    public $user;

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
     *     example="null"
     * )
     *
     * @var string
     */
    public $ga_property_id;

    /**
     * @OA\Property(
     *     title="ua_property_id",
     *     description="ua_property_id",
     *     example="null"
     * )
     *
     * @var string
     */
    public $ua_property_id;

    /**
     * @OA\Property(
     *     title="ua_view_id",
     *     description="ua_view_id",
     *     example="null"
     * )
     *
     * @var string
     */
    public $ua_view_id;

}
