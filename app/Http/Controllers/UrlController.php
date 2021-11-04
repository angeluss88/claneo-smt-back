<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\URL;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UrlController extends Controller
{
    /**
     * @OA\Get(
     *     path="/urls",
     *     operationId="urls_index",
     *     tags={"URLs"},
     *     summary="List of urls",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="urls",
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
     *                          @OA\Items(ref="#/components/schemas/UrlResource")
     *                     )
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/urls?page=1",
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
     *                 example="http://127.0.0.1:8000/api/urls?page=4",
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 example={{
     *                     "url": null,
     *                     "label": "&laquo; Previous",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urls?page=1",
     *                     "label": "1",
     *                     "active": true
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urls?page=2",
     *                     "label": "2",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urls?page=3",
     *                     "label": "3",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urls?page=4",
     *                     "label": "4",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urls?page=2",
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
     *                 example="http://127.0.0.1:8000/api/urls?page=2",
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/urls",
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
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @return Response
     */
    public function index(): Response
    {
        return response([
            'users' => URL::with(['project', 'keywords'])->paginate(5),
        ], 200);
    }

    /**
     * @OA\Post (
     *     path="/urls",
     *     operationId="urls_store",
     *     tags={"URLs"},
     *     summary="Create URL",
     *     @OA\Response(
     *         response="201",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="url",
     *             type="object",
     *             ref="#/components/schemas/UrlResource",
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
     *         @OA\JsonContent(ref="#/components/schemas/UrlCreateRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $fields = $request->validate([
            'url'                   => 'required|unique:urls,url|string|max:255',
            'project_id'            => 'integer',
            'status' => [
                'required',
                Rule::in(['301', '200', 301, 200, 'NEW', 'new', 'NEU', 'neu']),
            ],
            'main_category'         => 'required|string|max:255',
            'sub_category'          => 'string|max:255',
            'sub_category2'         => 'string|max:255',
            'sub_category3'         => 'string|max:255',
            'sub_category4'         => 'string|max:255',
            'sub_category5'         => 'string|max:255',
            'ecom_conversion_rate'  => 'string|max:255',
            'revenue'               => 'string|max:255',
            'avg_order_value'       => 'string|max:255',
            'bounce_rate'           => 'string|max:255',
            'page_type'             => 'string|max:255',
        ]);

        if(in_array($fields['status'], ['new', 'NEU', 'neu'])) {
            $fields['status'] = 'NEW';
        }

        $url = URL::create($fields);

        return response([
            'url' => $url,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/urls/{url}",
     *     operationId="urls_show",
     *     tags={"URLs"},
     *     summary="Show URL",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="url",
     *             type="object",
     *             ref="#/components/schemas/UrlResource",
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
     *         name="url",
     *         in="path",
     *         description="The url id",
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
     * @param  URL $url
     * @return Response
     */
    public function show(URL $url): Response
    {
        return response([
            'url' => URL::with(['project', 'keywords'])->find($url->id),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/urls/{url}",
     *     operationId="urls_update",
     *     tags={"URLs"},
     *     summary="Update URL",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="url",
     *             type="object",
     *             ref="#/components/schemas/UrlResource",
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
     *         name="url",
     *         in="path",
     *         description="The url id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UrlRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @param  URL $url
     * @return Response
     */
    public function update(Request $request, URL $url): Response
    {
        $fields = $request->validate([
            'url' => [
                'string',
                'max:255',
                Rule::unique('urls')->ignore($url->id),
            ],
            'status' => [
                Rule::in(['301', '200', 301, 200, 'NEW', 'new', 'NEU', 'neu']),
            ],
            'project_id'            => 'integer',
            'main_category'         => 'string|max:255',
            'sub_category'          => 'string|max:255',
            'sub_category2'         => 'string|max:255',
            'sub_category3'         => 'string|max:255',
            'sub_category4'         => 'string|max:255',
            'sub_category5'         => 'string|max:255',
            'ecom_conversion_rate'  => 'string|max:255',
            'revenue'               => 'string|max:255',
            'avg_order_value'       => 'string|max:255',
            'bounce_rate'           => 'string|max:255',
            'page_type'             => 'string|max:255',
            'keywords'              => 'array',
        ]);

        if(isset($fields['status']) && in_array($fields['status'], ['new', 'NEU', 'neu'])) {
            $fields['status'] = 'NEW';
        }

        $url->fill($fields)->save();

        if(isset($fields['keywords'])) {
            $pivot = [];
            foreach ($fields['keywords'] as $value) {
                if(!isset($value['id'])) {
                    return response([
                        'message' => 'Keywords array should contain keyword_id',
                    ], 422);
                }
                $v = $value['id'];
                unset($value['id']);
                $pivot[$v] = $value;
            }

            foreach ($pivot as $k_id => $data) {
                $attributes = [];
                if(isset($data['clicks'])){
                    $attributes['clicks'] = $data['clicks'];
                }
                if(isset($data['impressions'])){
                    $attributes['impressions'] = $data['impressions'];
                }
                if(isset($data['ctr'])){
                    $attributes['ctr'] = $data['ctr'];
                }

                $url->keywords()->updateExistingPivot($k_id, $attributes);
            }
        }

        return response([
            'url' => URL::with('keywords')->find($url->id),
        ], 200);
    }

    /**
     * @OA\Delete (
     *     path="/urls/{url}",
     *     operationId="urls_delete",
     *     tags={"URLs"},
     *     summary="Delete URL",
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
     *         name="url",
     *         in="path",
     *         description="The url id",
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
     * @param  URL  $url
     * @return Response
     */
    public function destroy(URL $url): Response
    {
        $url->delete();

        return response([], 204);
    }
}
