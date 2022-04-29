<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\URL;
use App\Models\UrlData;
use App\Models\UrlKeywordData;
use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UrlController extends Controller
{
    /**
     * @OA\Get(
     *     path="/urls?page={page}&count={count}&sort={sort}&keywords={keywords}&import_date={import_date}&categories={categories}&project_id={project_id}&import_id={import_id}",
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
     *     @OA\Parameter(
     *         name="keywords",
     *         in="path",
     *         description="Keyword(s)",
     *         required=false,
     *         example="kw1,kw2,kw3",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="import_date",
     *         in="path",
     *         description="Import date range (Y.m.d-Y.m.d)",
     *         required=false,
     *         example="2021.11.03-2021.12.03",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="categories",
     *         in="path",
     *         description="Categories filter",
     *         required=false,
     *         example="main_category:Main,sub_category:Webentwickler,sub_category2:München",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project filter",
     *         required=false,
     *         example=1,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="import_id",
     *         in="path",
     *         description="Import_id filter",
     *         required=false,
     *         example=1,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="path",
     *         description="Sort by field. Format: 'field.direction'. Direction must be asc or desc",
     *         required=false,
     *         example="url.desc",
     *         @OA\Schema(
     *             type="string",
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

        $url = URL::with(['project', 'events', 'keywords', 'urlData', 'urlKeywordData']);

        if($request->keywords && $request->keywords !== '{keywords}') {
            $keywords = explode(',', $request->keywords);

            foreach ($keywords as $k => $keyword) {
                $keywords[$k] = trim(htmlspecialchars($keyword));
            }

            $url->whereHas('keywords', function ($q) use ($keywords) {
                $q->whereIn('keyword', $keywords);
            });
        }

        if ($request->project_id && $request->project_id !== '{project_id}') {
            $url->where('project_id', (int) $request->project_id);
        }

        if ($request->import_id && $request->import_id !== '{import_id}') {
            $url->where('import_id', (int) $request->import_id);
        }

        if($request->categories && $request->categories !== '{categories}') {
            $categories = explode(',', $request->categories);
            $categoryFields = [
                'main_category',
                'sub_category',
                'sub_category2',
                'sub_category3',
                'sub_category4',
                'sub_category5',
            ];
            $categoriesFilter = [];

            foreach ($categories as $category) {
                $category = explode(':', $category);

                if( count($category) == 2
                    && isset($category[0]) && isset($category[1])
                    && in_array($category[0], $categoryFields)
                ) {
                    $categoriesFilter[$category[0]] = trim( $category[1] );
                }
            }

            foreach ($categoriesFilter as $field => $category) {
                $url->where($field, $category);
            }

        }

        if($request->import_date) {
            $url = $this->filterUrlsByDate($request->import_date, $url);
        }

        if($request->sort && $request->sort !== '{sort}') {
            $sort = explode('.', $request->sort);

            if(isset($sort[0]) && isset($sort[1]) && in_array($sort[1], ['asc', 'desc'])) {
                $url->orderBy($sort[0], $sort[1]);
            }
        }

        $url->withCount('keywords');
        foreach ($url = $url->paginate($count) as $item) {
            $avgConvRate = 0;
            $avgRevenue = 0;
            $avgOrderValue = 0;
            $avgBounceRate = 0;
            $avgSearchVolume = 0;

            $avgPosition = 0;
            $avgClicks = 0;
            $avgImpressions = 0;
            $avgCtr = 0;

            $urlDataCount = 0;
            $urlKeywordDataCount = 0;
            /**
             * @var UrlData $urlData
             * @var URL $item
             */
            foreach ($item->urlData as $urlData) {
                $avgConvRate += $urlData->ecom_conversion_rate;
                $avgRevenue += $urlData->revenue;
                $avgOrderValue += $urlData->avg_order_value;
                $avgBounceRate += $urlData->bounce_rate;

                $urlDataCount++;
            }
            if($urlDataCount !== 0) {
                $avgConvRate /= $urlDataCount;
                $avgOrderValue /= $urlDataCount;
                $avgBounceRate /= $urlDataCount;
            }

            /**
             * @var UrlKeywordData $urlKeywordData
             */
            foreach ($item->urlKeywordData as $urlKeywordData) {
                $avgPosition += $urlKeywordData->position;
                $avgClicks += $urlKeywordData->clicks;
                $avgImpressions += $urlKeywordData->impressions;
                $avgCtr += $urlKeywordData->ctr;

                $urlKeywordDataCount++;
            }
            if($urlKeywordDataCount !== 0) {
                $avgPosition /= $urlKeywordDataCount;
                $avgCtr /= $urlKeywordDataCount;
            }

            foreach ($item->keywords as $keyword) {
                $avgSearchVolume += $keyword->search_volume;
            }

            $item->setAttribute('avgConvRate', $avgConvRate);
            $item->setAttribute('avgRevenue', $avgRevenue);
            $item->setAttribute('avgOrderValue', $avgOrderValue);
            $item->setAttribute('avgBounceRate', $avgBounceRate);

            $item->setAttribute('avgPosition', $avgPosition);
            $item->setAttribute('avgClicks', $avgClicks);
            $item->setAttribute('avgImpressions', $avgImpressions);
            $item->setAttribute('avgCtr', $avgCtr);

            $item->setAttribute('avgSearchVolume', $avgSearchVolume);
            $item->setAttribute('avgTrafficPotential', 'Coming soon...');
        }

        return response([
            'urls' => $url,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/urls_aggregation?project_id={project_id}&date_range={date_range}",
     *     operationId="urls_aggregation",
     *     tags={"URLs"},
     *     summary="URL aggregation data",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(
     *                     @OA\Property(
     *                          property="avgConvRate",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="avgRevenue",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="avgOrderValue",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="avgBounceRate",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrPosition",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrClicks",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrImpressions",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrCtr",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrSearchVolume",
     *                          type="integer",
     *                          example=0,
     *                     )
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Parameter(
     *         name="date_range",
     *         in="path",
     *         description="Date range (Y.m.d-Y.m.d)",
     *         required=false,
     *         example="2021.11.03-2021.12.03",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="Project filter",
     *         required=false,
     *         example=1,
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
    public function urlAggregation (Request $request): Response
    {
        $url = URL::with(['project', 'events', 'keywords', 'urlData', 'urlKeywordData']);

        if ($request->project_id && $request->project_id !== '{project_id}') {
            $url->where('project_id', (int) $request->project_id);
        }

        if($request->date_range) {
            $url = $this->filterUrlsByDate($request->date_range, $url);
        }

        $data = [];
        $aggrConvRate = 0;
        $aggrRevenue = 0;
        $aggrOrderValue = 0;
        $aggrBounceRate = 0;

        $totalUrlKeywordDataCount = 0;
        $aggrPosition = 0;
        $aggrClicks = 0;
        $aggrImpressions = 0;
        $aggrCtr = 0;
        $aggrSearchVolume = 0;

        foreach ($url->get() as $item) {
            /**
             * @var UrlData $urlData
             * @var URL $item
             */
            foreach ($item->urlData as $urlData) {
                $aggrConvRate += $urlData->ecom_conversion_rate;
                $aggrRevenue += $urlData->revenue;
                $aggrOrderValue += $urlData->avg_order_value;
                $aggrBounceRate += $urlData->bounce_rate;
            }

            $urlKeywordDataCount = 0;
            $position = 0;
            $clicks = 0;
            $impressions = 0;
            $ctr = 0;
            $searchVolume = 0;

            /**
             * @var UrlKeywordData $urlKeywordData
             */
            foreach ($item->urlKeywordData as $urlKeywordData) {
                $position += $urlKeywordData->position;
                $clicks += $urlKeywordData->clicks;
                $impressions += $urlKeywordData->impressions;
                $ctr += $urlKeywordData->ctr;
                $urlKeywordDataCount++;
            }

            foreach ($item->keywords as $keyword) {
                $searchVolume += $keyword->search_volume;
            }

            $aggrPosition +=  $position;
            $aggrClicks +=  $clicks;
            $aggrImpressions += $impressions;
            $aggrCtr +=  $ctr;
            $aggrSearchVolume +=  $searchVolume;
            $totalUrlKeywordDataCount += $urlKeywordDataCount;
        }

        if($url->count() !== 0) {
            $aggrConvRate /= $url->count();
            $aggrOrderValue /= $url->count();
            $aggrBounceRate /= $url->count();
        }

        if($totalUrlKeywordDataCount > 0) {
            $aggrPosition /= $totalUrlKeywordDataCount;
            $aggrCtr /= $totalUrlKeywordDataCount;
        }

        $data['avgConvRate'] =  $aggrConvRate;
        $data['avgRevenue'] =  $aggrRevenue;
        $data['avgOrderValue'] =  $aggrOrderValue;
        $data['avgBounceRate'] =  $aggrBounceRate;
        $data['aggrPosition'] =  $aggrPosition;
        $data['aggrClicks'] =  $aggrClicks;
        $data['aggrImpressions'] =  $aggrImpressions;
        $data['aggrCtr'] =  $aggrCtr;
        $data['aggrSearchVolume'] =  $aggrSearchVolume;

        return response([
            'data' => $data,
        ], 200);
    }

    /**
     * @param $date_range
     * @param Builder $urls
     * @return Builder
     */
    protected function filterUrlsByDate($date_range, Builder $urls): Builder
    {
        $dates = explode('-', $date_range);
        if(count($dates) == 2) {
            $from = Carbon::createFromFormat('Y.m.d', $dates[0])->subDay();
            $to = Carbon::createFromFormat('Y.m.d', $dates[1]);
            if($from && $to) {
                $urls->with([
                    'urlData' => function (HasMany $query) use ($from, $to) {
                        $query->whereBetween('date', [$from, $to]);
                    }
                ]);
                $urls->with([
                    'urlKeywordData' => function (HasManyThrough $query) use ($from, $to) {
                        $query->whereBetween('date', [$from, $to]);
                    }
                ]);
            }
        }

        return $urls;
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
            'page_type'             => 'string|max:255',
        ]);

        if(in_array($fields['status'], ['new', 'NEU', 'neu'])) {
            $fields['status'] = 'NEW';
        }

        $url = URL::create($fields);

        Event::create([
            'user_id' => Auth::user()->id,
            'entity_type' => URL::class,
            'entity_id' => $url->id,
            'action' => Event::CREATE_ACTION,
            'data' =>  $fields,
        ]);

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
            'url' => URL::with(['project', 'events', 'keywords', 'urlData', 'urlKeywordData'])->find($url->id),
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
            'page_type'             => 'string|max:255',
            'keywords'              => 'array',
        ]);

        if(isset($fields['status']) && in_array($fields['status'], ['new', 'NEU', 'neu'])) {
            $fields['status'] = 'NEW';
        }

        $oldAttributes = $url->getOriginal();

        $url->fill($fields)->save();

        if(isset($fields['keywords'])) {
            $url->keywords()->sync($fields['keywords']);
        }

        Event::create([
            'user_id' => Auth::user()->id,
            'entity_type' => URL::class,
            'entity_id' => $url->id,
            'action' => Event::UPDATE_ACTION,
            'data' =>  $fields,
            'oldData' => $oldAttributes,
        ]);

        return response([
            'url' => URL::with('keywords', 'events', 'project', 'urlData', 'urlKeywordData')->find($url->id),
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

        Event::create([
            'user_id' => Auth::user()->id,
            'entity_type' => URL::class,
            'entity_id' => $url->id,
            'action' => Event::DELETE_ACTION,
            'data' =>  [],
        ]);

        return response([], 204);
    }
}
