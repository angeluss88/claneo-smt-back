<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Project Update Request",
 *     description="Project Update Request",
 * )
 */
class ProjectUpdateRequest
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

    /**
     * @OA\Property(
     *     title="value_multiplier",
     *     description="value_multiplier",
     *     example=1.11
     * )
     *
     * @var float
     */
    public $value_multiplier;

    /**
     * @OA\Property(
     *     title="cpm",
     *     description="CPM",
     *     example=1.11
     * )
     *
     * @var float
     */
    public $cpm;

    /**
     * @OA\Property(
     *     title="brand_terms",
     *     description="brand_terms",
     *     example="lorem,ipsum,dolor"
     * )
     *
     * @var string
     */
    public $brand_terms;
}
