<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportUrlsRequest;
use App\Http\Requests\UrlAggregationRequest;
use App\Http\Requests\UrlIndexRequest;
use App\Http\Requests\UrlStoreRequest;
use App\Http\Requests\UrlUpdateRequest;
use App\Models\Event;
use App\Models\Project;
use App\Models\TableConfig;
use App\Models\URL;
use App\Models\UrlData;
use App\Models\UrlKeywordData;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Schema;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

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
     *                 property="kw_number",
     *                 type="integer",
     *                 example=100,
     *             ),
     *             @OA\Property(
     *                 property="url_number",
     *                 type="integer",
     *                 example=10,
     *             ),
     *             @OA\Property(
     *                 property="sv_sum",
     *                 type="string",
     *                 example="lorem,ipsum,dolor",
     *             ),
     *             @OA\Property(
     *                 property="table_config",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/TableConfigResource")
     *             ),
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
     * @param UrlIndexRequest $request
     * @return Response
     */
    public function index(UrlIndexRequest $request): Response
    {
        $count = $request->count == '{count}' ? 10 : $request->count;
        $count = is_null($count) ? 10 : $count;

        $page = $request->page == '{page}' ? 1 : $request->page;
        $page = is_null($page) ? 1 : $page;

        $customSort = ['aggrConvRate', 'aggrRevenue', 'aggrOrderValue', 'aggrBounceRate', 'aggrSearchVolume',
            'aggrPosition', 'aggrClicks', 'aggrCtr', 'aggrImpressions'];

        $url = URL::with(['project', 'events', 'keywords', 'urlData', 'urlKeywordData', 'seoEvents']);

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

        $url->withCount('keywords');
        $columnNames = Schema::getColumnListing('urls');

        if($request->sort && $request->sort !== '{sort}') {
            $sort = explode('.', $request->sort);

            if(isset($sort[0]) && isset($sort[1]) && in_array($sort[0], $columnNames) && in_array($sort[1], ['asc', 'desc'])) {
                $url->orderBy($sort[0], $sort[1]);
            }
        }
        $kwCount = 0;
        $urlCount = 0;
        $svSum = 0;

        foreach ($url = $url->get() as $item) {
            $aggrConvRate = 0;
            $aggrRevenue = 0;
            $aggrOrderValue = 0;
            $aggrBounceRate = 0;
            $aggrSearchVolume = 0;

            $aggrPosition = 0;
            $aggrClicks = 0;
            $aggrImpressions = 0;
            $aggrCtr = 0;

            $urlDataCount = 0;
            $urlKeywordDataCount = 0;
            /**
             * @var UrlData $urlData
             * @var URL $item
             */
            foreach ($item->urlData as $urlData) {
                $aggrConvRate += $urlData->ecom_conversion_rate;
                $aggrRevenue += $urlData->revenue;
                $aggrOrderValue += $urlData->avg_order_value;
                $aggrBounceRate += $urlData->bounce_rate;

                $urlDataCount++;
            }
            if($urlDataCount !== 0) {
                $aggrConvRate /= $urlDataCount;
                $aggrOrderValue /= $urlDataCount;
                $aggrBounceRate /= $urlDataCount;
            }

            /**
             * @var UrlKeywordData $urlKeywordData
             */
            foreach ($item->urlKeywordData as $urlKeywordData) {
                $aggrPosition += $urlKeywordData->position;
                $aggrClicks += $urlKeywordData->clicks;
                $aggrImpressions += $urlKeywordData->impressions;
                $aggrCtr += $urlKeywordData->ctr;

                $urlKeywordDataCount++;
            }
            if($urlKeywordDataCount !== 0) {
                $aggrPosition /= $urlKeywordDataCount;
                $aggrCtr /= $urlKeywordDataCount;
            }

            foreach ($item->keywords as $keyword) {
                $aggrSearchVolume += $keyword->search_volume;
            }

            $item->setAttribute('aggrConvRate', $aggrConvRate);
            $item->setAttribute('aggrRevenue', $aggrRevenue);
            $item->setAttribute('aggrOrderValue', $aggrOrderValue);
            $item->setAttribute('aggrBounceRate', $aggrBounceRate);

            $item->setAttribute('aggrPosition', $aggrPosition);
            $item->setAttribute('aggrClicks', $aggrClicks);
            $item->setAttribute('aggrImpressions', $aggrImpressions);
            $item->setAttribute('aggrCtr', $aggrCtr);

            $item->setAttribute('aggrSearchVolume', $aggrSearchVolume);
            $item->setAttribute('aggrTrafficPotential', 'Coming soon...');
            $item->setAttribute('totalUrlKeywordDataCount', $urlKeywordDataCount);

            $kwCount += $item->keywords_count;
            $svSum += $aggrSearchVolume;
            $urlCount++;
        }

        if ($request->sort ) {
            $sort = explode('.', $request->sort);

            if(isset($sort[0]) && isset($sort[1]) && in_array($sort[0], $customSort) && in_array($sort[1], ['asc', 'desc'])) {
                $url = $url->sortBy($sort[0], SORT_REGULAR, $sort[1] == 'desc')->values();
            }
        }

        $config = TableConfig::whereUserId(auth()->id())->whereTableId('urls')->orderBy('position');
        $table_config = [];
        foreach ($config->get() as $item) {
            $table_config[] = [
                'position' => $item->position,
                'column' => $item->column,
            ];
        }

        return response([
            'kw_number' => $kwCount,
            'url_number' => $urlCount,
            'sv_sum' => $svSum,
            'urls' => new LengthAwarePaginator(
                $url->forPage($page, $count),
                $url->count(),
                $count,
                $page,
            ),
            'table_config' => $table_config,
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
     *                          property="aggrConvRate",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrRevenue",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrOrderValue",
     *                          type="integer",
     *                          example=0,
     *                     ),
     *                     @OA\Property(
     *                          property="aggrBounceRate",
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
     * @param UrlAggregationRequest $request
     * @return Response
     */
    public function urlAggregation (UrlAggregationRequest $request): Response
    {
        $collection = URL::with(['project', 'events', 'keywords', 'urlData', 'urlKeywordData']);

        if ($request->project_id && $request->project_id !== '{project_id}') {
            $collection->where('project_id', (int) $request->project_id);
        }

        if($request->date_range && $request->date_range !== '{date_range}') {
            $collection = $this->filterUrlsByDate($request->date_range, $collection);
        }

        $aggregations = [];
        foreach ($collection->get() as $url){
            $aggregations[] = $this->getURLAggregations($url);
        }

        $data = [];
        foreach ($aggregations as $item) {
            foreach ($item as $key => $value) {
                $data[$key] = $data[$key] ?? 0;
                $data[$key] += $value;
            }
        }

        if(isset($data['aggrConvRate'])) {
            $data['aggrConvRate'] /= count($aggregations);
        }
        if(isset($data['aggrOrderValue'])) {
            $data['aggrOrderValue'] /= count($aggregations);
        }
        if(isset($data['aggrBounceRate'])) {
            $data['aggrBounceRate'] /= count($aggregations);
        }
        if(isset($data['aggrPosition']) && isset($data['totalUrlKeywordDataCount']) && $data['totalUrlKeywordDataCount'] > 0) {
            $data['aggrPosition'] /= $data['totalUrlKeywordDataCount'];
        }
        if(isset($data['aggrCtr']) && isset($data['totalUrlKeywordDataCount']) && $data['totalUrlKeywordDataCount'] > 0) {
            $data['aggrCtr'] /= $data['totalUrlKeywordDataCount'];
        }

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
     * @param UrlStoreRequest $request
     * @return Response
     */
    public function store(UrlStoreRequest $request): Response
    {
        $fields = $request->validated();

        if(isset($fields['status']) && in_array($fields['status'], ['new', 'NEU', 'neu'])) {
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
     *             ),
     *             @OA\Property(
     *                 property="table_config",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/TableConfigResource")
     *             ),
     *         ),
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
        $url = URL::with(['project', 'events', 'keywords', 'urlData', 'urlKeywordData', 'seoEvents'])
            ->withCount('keywords')
            ->find($url->id);

        /**
         * @var URL $url
         */
        $data = $this->getURLAggregations($url);

        if(isset($data['aggrPosition']) && isset($data['totalUrlKeywordDataCount']) && $data['totalUrlKeywordDataCount'] > 0) {
            $data['aggrPosition'] /= $data['totalUrlKeywordDataCount'];
        }

        if(isset($data['aggrCtr']) && isset($data['totalUrlKeywordDataCount']) && $data['totalUrlKeywordDataCount'] > 0) {
            $data['aggrCtr'] /= $data['totalUrlKeywordDataCount'];
        }

        foreach ($data as $k => $v){
            $url->$k = $v;
        }

        $url->setAttribute('aggrTrafficPotential', 'Coming soon...');

        $config = TableConfig::whereUserId(auth()->id())->whereTableId('url')->orderBy('position');
        $table_config = [];
        foreach ($config->get() as $item) {
            $table_config[] = [
                'position' => $item->position,
                'column' => $item->column,
            ];
        }

        return response([
            'url' => $url,
            'table_config' => $table_config,
        ], 200);
    }

    /**
     * @param URL $url
     * @return int[]
     */
    protected function getURLAggregations (URL $url): array
    {
        $data = [
            'aggrConvRate' => 0,
            'aggrRevenue' => 0,
            'aggrOrderValue' => 0,
            'aggrBounceRate' => 0,
            'totalUrlKeywordDataCount' => 0,
            'aggrPosition' => 0,
            'aggrClicks' => 0,
            'aggrImpressions' => 0,
            'aggrCtr' => 0,
            'aggrSearchVolume' => 0,
        ];

        /**
         * @var UrlData $urlData
         * @var URL $item
         */
        foreach ($url->urlData as $urlData) {
            $data['aggrConvRate'] += $urlData->ecom_conversion_rate;
            $data['aggrRevenue'] += $urlData->revenue;
            $data['aggrOrderValue'] += $urlData->avg_order_value;
            $data['aggrBounceRate'] += $urlData->bounce_rate;
        }

        /**
         * @var UrlKeywordData $urlKeywordData
         */
        foreach ($url->urlKeywordData as $urlKeywordData) {
            $data['aggrPosition'] += $urlKeywordData->position;
            $data['aggrClicks'] += $urlKeywordData->clicks;
            $data['aggrImpressions'] += $urlKeywordData->impressions;
            $data['aggrCtr'] += $urlKeywordData->ctr;
            $data['totalUrlKeywordDataCount']++;
        }

        foreach ($url->keywords as $keyword) {
            $data['aggrSearchVolume'] += $keyword->search_volume;
        }

        return $data;
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
     *         @OA\JsonContent(ref="#/components/schemas/UrlUpdateRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param UrlUpdateRequest $request
     * @param URL $url
     * @return Response
     */
    public function update(UrlUpdateRequest $request, URL $url): Response
    {
        $fields = $request->validated();

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
            'url' => URL::with(['project', 'events', 'keywords', 'urlData', 'urlKeywordData', 'seoEvents'])->find($url->id),
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

    /**
     *
     * @OA\Post (
     *     path="/import_urls",
     *     operationId="import_urls",
     *     tags={"URLs"},
     *     summary="Import URLs",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="imported",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/ImportUrlsResponse"),
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
     *             @OA\Schema(ref="#/components/schemas/ImportStrategy")
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param ImportUrlsRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function import(ImportUrlsRequest $request): JsonResponse
    {
        set_time_limit (0);

        try {
            $project = Project::findOrFail($request->get('project_id'));
            $path = $request->file('file')->getRealPath();
            $csv = $this->parseCsv($path, ';', 2);

            if (isset($csv[0]) && (count($csv[0]) < 3)) {
                $csv = $this->parseCsv($path, ',', 2);
            }

            $headers = empty($csv) ? [] : array_flip(array_shift($csv));

            $keys = $this->getKeys($project, $headers);

            // empty keys mean that we don't have any of field name: field_de, field_en or *field_en
            if (empty($keys)) {
                return response()->json([
                    'message' => 'URL: this field is required',
                ], 422);
            }

            $required_fields = ['url', 'status', 'main_category', 'page_type'];

            foreach ($required_fields as $required_field) {
                if (!isset($headers[$keys[$required_field]])) {
                    $keys[$required_field] = str_replace('Category', 'category', $keys[$required_field]);
                    if (!isset($headers[$keys[$required_field]])) {
                        return response()->json([
                            'message' => $keys[$required_field] . ': this column is required',
                        ], 422);
                    }
                }
            }

            $urls = []; // urls array to do mass insert after parsing.
            $failed_rows = []; // any errors during parsing
            $urlsForCheck = [];

            // begin parsing process
            foreach ($csv as $row_number => $row) {
                $url = [];

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

                $url['url'] = $row[$headers[$keys['url']]];
                $urlsForCheck[] = $row[$headers[$keys['url']]];
                $url['status'] = $row[$headers[$keys['status']]];
                $url['status'] = str_replace('NEU', 'NEW', $url['status']);
                $url['main_category'] = $row[$headers[$keys['main_category']]];
                $url['project_id'] = $project->id;
                $url['page_type'] = $row[$headers[$keys['page_type']]];
                $url['sub_category'] = isset($headers[$keys['sub_category_1']]) ? $row[$headers[$keys['sub_category_1']]] : null;
                $url['sub_category2'] = isset($headers[$keys['sub_category_2']]) ? $row[$headers[$keys['sub_category_2']]] : null;
                $url['sub_category3'] = isset($headers[$keys['sub_category_3']]) ? $row[$headers[$keys['sub_category_3']]] : null;
                $url['sub_category4'] = isset($headers[$keys['sub_category_4']]) ? $row[$headers[$keys['sub_category_4']]] : null;
                $url['sub_category5'] = isset($headers[$keys['sub_category_5']]) ? $row[$headers[$keys['sub_category_5']]] : null;

                // add url item to $urls by url key (the same urls will be the same item)
                $urls[$row[$headers[$keys['url']]]] = $url;
            }
            // end parsing process

            // if we have fails during parsing - return all the errors
            if (!empty($failed_rows)) {
                return response()->json([$failed_rows], 422);
            }

            $existsUrls = URL::whereIn('url', $urlsForCheck)->count();

            // if we don't have errors - create entities
            // save all prepared urls from $urls
            URL::upsert(array_values($urls), ['url']);

        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return response()->json([
            'new_urls' => count($urls) - $existsUrls,
            'updated_urls' => $existsUrls,
        ], 201);
    }

    /**
     * get keys mapping
     *
     * @param Project $project
     * @param $headers
     * @return array
     */
    protected function getKeys(Project $project, $headers): array
    {
        // check if we have DE field names
        if(isset($headers[URL::URL_KEY])) {
            return  [
                'url' => URL::URL_KEY,
                'page_type' => URL::PAGE_TYPE,
                'main_category' => URL::MAIN_CATEGORY,
                'sub_category_1' => URL::SUB_CAT_1,
                'sub_category_2' => URL::SUB_CAT_2,
                'sub_category_3' => URL::SUB_CAT_3,
                'sub_category_4' => URL::SUB_CAT_4,
                'sub_category_5' => URL::SUB_CAT_5,
                'status' => URL::URL__STATUS,
            ];
        }

        // check if we have EN field names
        if(isset($headers[URL::URL_KEY_EN])) {
            return [
                'url' => URL::URL_KEY_EN,
                'page_type' => URL::PAGE_TYPE_EN,
                'main_category' => URL::MAIN_CATEGORY_EN,
                'sub_category_1' => URL::SUB_CAT_1_EN,
                'sub_category_2' => URL::SUB_CAT_2_EN,
                'sub_category_3' => URL::SUB_CAT_3_EN,
                'sub_category_4' => URL::SUB_CAT_4_EN,
                'sub_category_5' => URL::SUB_CAT_5_EN,
                'status' => URL::URL__STATUS_EN,
            ];
        }

        // check if we have EN field names with '*' sign (required fields)
        if(isset($headers['*' . URL::URL_KEY_EN])) {
            return [
                'url' => '*' . URL::URL_KEY_EN,
                'page_type' => '*' . URL::PAGE_TYPE_EN,
                'main_category' => '*' . URL::MAIN_CATEGORY_EN,
                'sub_category_1' => URL::SUB_CAT_1_EN,
                'sub_category_2' => URL::SUB_CAT_2_EN,
                'sub_category_3' => URL::SUB_CAT_3_EN,
                'sub_category_4' => URL::SUB_CAT_4_EN,
                'sub_category_5' => URL::SUB_CAT_5_EN,
                'status' => '*' . URL::URL__STATUS_EN,
            ];
        }

        // if we still here - we don't have any of desired field names (or at least URL column with right name),
        // so we return empty keys
        return [];
    }

    /**
     * @OA\Get(
     *     path="/import_urls_example",
     *     operationId="import_urls_example",
     *     tags={"URLs"},
     *     summary="Download URLs import example file",
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
        $filePath = public_path(). "/files/urls_example.csv";

        $headers = array(
            'Content-Type: text/csv',
        );

        return response()->download($filePath, 'urls_example.csv', $headers);
    }

}
