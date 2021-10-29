<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Import Strategy",
 *     description="Import Strategy",
 * )
 */
class ImportStrategy
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
     *     title="project_id",
     *     description="ID of project",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $project_id;
}
