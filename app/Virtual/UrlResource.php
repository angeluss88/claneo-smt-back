<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="URL Resource",
 *     description="URL Resource",
 * )
 */
class UrlResource
{
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
     *     title="project_id",
     *     description="project_id",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $project_id;

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
     *     title="keywords_count",
     *     description="Number of keywords",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $keywords_count;

    /**
     * @OA\Property(
     *     title="aggrConvRate",
     *     description="Average conversion rate",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $aggrConvRate;

    /**
     * @OA\Property(
     *     title="aggrRevenue",
     *     description="Average revenue",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $aggrRevenue;

    /**
     * @OA\Property(
     *     title="aggrOrderValue",
     *     description="Average order value",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrOrderValue;

    /**
     * @OA\Property(
     *     title="aggrBounceRate",
     *     description="Average bounce rate",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrBounceRate;

    /**
     * @OA\Property(
     *     title="aggrPosition",
     *     description="Average position",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrPosition;

    /**
     * @OA\Property(
     *     title="aggrClicks",
     *     description="Average clicks",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrClicks;

    /**
     * @OA\Property(
     *     title="aggrImpressions",
     *     description="Average impressions",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrImpressions;

    /**
     * @OA\Property(
     *     title="aggrCtr",
     *     description="Average CTR",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrCtr;

    /**
     * @OA\Property(
     *     title="aggrSearchVolume",
     *     description="Average Search Volume",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrSearchVolume;

    /**
     * @OA\Property(
     *     title="aggrTrafficPotential",
     *     description="Average Traffic Potential",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $aggrTrafficPotential;

    /**
     * @OA\Property(
     *     title="totalUrlKeywordDataCount",
     *     description="UrlKeywordData Count",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $totalUrlKeywordDataCount;

    /**
     * @OA\Property(
     *     title="project",
     *     type="object",
     *     @OA\Schema (ref="#/components/schemas/ProjectResource")
     * )
     */
    public $project;

    /**
     * @OA\Property(
     *     title="events",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/EventResource")
     * )
     */
    public $events;

    /**
     * @OA\Property(
     *     title="keywords",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/KeywordResource")
     * )
     */
    public $keywords;

    /**
     * @OA\Property(
     *     title="urlData",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/UrlDataResource")
     * )
     */
    public $urlData;

    /**
     * @OA\Property(
     *     title="url_keyword_data",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/UrlKeywordDataResource")
     * )
     */
    public $url_keyword_data;

    /**
     * @OA\Property(
     *     title="seo_events",
     *     type="array",
     *     collectionFormat="multi",
     *     @OA\Items(ref="#/components/schemas/SeoEventResource")
     * )
     */
    public $seo_events;

}
