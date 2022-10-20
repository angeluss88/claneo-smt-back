<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="TableConfig store Data",
 *     description="TableConfigStore Data",
 *     required={"table", "columns"}
 * )
 */
class TableConfigStoreRequest
{
    /**
     * @OA\Property(
     *     title="table",
     *     description="Table name",
     *     example="users",
     * )
     *
     * @var string
     */
    public $table;

    /**
     * @OA\Property(
     *     title="columns",
     *     description="Columns, separated by comma",
     *     example="ID,title,descripton",
     * )
     *
     * @var string
     */
    public $columns;
 }
