<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Role Resource",
 *     description="Role Resource",
 * )
 */
class RoleResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of Role",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="name",
     *     description=" Name of Role",
     *     example="SEO",
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Description of role",
     *     example="SEO Manager",
     * )
     *
     * @var string
     */
    public $description;

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

}
