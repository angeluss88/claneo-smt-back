<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableConfigRequest;
use App\Http\Requests\TableConfigStoreRequest;
use App\Models\TableConfig;
use Illuminate\Http\Response;

class TableConfigController extends Controller
{
    /**
     * @OA\Get(
     *     path="/table_config/{table}?user_id={user_id}",
     *     operationId="table_config_index",
     *     tags={"Account"},
     *     summary="User Table Config",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/TableConfigResource")
     *         )),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Parameter(
     *         name="table",
     *         in="path",
     *         description="Table name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="The user ID",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param TableConfigRequest $request
     * @return Response
     */
    public function index(TableConfigRequest $request): Response
    {
        $userId = $request->user_id && $request->user_id !== ',' ? $request->user_id : auth()->id();
        $config = TableConfig::whereUserId($userId)->whereTableId($request->table)->orderBy('position');

        $data = [];
        foreach ($config->get() as $item) {
            $data[] = [
                'position' => $item->position,
                'column' => $item->column,
            ];
        }

        return response([
            'data' => $data,
        ], 200);
    }

    /**
     *
     * @OA\Post (
     *     path="/table_config",
     *     operationId="table_config_store",
     *     tags={"Account"},
     *     summary="Create Config item",
     *     @OA\Response(
     *         response="204",
     *         description="Everything is fine",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TableConfigStoreRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param TableConfigStoreRequest $request
     * @return Response
     */
    public function store(TableConfigStoreRequest $request): Response
    {
        $fields = $request->validated();

        $columns = explode(',', $fields['columns']);

        $insert = [];

        $position = 1;
        foreach ($columns as $column) {
            if (empty($column)) {
                continue;
            }
            $insert[] = [
                'user_id' => auth()->id(),
                'table_id' => $fields['table'],
                'column' => $column,
                'position' => $position++,
            ];
        }

        TableConfig::whereUserId(auth()->id())->whereTableId($fields['table'])->delete();
        TableConfig::insert($insert);

        return response([], 204);
    }

    /**
     * @OA\Delete (
     *     path="/table_config/{table}",
     *     operationId="table_config_delete",
     *     tags={"Account"},
     *     summary="Delete Table Config",
     *     @OA\Response(
     *         response="204",
     *         description="Everything is fine",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Parameter(
     *         name="table",
     *         in="path",
     *         description="The table name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param string $table
     * @return Response
     */
    public function destroy(string $table): Response
    {
        TableConfig::whereUserId(auth()->id())->whereTableId($table)->delete();

        return response([], 204);
    }
}
