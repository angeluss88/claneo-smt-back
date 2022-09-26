<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="URL Resource with aggregations",
 *     description="URL Resource with aggregation",
 * )
 */
class UrlAggrResource
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
     *     title="aggrConvRate",
     *     description="aggrConvRate",
     *     example=0,
     * )
     *
     * @var string
     */
    public $aggrConvRate;

    /**
     * @OA\Property(
     *     title="aggrRevenue",
     *     description="aggrRevenue",
     *     example=0,
     * )
     *
     * @var string
     */
    public $aggrRevenue;

    /**
     * @OA\Property(
     *     title="aggrOrderValue",
     *     description="aggrOrderValue",
     *     example=0,
     * )
     *
     * @var string
     */
    public $aggrOrderValue;

    /**
     * @OA\Property(
     *     title="aggrBounceRate",
     *     description="aggrBounceRate",
     *     example=0,
     * )
     *
     * @var string
     */
    public $aggrBounceRate;

    /**
     * @OA\Property(
     *     title="totalUrlKeywordDataCount",
     *     description="Number of keywords",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $totalUrlKeywordDataCount;

    /**
     * @OA\Property(
     *     title="aggrPosition",
     *     description="aggrPosition",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $aggrPosition;

    /**
     * @OA\Property(
     *     title="aggrClicks",
     *     description="aggrClicks",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $aggrClicks;

    /**
     * @OA\Property(
     *     title="aggrImpressions",
     *     description="aggrImpressions",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $aggrImpressions;

    /**
     * @OA\Property(
     *     title="aggrCtr",
     *     description="aggrCtr",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $aggrCtr;

    /**
     * @OA\Property(
     *     title="aggrSearchVolume",
     *     description="aggrSearchVolume",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $aggrSearchVolume;

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
}
