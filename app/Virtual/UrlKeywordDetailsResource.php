<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="UrlKeywordDetails Resource",
 *     description="UrlKeywordDetails Resource",
 * )
 */
class UrlKeywordDetailsResource
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of Keyword",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Keyword",
     *     description="Keyword",
     *     example="very cute shop",
     * )
     *
     * @var string
     */
    public $keyword;

    /**
     * @OA\Property(
     *     title="search_volume",
     *     description="search_volume",
     *     example="1",
     * )
     *
     * @var integer
     */
    public $search_volume;

    /**
     * @OA\Property(
     *     title="search_volume_clustered",
     *     description="search_volume_clustered",
     *     example="1",
     * )
     *
     * @var integer
     */
    public $search_volume_clustered;

    /**
     * @OA\Property(
     *     title="current_ranking_url",
     *     description="current_ranking_url",
     *     example="https://www.site.com/page",
     * )
     *
     * @var string
     */
    public $current_ranking_url;

    /**
     * @OA\Property(
     *     title="featured_snippet_keyword",
     *     description="featured_snippet_keyword",
     *     example="",
     * )
     *
     * @var string
     */
    public $featured_snippet_keyword;

    /**
     * @OA\Property(
     *     title="featured_snippet_owned",
     *     description="featured_snippet_owned",
     *     example="",
     * )
     *
     * @var string
     */
    public $featured_snippet_owned;

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

    /**
     * @OA\Property(
     *     title="search_intention",
     *     description="search_intention",
     *     example="transactional",
     * )
     *
     * @var string
     */
    public $search_intention;

    /**
     * @OA\Property(
     *     title="import_id",
     *     description="import_id",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $import_id;

    /**
     * @OA\Property(
     *     title="current_ranking_position",
     *     description="current_ranking_position",
     *     example="Not in Top 100",
     * )
     *
     * @var string
     */
    public $current_ranking_position;

    /**
     * @OA\Property(
     *     title="totalPosition",
     *     description="totalPosition",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $totalPosition;

    /**
     * @OA\Property(
     *     title="totalClicks",
     *     description="totalClicks",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $totalClicks;

    /**
     * @OA\Property(
     *     title="totalImpressions",
     *     description="totalImpressions",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $totalImpressions;

    /**
     * @OA\Property(
     *     title="totalCtr",
     *     description="totalCtr",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $totalCtr;

    /**
     * @OA\Property(
     *     title="urls",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/UrlKeywordDataResource")
     * )
     */
    public $url_keyword_data;

}
