<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="SeoEvent Resource",
 *     description="SeoEvent Resource",
 * )
 */
class SeoEventResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of SeoEvent",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="title",
     *     description="SeoEvent Title",
     *     example="SeoEventTitle",
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @OA\Property(
     *     title="description",
     *     description="SeoEvent Description",
     *     example="Seo Event Description",
     * )
     *
     * @var string
     */
    public $description;

    /**
     * @OA\Property(
     *     title="entity_type",
     *     description="Type of related model",
     *     example="App\\Models\\Project",
     * )
     *
     * @var integer
     */
    public $entity_type;

    /**
     * @OA\Property(
     *     title="entity_id",
     *     description="ID of related entity",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $entity_id;

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
     *     title="date",
     *     description="Date of Seo Event",
     *     example="2023-12-31T00:00:00.000000Z",
     * )
     *
     * @var string
     */
    public $date;

    /**
     * @OA\Property(
     *     title="entity",
     *     type="object",
     *     @OA\Schema (ref="#/components/schemas/ProjectResource")
     * )
     */
    public $entity;

    /**
     * @OA\Property(
     *     title="type",
     *     description="Type of related model",
     *     example="url",
     * )
     *
     * @var integer
     */
    public $type;

}
