<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="URL Request",
 *     description="URL Request",
 * )
 */
class UrlRequest
{
    /**
     * @OA\Property(
     *     title="url",
     *     description="URL",
     *     example="https://www.site.com/page",
     * )
     *
     * @var string
     */
    public $url;

    /**
     * @OA\Property(
     *     title="status",
     *     description="URL status",
     *     example="NEW",
     * )
     *
     * @var string
     */
    public $status;

    /**
     * @OA\Property(
     *     title="main_category",
     *     description="Main Category",
     *     example="Main",
     * )
     *
     * @var string
     */
    public $main_category;

    /**
     * @OA\Property(
     *     title="sub_category",
     *     description="Sub Category",
     *     example="Sub",
     * )
     *
     * @var string
     */
    public $sub_category;

    /**
     * @OA\Property(
     *     title="sub_category2",
     *     description="Sub Category2",
     *     example="Sub2",
     * )
     *
     * @var string
     */
    public $sub_category2;

    /**
     * @OA\Property(
     *     title="sub_category3",
     *     description="Sub Category3",
     *     example="Sub3",
     * )
     *
     * @var string
     */
    public $sub_category3;

    /**
     * @OA\Property(
     *     title="sub_category4",
     *     description="Sub Category4",
     *     example="Sub4",
     * )
     *
     * @var string
     */
    public $sub_category4;

    /**
     * @OA\Property(
     *     title="sub_category5",
     *     description="Sub Category5",
     *     example="Sub5",
     * )
     *
     * @var string
     */
    public $sub_category5;

    /**
     * @OA\Property(
     *     title="ecom_conversion_rate",
     *     description="E-Commerce conversion rate",
     *     example="ecom_conversion_rate",
     * )
     *
     * @var string
     */
    public $ecom_conversion_rate;

    /**
     * @OA\Property(
     *     title="revenue",
     *     description="Revenue",
     *     example="revenue",
     * )
     *
     * @var string
     */
    public $revenue;

    /**
     * @OA\Property(
     *     title="avg_order_value",
     *     description="Average Order Value",
     *     example="avg_order_value",
     * )
     *
     * @var string
     */
    public $avg_order_value;

    /**
     * @OA\Property(
     *     title="bounce_rate",
     *     description="Bounce Rate",
     *     example="bounce_rate",
     * )
     *
     * @var string
     */
    public $bounce_rate;

    /**
     * @OA\Property(
     *     title="page_type",
     *     description="page_type",
     *     example="page_type",
     * )
     *
     * @var string
     */
    public $page_type;

    /**
     *     @OA\Property(
     *         type="array",
     *         example={{
     *            "id": 1,
     *            "clicks": "1111",
     *            "impressions": "1111",
     *            "ctr": "8011118",
     *         }, {
     *            "id": 2,
     *            "clicks": "2222",
     *            "impressions": "2222",
     *             "ctr": "232323"
     *         }},
     *         @OA\Items(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="clicks",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="impressions",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="ctr",
     *                 type="string",
     *                 example=""
     *             ),
     *         ),
     *     ),
     */
    public $keywords;
}
