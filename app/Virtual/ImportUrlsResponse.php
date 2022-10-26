<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Import Urls Response",
 *     description="Import Urls Response",
 * )
 */
class ImportUrlsResponse
{
    /**
     * @OA\Property(
     *     title="new_urls",
     *     description="new_urls",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $new_urls;
    /**
     * @OA\Property(
     *     title="updated_urls",
     *     description="updated_urls",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $updated_urls;
 }
