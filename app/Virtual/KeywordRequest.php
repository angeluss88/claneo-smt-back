<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Keyword Request",
 *     description="Keyword Request",
 * )
 */
class KeywordRequest
{
    /**
     * @OA\Property(
     *     title="keyword",
     *     description="keyword",
     *     example="the best site ever",
     * )
     *
     * @var string
     */
    public $keyword;

    /**
     * @OA\Property(
     *     title="search_volume",
     *     description="search_volume",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $search_volume;

    /**
     * @OA\Property(
     *     title="search_volume_clustered",
     *     description="search_volume_clustered",
     *     example=1,
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
     *     example="yes",
     * )
     *
     * @var string
     */
    public $featured_snippet_keyword;

    /**
     * @OA\Property(
     *     title="featured_snippet_owned",
     *     description="featured_snippet_owned",
     *     example="yes",
     * )
     *
     * @var string
     */
    public $featured_snippet_owned;

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
     *     title="current_ranking_position",
     *     description="current_ranking_position",
     *     example=1,
     * )
     *
     * @var string
     */
    public $current_ranking_position;

}
