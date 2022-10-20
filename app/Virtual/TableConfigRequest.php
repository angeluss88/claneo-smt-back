<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="TableConfig Get Data",
 *     description="TableConfig Data",
 *     required={"table", "columns"}
 * )
 */
class TableConfigRequest
{
    /**
     * @OA\Property(
     *     title="table",
     *     description="Table name",
     *     example="user",
     * )
     *
     * @var string
     */
    public $table;

    /**
     * @OA\Property(
     *     title="columns",
     *     description="columns",
     *     example="ID,title,descripton",
     * )
     *
     * @var string
     */
    public $columns;
 }
