<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\ImportKeywordsRequest;
use App\Http\Requests\KeywordStoreRequest;
use App\Http\Requests\KeywordUpdateRequest;
use App\Models\Event;
use App\Models\Keyword;
use App\Models\URL;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * @param BaseIndexRequest $request
     * @return Response
     */
    public function index(BaseIndexRequest $request): Response
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
     * @param KeywordStoreRequest $request
     * @return Response
     */
    public function store(KeywordStoreRequest $request): Response
    {
        $fields = $request->validated();
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
     *         @OA\JsonContent(ref="#/components/schemas/KeywordUpdateRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param KeywordUpdateRequest $request
     * @param Keyword $keyword
     * @return Response
     */
    public function update(KeywordUpdateRequest $request, Keyword $keyword): Response
    {
        $fields = $request->validated();
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

        if ( isset($fields['current_ranking_position']) ) {
            $fields['current_ranking_position'] = str_replace('Nicht', 'Not', $fields['current_ranking_position']);
            $fields['current_ranking_position'] = str_replace('nicht', 'Not', $fields['current_ranking_position']);
        }

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


    /**
     *
     * @OA\Post (
     *     path="/import_keywords",
     *     operationId="import_keywords",
     *     tags={"Keywords"},
     *     summary="Import Keywords",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="imported",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/ImportKeywordsResponse"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid.",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ImportKeywordsRequest")
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param ImportKeywordsRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function import(ImportKeywordsRequest $request): JsonResponse
    {
        set_time_limit (0);

        try {
            $url = URL::findOrFail($request->get('url_id'));
            $path = $request->file('file')->getRealPath();
            $csv = $this->parseCsv($path, ';', 2);

            if (isset($csv[0]) && (count($csv[0]) < 3)) {
                $csv = $this->parseCsv($path, ',', 2);
            }

            $headers = empty($csv) ? [] : array_flip(array_shift($csv));

            $keys = $this->getKeys($url, $headers);

            // empty keys mean that we don't have any of field name: field_de, field_en or *field_en
            if (empty($keys)) {
                return response()->json([
                    'message' => 'Keyword: this field is required',
                ], 422);
            }

            $required_fields = ['keyword', 'search_volume', 'current_ranking_position'];

            foreach ($required_fields as $required_field) {
                if (!isset($headers[$keys[$required_field]])) {
                    return response()->json([
                        'message' => $keys[$required_field] . ': this column is required',
                    ], 422);
                }
            }

            $keywords = []; // keywords array to do mass insert after parsing.
            $failed_rows = []; // any errors during parsing
            $keywordsForCheck = [];

            // begin parsing process
            foreach ($csv as $row_number => $row) {
                $keyword = [];

                // begin validation process
                // if we don't have any of required fields - stop parse this row and add item to failed rows
                foreach ($required_fields as $required_field) {
                    if (!$row[$headers[$keys[$required_field]]] && $row[$headers[$keys[$required_field]]] !== "0") {
                        $failed_rows[] = [
                            'row' => $row_number,
                            'attribute' => $keys[$required_field],
                            'message' => $keys[$required_field] . ': this field is required in row #' . $row_number,
                        ];
                        continue 2;
                    }
                }

                $keyword['keyword'] = $row[$headers[$keys['keyword']]];
                $keywordsForCheck[] = $row[$headers[$keys['keyword']]];

                $keyword['search_volume'] = (int) str_replace('.', '', $row[$headers[$keys['search_volume']]]);
                $keyword['search_volume_clustered'] = isset($headers[$keys['sv_clustered']]) ? $row[$headers[$keys['sv_clustered']]] : null;
                $keyword['search_volume_clustered'] = (int)str_replace('.', '', $keyword['search_volume_clustered']);

                $keyword['current_ranking_position'] = $row[$headers[$keys['current_ranking_position']]];
                $keyword['current_ranking_position'] = str_replace('Nicht', 'Not', $keyword['current_ranking_position']);
                $keyword['current_ranking_position'] = str_replace('nicht', 'Not', $keyword['current_ranking_position']);

                // add keyword item to $keywords by keyword key (the same keywords will be the same item)
                $keywords[$row[$headers[$keys['keyword']]]] = $keyword;
            }
            // end parsing process

            // if we have fails during parsing - return all the errors
            if (!empty($failed_rows)) {
                return response()->json([$failed_rows], 422);
            }

            $existsKeywords = Keyword::whereIn('keyword', $keywordsForCheck)->count();

            // if we don't have errors - create entities
            // save all prepared urls from $urls
            Keyword::upsert(array_values($keywords), ['keyword']);

            $kwIds = Keyword::whereIn('keyword', $keywords)->get('id');
            URL::where('url', $url->url)->first()->keywords()->syncWithoutDetaching($kwIds);

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return response()->json([
            'new_keywords' => count($keywords) - $existsKeywords,
            'updated_keywords' => $existsKeywords,
        ], 201);
    }

    /**
     * get keys mapping
     *
     * @param URL $url
     * @param $headers
     * @return array
     */
    protected function getKeys(URL $url, $headers): array
    {
        // check if we have DE field names
        if(isset($headers[Keyword::KEYWORD])) {
            return  [
                'keyword' => Keyword::KEYWORD,
                'search_volume' => Keyword::SEARCH_VOLUME,
                'sv_clustered' => Keyword::SV_CLUSTERED,
                'current_ranking_position' => Keyword::CURRENT_RANKING_POSITION_3,
            ];
        }

        // check if we have EN field names
        if(isset($headers[Keyword::KEYWORD_EN])) {
            return [
                'keyword' => Keyword::KEYWORD_EN,
                'search_volume' => Keyword::SEARCH_VOLUME_EN,
                'sv_clustered' => Keyword::SV_CLUSTERED_EN,
                'current_ranking_position' => Keyword::CURRENT_RANKING_POSITION_EN,
            ];
        }

        // check if we have EN field names with '*' sign (required fields)
        if(isset($headers['*' . Keyword::KEYWORD_EN])) {
            return [
                'keyword' => '*' . Keyword::KEYWORD_EN,
                'search_volume' => '*' . Keyword::SEARCH_VOLUME_EN,
                'sv_clustered' => Keyword::SV_CLUSTERED_EN,
                'current_ranking_position' => '*' . Keyword::CURRENT_RANKING_POSITION_EN,
            ];
        }

        // if we still here - we don't have any of desired field names (or at least URL column with right name),
        // so we return empty keys
        return [];
    }

    /**
     * @OA\Get(
     *     path="/import_keywords_example",
     *     operationId="import_keywords_example",
     *     tags={"Keywords"},
     *     summary="Download Keywords import example file",
     *     @OA\Response(
     *         response="200",
     *         description="Download file",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @return BinaryFileResponse
     */
    public function example(): BinaryFileResponse
    {
        $filePath = public_path(). "/files/keywords_example.csv";

        $headers = array(
            'Content-Type: text/csv',
        );

        return response()->download($filePath, 'keywords_example.csv', $headers);
    }
}
