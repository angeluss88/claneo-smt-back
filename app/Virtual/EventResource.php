<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Event Resource",
 *     description="Event Resource",
 * )
 */
class EventResource
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
     *     title="entity_type",
     *     description="Name of related model",
     *     example="App\\Models\\URL",
     * )
     *
     * @var integer
     */
    public $entity_type;


    /**
     * @OA\Property(
     *     title="entity_id",
     *     description="ID of related entity",
     *     example=3,
     * )
     *
     * @var integer
     */
    public $entity_id;

    /**
     * @OA\Property(
     *     title="data",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/UrlNoRelationsResource")
     * )
     */
    public $data;

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
     *     title="action",
     *     description="action",
     *     example="create",
     * )
     *
     * @var string
     */
    public $status;

    /**
     * @OA\Property(
     *     title="oldData",
     *     description="oldData",
     *     example=null,
     * )
     *
     * @var string
     */
    public $oldData;

    /**
     * @OA\Property(
     *     title="user",
     *     type="object",
     *     @OA\Schema (ref="#/components/schemas/UserResource")
     * )
     */
    public $user;
}
