<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="TableConfigResource",
 *     description="TableConfig Resource",
 * )
 */
class TableConfigResource
{

    /**
     * @OA\Property(
     *     title="position",
     *     description="Column position",
     *     example=0,
     * )
     *
     * @var integer
     */
    public $position;

    /**
     * @OA\Property(
     *     title="column",
     *     description="name of column",
     *     example="ID",
     * )
     *
     * @var string
     */
    public $column;

}
