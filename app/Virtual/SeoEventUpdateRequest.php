<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="SeoEvent Update Request",
 *     description="SeoEvent Update Request",
 * )
 */
class SeoEventUpdateRequest
{
    /**
     * @OA\Property(
     *     title="title",
     *     description="Seo Event Title",
     *     example="SeoEvent",
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Seo Event Description",
     *     example="description",
     * )
     *
     * @var string
     */
    public $description;

    /**
     * @OA\Property(
     *     title="entity_id",
     *     description="Entity id",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $entity_id;

    /**
     * @OA\Property(
     *     title="entity_type",
     *     description="Entity type['project', 'url']",
     *     example="project",
     * )
     *
     * @var string
     */
    public $entity_type;

    /**
     * @OA\Property(
     *     title="date",
     *     description="Seo Event Date",
     *     example="2023-12-31",
     * )
     *
     * @var string
     */
    public $date;

}
