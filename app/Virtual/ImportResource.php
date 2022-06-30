<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Import Resource",
 *     description="Import Resource",
 * )
 */
class ImportResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of Import",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="user_id",
     *     description="ID of relateduser",
     *     example=5,
     * )
     *
     * @var integer
     */
    public $user_id;

    /**
     * @OA\Property(
     *     title="project_id",
     *     description="ID of related project",
     *     example=3,
     * )
     *
     * @var integer
     */
    public $project_id;

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
     *     title="status",
     *     description="Status of import",
     *     example="complete",
     * )
     *
     * @var string
     */
    public $status;

    /**
     * @OA\Property(
     *     title="lastGAExpandDate",
     *     description="Date of last GA expand",
     *     example="2022-05-19T20:04:24.000000Z",
     * )
     *
     * @var string
     */
    public $lastGAExpandDate;

    /**
     * @OA\Property(
     *     title="lastGSCExpandData",
     *     description="Date of last GSC expand",
     *     example="2022-05-19T20:04:24.000000Z",
     * )
     *
     * @var string
     */
    public $lastGSCExpandData;

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
     *     title="project",
     *     type="object",
     *     @OA\Schema (ref="#/components/schemas/ProjectResource")
     * )
     */
    public $project;
}
