<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Import Keywords Request",
 *     description="Import Keywords Request",
 * )
 */
class ImportKeywordsRequest
{
    /**
     * @OA\Property(
     *     description="csv file to upload",
     *     type="file",
     *     format="file"
     * )
     */
    public $file;

    /**
     * @OA\Property(
     *     title="url_id",
     *     description="ID of url",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $url_id;
}
