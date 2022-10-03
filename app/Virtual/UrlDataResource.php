<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="UrlDataResource Resource",
 *     description="UrlDataResource Resource",
 * )
 */
class UrlDataResource
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
     *     title="url_id",
     *     description="url_id",
     *     example=1
     * )
     *
     *  @var integer
     */
    public $url_id;

    /**
     * @OA\Property(
     *     title="ecom_conversion_rate",
     *     description="ecom_conversion_rate",
     *     example="10",
     * )
     *
     * @var string
     */
    public $ecom_conversion_rate;

    /**
     * @OA\Property(
     *     title="revenue",
     *     description="revenue",
     *     example="10",
     * )
     *
     * @var string
     */
    public $revenue;

    /**
     * @OA\Property(
     *     title="avg_order_value",
     *     description="avg_order_value",
     *     example="10",
     * )
     *
     * @var string
     */
    public $avg_order_value;

    /**
     * @OA\Property(
     *     title="bounce_rate",
     *     description="bounce_rate",
     *     example="10",
     * )
     *
     * @var string
     */
    public $bounce_rate;

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

}
