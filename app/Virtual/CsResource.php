<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Content Strategy Resource",
 *     description="Content Strategy Resource",
 * )
 */
class CsResource
{
    /**
     * @OA\Property(
     *     title="URL",
     *     description="URL",
     *     example="https://www.site.com/some/page",
     * )
     *
     * @var string
     */
    public $url;

    /**
     * @OA\Property(
     *     title="page_type",
     *     description="page_type",
     *     example="null",
     * )
     *
     * @var string
     */
    public $page_type;

    /**
     * @OA\Property(
     *     title="main_category",
     *     description="Main Category",
     *     example="Home",
     * )
     *
     * @var string
     */
    public $main_category;

    /**
     * @OA\Property(
     *     title="sub_category",
     *     description="Sub Category",
     *     example="",
     * )
     *
     * @var string
     */
    public $sub_category;

    /**
     * @OA\Property(
     *     title="sub_category2",
     *     description="Sub Category 2",
     *     example="",
     * )
     *
     * @var string
     */
    public $sub_category2;


    /**
     * @OA\Property(
     *     title="sub_category3",
     *     description="Sub Category 3",
     *     example="",
     * )
     *
     * @var string
     */
    public $sub_category3;


    /**
     * @OA\Property(
     *     title="sub_category4",
     *     description="Sub Category 4",
     *     example="",
     * )
     *
     * @var string
     */
    public $sub_category4;


    /**
     * @OA\Property(
     *     title="sub_category5",
     *     description="Sub Category 5",
     *     example="",
     * )
     *
     * @var string
     */
    public $sub_category5;

    /**
     * @OA\Property(
     *     title="status",
     *     description="status",
     *     example="200",
     * )
     *
     * @var string
     */
    public $status;

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
     *     title="keywords",
     *     description="Keyword",
     *     example="kw",
     * )
     *
     * @var string
     */
    public $keyword;

    /**
     * @OA\Property(
     *     title="current_ranking_position",
     *     description="current_ranking_position",
     *     example="Not in top 100",
     * )
     *
     * @var string
     */
    public $current_ranking_position;

    /**
     * @OA\Property(
     *     title="search_volume",
     *     description="Search Volume",
     *     example="30",
     * )
     *
     * @var integer
     */
    public $search_volume;

    /**
     * @OA\Property(
     *     title="search_volume_clustered",
     *     description="Search Volume Clustered",
     *     example="30",
     * )
     *
     * @var integer
     */
    public $search_volume_clustered;

    /**
     * @OA\Property(
     *     title="current_ranking_url",
     *     description="current_ranking_url",
     *     example="https://www.site.com/some/page",
     * )
     *
     * @var string
     */
    public $current_ranking_url;

    /**
     * @OA\Property(
     *     title="featured_snippet_keyword",
     *     description="Featured Snippet Keyword",
     *     example="",
     * )
     *
     * @var string
     */
    public $featured_snippet_keyword;

    /**
     * @OA\Property(
     *     title="featured_snippet_owned",
     *     description="Featured Snippet Owned",
     *     example="",
     * )
     *
     * @var string
     */
    public $featured_snippet_owned;

    /**
     * @OA\Property(
     *     title="search_intention",
     *     description="Search Intention",
     *     example="transactional",
     * )
     *
     * @var string
     */
    public $search_intention;

    /**
     * @OA\Property(
     *     title="id",
     *     description="ID of URL",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $id;

}
