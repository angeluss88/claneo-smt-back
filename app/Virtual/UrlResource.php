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
     *     title="ecom_conversion_rate",
     *     description="ecom_conversion_rate",
     *     example="null",
     * )
     *
     * @var string
     */
    public $ecom_conversion_rate;

    /**
     * @OA\Property(
     *     title="revenue",
     *     description="revenue",
     *     example="null",
     * )
     *
     * @var string
     */
    public $revenue;

    /**
     * @OA\Property(
     *     title="avg_order_value",
     *     description="avg_order_value",
     *     example="null",
     * )
     *
     * @var string
     */
    public $avg_order_value;

    /**
     * @OA\Property(
     *     title="bounce_rate",
     *     description="bounce rate",
     *     example="null",
     * )
     *
     * @var string
     */
    public $bounce_rate;

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
     *     title="avgConvRate",
     *     description="Average conversion rate",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $avgConvRate;

    /**
     * @OA\Property(
     *     title="avgRevenue",
     *     description="Average revenue",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $avgRevenue;

    /**
     * @OA\Property(
     *     title="avgOrderValue",
     *     description="Average order value",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgOrderValue;

    /**
     * @OA\Property(
     *     title="avgBounceRate",
     *     description="Average bounce rate",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgBounceRate;

    /**
     * @OA\Property(
     *     title="avgPosition",
     *     description="Average position",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgPosition;

    /**
     * @OA\Property(
     *     title="avgClicks",
     *     description="Average clicks",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgClicks;

    /**
     * @OA\Property(
     *     title="avgImpressions",
     *     description="Average impressions",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgImpressions;

    /**
     * @OA\Property(
     *     title="avgCtr",
     *     description="Average CTR",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgCtr;

    /**
     * @OA\Property(
     *     title="avgSearchVolume",
     *     description="Average Search Volume",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgSearchVolume;

    /**
     * @OA\Property(
     *     title="avgTrafficPotential",
     *     description="Average Traffic Potential",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $avgTrafficPotential;

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
