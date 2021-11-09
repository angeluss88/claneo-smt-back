<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Keyword;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class KeywordController extends Controller
{
    /**
     * @OA\Get(
     *     path="/keywords?page={page}&count={count}",
     *     operationId="keywords_index",
     *     tags={"Keywords"},
     *     summary="List of keywords",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="keywords",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(
     *                     @OA\Property(
     *                          property="current_page",
     *                          type="integer",
     *                          example=1,
     *                     ),
     *                     @OA\Property(
     *                          property="data",
     *                          type="array",
     *                          collectionFormat="multi",
     *                          @OA\Items(ref="#/components/schemas/KeywordResource")
     *                     )
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/keywords?page=1",
     *             ),
     *             @OA\Property(
     *                 property="from",
     *                 type="integer",
     *                 example=1,
     *             ),
     *             @OA\Property(
     *                 property="last_page",
     *                 type="integer",
     *                 example=4,
     *             ),
     *             @OA\Property(
     *                 property="last_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/keywords?page=4",
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 example={{
     *                     "url": null,
     *                     "label": "&laquo; Previous",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/keywords?page=1",
     *                     "label": "1",
     *                     "active": true
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/keywords?page=2",
     *                     "label": "2",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/keywords?page=3",
     *                     "label": "3",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/keywords?page=4",
     *                     "label": "4",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/keywords?page=2",
     *                     "label": "Next &raquo;",
     *                     "active": false
     *                 }},
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="label",
     *                         type="string",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="active",
     *                         type="boolean",
     *                         example=""
     *                      ),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="next_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/keywords?page=2",
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/keywords",
     *             ),
     *             @OA\Property(
     *                 property="per_page",
     *                 type="integer",
     *                 example=1,
     *             ),
     *             @OA\Property(
     *                 property="prev_page_url",
     *                 type="string",
     *                 example=null,
     *             ),
     *             @OA\Property(
     *                 property="to",
     *                 type="integer",
     *                 example=1,
     *             ),
     *             @OA\Property(
     *                 property="total",
     *                 type="integer",
     *                 example=4,
     *         )),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="The page",
     *         required=false,
     *         example=1,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="count",
     *         in="path",
     *         description="Count of rows",
     *         required=false,
     *         example=10,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $count = $request->count == '{count}' ? 10 : $request->count;
        return response([
            'keywords' => Keyword::with(['urls', 'events'])->paginate($count),
        ], 200);
    }

    /**
     * @OA\Post (
     *     path="/keywords",
     *     operationId="keywords_store",
     *     tags={"Keywords"},
     *     summary="Create Keyword",
     *     @OA\Response(
     *         response="201",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="Keyword",
     *             type="object",
     *             ref="#/components/schemas/KeywordResource",
     *         ))
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
     *         @OA\JsonContent(ref="#/components/schemas/KeywordRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * ).
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $fields = $request->validate([
            'keyword'                   => 'required|unique:keywords,keyword|string|max:255',
            'search_volume'             => 'required|integer',
            'search_volume_clustered'   => 'integer',
            'current_ranking_url'       => 'max:255',
            'featured_snippet_keyword'  => [
                Rule::in(['ja', 'nein', 'yes', 'no', 'Ja', 'Nein', 'Yes', 'No']),
            ],
            'featured_snippet_owned'  => [
                Rule::in(['ja', 'nein', 'yes', 'no', 'Ja', 'Nein', 'Yes', 'No']),
            ],
            'current_ranking_position'  => [
                'required',
                Rule::in(array_merge(range(1, 100),
                   [ 'Nicht in Top 100', 'nicht in Top 100', 'Not in Top 100', 'not in Top 100'])),
            ],
            'search_intention'  => [
                Rule::in(['informational', 'Informational', 'transaktional', 'Transaktional', 'transactional',
                    'Informational/transaktional', 'informational/transaktional', 'informational/transactional', 'Informational/transactional',
                    'navigational', 'Navigational']),
            ],
        ]);

        unset($fields['import_id']);

        $fields = $this->replaceFields($fields);

        $keyword = Keyword::create($fields);

        Event::create([
            'user_id' => Auth::user()->id,
            'entity_type' => Keyword::class,
            'entity_id' => $keyword->id,
            'action' => Event::CREATE_ACTION,
            'data' =>  $fields,
        ]);

        return response([
            'keyword' => $keyword,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/keywords/{keyword}",
     *     operationId="keywords_show",
     *     tags={"Keywords"},
     *     summary="Show Keyword",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="keyword",
     *             type="object",
     *             ref="#/components/schemas/KeywordResource",
     *         )),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         description="The keyword id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param  Keyword  $keyword
     * @return Response
     */
    public function show(Keyword $keyword): Response
    {
        return response([
            'keyword' => Keyword::with(['urls', 'events'])->find($keyword->id),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/keywords/{keyword}",
     *     operationId="keywords_update",
     *     tags={"Keywords"},
     *     summary="Update Keyword",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="keyword",
     *             type="object",
     *             ref="#/components/schemas/KeywordResource",
     *         )),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         description="The keyword id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/KeywordRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @param  Keyword $keyword
     * @return Response
     */
    public function update(Request $request, Keyword $keyword): Response
    {
        $fields = $request->validate([
            'keyword' => [
                'string',
                'max:255',
                Rule::unique('keywords')->ignore($keyword->id),
            ],
            'search_volume' => 'integer',
            'search_volume_clustered' => 'integer',
            'current_ranking_url' => 'max:255',
            'featured_snippet_keyword' => [
                Rule::in(['ja', 'nein', 'yes', 'no', 'Ja', 'Nein', 'Yes', 'No']),
            ],
            'featured_snippet_owned' => [
                Rule::in(['ja', 'nein', 'yes', 'no', 'Ja', 'Nein', 'Yes', 'No']),
            ],
            'current_ranking_position' => [
                Rule::in(array_merge(range(1, 100), [ 'Nicht in Top 100', 'nicht in Top 100', 'Not in Top 100', 'not in Top 100'])),
            ],
            'search_intention' => [
                Rule::in(['informational', 'Informational', 'transaktional', 'Transaktional', 'transactional',
                    'Informational/transaktional', 'informational/transaktional', 'informational/transactional', 'Informational/transactional',
                    'navigational', 'Navigational']),
            ],
        ]);

        unset($fields['import_id']);

        $fields = $this->replaceFields($fields);

        $attributes = $keyword->getAttributes();

        $keyword->fill($fields)->save();

        Event::create([
            'user_id' => Auth::user()->id,
            'entity_type' => Keyword::class,
            'entity_id' => $keyword->id,
            'action' => Event::UPDATE_ACTION,
            'data' =>  $fields,
            'oldData' => $attributes,
        ]);

        return response([
            'keyword' => $keyword,
        ], 200);
    }

    protected function replaceFields ($fields)
    {
        if ( isset($fields['featured_snippet_keyword']) ) {
            $fields['featured_snippet_keyword'] = strtolower($fields['featured_snippet_keyword']);
            $fields['featured_snippet_keyword'] = str_replace('ja', 'yes', $fields['featured_snippet_keyword']);
            $fields['featured_snippet_keyword'] = str_replace('nein', 'no', $fields['featured_snippet_keyword']);
        }

        if ( isset($fields['featured_snippet_owned']) ) {
            $fields['featured_snippet_owned'] = strtolower($fields['featured_snippet_owned']);
            $fields['featured_snippet_owned'] = str_replace('ja', 'yes', $fields['featured_snippet_owned']);
            $fields['featured_snippet_owned'] = str_replace('nein', 'no', $fields['featured_snippet_owned']);
        }

        $fields['current_ranking_position'] = str_replace('Nicht', 'Not', $fields['current_ranking_position']);
        $fields['current_ranking_position'] = str_replace('nicht', 'Not', $fields['current_ranking_position']);

        if ( isset($fields['search_intention']) ) {
            $fields['search_intention'] = strtolower($fields['search_intention']);
            $fields['search_intention'] = str_replace('transaktional', 'transactional', $fields['search_intention']);
        }

        return $fields;
    }

    /**
     * @OA\Delete (
     *     path="/keywords/{keyword}",
     *     operationId="keywords_delete",
     *     tags={"Keywords"},
     *     summary="Delete Keyword",
     *     @OA\Response(
     *         response="204",
     *         description="Everything is fine",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         description="The keyword id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param  Keyword $keyword
     * @return Response
     */
    public function destroy(Keyword $keyword): Response
    {
        $keyword->delete();
        Event::create([
            'user_id' => Auth::user()->id,
            'entity_type' => Keyword::class,
            'entity_id' => $keyword->id,
            'action' => Event::DELETE_ACTION,
            'data' =>  [],
        ]);

        return response([], 204);
    }
}
