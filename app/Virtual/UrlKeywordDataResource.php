<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="UrlKeywordData Resource",
 *     description="UrlKeywordData Resource",
 * )
 */
class UrlKeywordDataResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="url_keyword_id",
     *     description="url_keyword_id",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $url_keyword_id;

    /**
     * @OA\Property(
     *     title="position",
     *     description="position",
     *     example="1",
     * )
     *
     * @var string
     */
    public $position;

    /**
     * @OA\Property(
     *     title="clicks",
     *     description="clicks",
     *     example="1",
     * )
     *
     * @var string
     */
    public $clicks;

    /**
     * @OA\Property(
     *     title="impressions",
     *     description="impressions",
     *     example="1",
     * )
     *
     * @var string
     */
    public $impressions;

    /**
     * @OA\Property(
     *     title="ctr",
     *     description="ctr",
     *     example="1",
     * )
     *
     * @var string
     */
    public $ctr;

    /**
     * @OA\Property(
     *     title="date",
     *     description="date of Details",
     *     example="2021-03-21"
     * )
     *
     * @var string
     */
    public $date;

    /**
     * @OA\Property(
     *     title="laravel_through_key",
     *     description="laravel_through_key",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $laravel_through_key;
}
