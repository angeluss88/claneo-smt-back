<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Import Keywords Response",
 *     description="Import Keywords Response",
 * )
 */
class ImportKeywordsResponse
{
    /**
     * @OA\Property(
     *     title="new_keywords",
     *     description="new_keywords",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $new_keywords;
    /**
     * @OA\Property(
     *     title="updated_keywords",
     *     description="updated_keywords",
     *     example=10,
     * )
     *
     * @var integer
     */
    public $updated_keywords;
 }
