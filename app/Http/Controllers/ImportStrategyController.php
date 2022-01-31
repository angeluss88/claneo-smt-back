<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\Keyword;
use App\Models\Project;
use App\Models\URL;
use App\Models\UrlData;
use App\Services\GoogleAnalyticsService;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Google_Client;
use Google_Service_Webmasters;
use Google_Service_Webmasters_SearchAnalyticsQueryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use Validator;

class ImportStrategyController extends Controller
{
    public $ga;

    public function __construct (GoogleAnalyticsService $ga)
    {
        $this->ga = $ga;
    }

    /**
     * @OA\Get(
     *     path="/imports?page={page}&count={count}",
     *     operationId="imports_index",
     *     tags={"Content Strategy"},
     *     summary="List of imports",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="imports",
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
     *                          @OA\Items(ref="#/components/schemas/ImportResource")
     *                     )
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/imports?page=1",
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
     *                 example="http://127.0.0.1:8000/api/imports?page=4",
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 example={{
     *                     "url": null,
     *                     "label": "&laquo; Previous",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/imports?page=1",
     *                     "label": "1",
     *                     "active": true
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/imports?page=2",
     *                     "label": "2",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/imports?page=3",
     *                     "label": "3",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/imports?page=4",
     *                     "label": "4",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/imports?page=2",
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
     *                 example="http://127.0.0.1:8000/api/imports?page=2",
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/imports",
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
            'imports' => Import::with(['user', 'project'])->paginate($count),
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/imports/{import}",
     *     operationId="imports_show",
     *     tags={"Content Strategy"},
     *     summary="Show import",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="url",
     *             type="object",
     *             ref="#/components/schemas/ImportResource",
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
     *         name="import",
     *         in="path",
     *         description="The import id",
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
     * @param  Import $import
     * @return Response
     */
    public function show(Import $import): Response
    {
        return response([
            'import' => Import::with(['user', 'project'])->find($import->id),
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/import_example",
     *     operationId="import_example",
     *     tags={"Content Strategy"},
     *     summary="Download Import strategy example file",
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
        $filePath = public_path(). "/files/is_example.csv";

        $headers = array(
            'Content-Type: text/csv',
        );

        return response()->download($filePath, 'is_example.csv', $headers);
    }

    /**
     *
     * @OA\Post (
     *     path="/import_strategy",
     *     operationId="import_strategy",
     *     tags={"Content Strategy"},
     *     summary="Import Content Strategy",
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
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function import(Request $request): JsonResponse
    {
        set_time_limit (0);
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        $urlsToExpand = [];
        $keywordsToExpand = [];

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

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
                    'attribute' => 'URL',
                    'error_message' => 'this field is required',
                ], 422);
            }

            $required_fields = ['url', 'status', 'main_category', 'keyword', 'search_volume'];

            foreach ($required_fields as $required_field) {
                if (!isset($headers[$keys[$required_field]])) {
                    return response()->json([
                        'attribute' => $keys[$required_field],
                        'error_message' => 'this column is required',
                    ], 422);
                }
            }

            // there could be different current_ranking_position columns names, we should check them all
            if (isset($headers[$keys['current_ranking_position']])) {
                $current_ranking_position_key = 'current_ranking_position';
            } else if (isset($headers[$keys['current_ranking_position2']])) {
                $current_ranking_position_key = 'current_ranking_position2';
            } else if (isset($headers[$keys['current_ranking_position3']])) {
                $current_ranking_position_key = 'current_ranking_position3';
            } else {
                return response()->json([
                    'attribute' => $keys['current_ranking_position'],
                    'error_message' => 'this column is required',
                ], 422);
            }

            $urls = []; // urls array to do mass insert after parsing. keywords will be an array of URL
            $keywords = []; // keywords. format ['url' => [['keyword1'], ['keyword2'],]
            $url_to_keywords = []; // array to keep many-to-many for urls-keys
            $failed_rows = []; // any errors during parsing

            // begin parsing process
            foreach ($csv as $row_number => $row) {
                $url = [];
                $keyword = [];

                // begin validation process
                // if we don't have any of required fields - stop parse this row and add item to failed rows
                foreach ($required_fields as $required_field) {
                    if (!$row[$headers[$keys[$required_field]]] && $row[$headers[$keys[$required_field]]] !== "0") {
                        $failed_rows[] = [
                            'row' => $row_number,
                            'attribute' => $keys[$required_field],
                            'error_message' => 'this field is required',
                        ];
                        continue 2;
                    }
                }

                // custom checking of current_ranking_position - it has several cases of name
                if (!$row[$headers[$keys[$current_ranking_position_key]]]) {
                    $failed_rows[] = [
                        'row' => $row_number,
                        'attribute' => $keys['current_ranking_position'],
                        'error_message' => 'this field is required',
                    ];
                    continue;
                }
                // end validation process

                // create keyword item form row
                $keyword['keyword'] = $row[$headers[$keys['keyword']]];
                $keyword['search_volume'] = (int)$row[$headers[$keys['search_volume']]];
                $keyword['search_volume_clustered'] = isset($headers[$keys['sv_clustered']]) ? $row[$headers[$keys['sv_clustered']]] : null;
                $keyword['search_volume_clustered'] = (int)$keyword['search_volume_clustered'];

                $keyword['current_ranking_url'] = $headers[$keys['current_ranking_url']] ? $row[$headers[$keys['current_ranking_url']]] : null;

                $keyword['featured_snippet_keyword'] = isset($headers[$keys['featured_snippet_kw']]) ? $row[$headers[$keys['featured_snippet_kw']]] : null;
                $keyword['featured_snippet_keyword'] = strtolower($keyword['featured_snippet_keyword']);
                $keyword['featured_snippet_keyword'] = str_replace('ja', 'yes', $keyword['featured_snippet_keyword']);
                $keyword['featured_snippet_keyword'] = str_replace('nein', 'no', $keyword['featured_snippet_keyword']);

                $keyword['featured_snippet_owned'] = isset($headers[$keys['featured_snippet_owned']]) ? $row[$headers[$keys['featured_snippet_owned']]] : null;
                $keyword['featured_snippet_owned'] = strtolower($keyword['featured_snippet_owned']);
                $keyword['featured_snippet_owned'] = str_replace('ja', 'yes', $keyword['featured_snippet_owned']);
                $keyword['featured_snippet_owned'] = str_replace('nein', 'no', $keyword['featured_snippet_owned']);

                $keyword['current_ranking_position'] = $row[$headers[$keys[$current_ranking_position_key]]];
                $keyword['current_ranking_position'] = str_replace('Nicht', 'Not', $keyword['current_ranking_position']);
                $keyword['current_ranking_position'] = str_replace('nicht', 'Not', $keyword['current_ranking_position']);

                $keyword['search_intention'] = isset($headers[$keys['search_intention']]) ? $row[$headers[$keys['search_intention']]] : null;
                if ($keyword['search_intention']) {
                    $keyword['search_intention'] = strtolower($keyword['search_intention']);
                    $keyword['search_intention'] = str_replace('transaktional', 'transactional', $keyword['search_intention']);
                }

                // add keyword item to keywords array by keyword key (the same keywords will be the same item)
                $keywords[$row[$headers[$keys['keyword']]]] = $keyword;
                $keywordsToExpand[] = $keyword['keyword'];

                // create URL item from row
                $url['url'] = $row[$headers[$keys['url']]];
                $url['status'] = $row[$headers[$keys['status']]];
                $url['status'] = str_replace('NEU', 'NEW', $url['status']);
                $url['main_category'] = $row[$headers[$keys['main_category']]];
                $url['project_id'] = $project->id;
                $url['page_type'] = isset($headers[$keys['page_type']]) ? $row[$headers[$keys['page_type']]] : null;
                $url['sub_category'] = isset($headers[$keys['sub_category_1']]) ? $row[$headers[$keys['sub_category_1']]] : null;
                $url['sub_category2'] = isset($headers[$keys['sub_category_2']]) ? $row[$headers[$keys['sub_category_2']]] : null;
                $url['sub_category3'] = isset($headers[$keys['sub_category_3']]) ? $row[$headers[$keys['sub_category_3']]] : null;
                $url['sub_category4'] = isset($headers[$keys['sub_category_4']]) ? $row[$headers[$keys['sub_category_4']]] : null;
                $url['sub_category5'] = isset($headers[$keys['sub_category_5']]) ? $row[$headers[$keys['sub_category_5']]] : null;

                $urlsToExpand[] = $url['url'];

                // add url item to $urls by url key (the same urls will be the same item)
                $urls[$row[$headers[$keys['url']]]] = $url;

                // add urls-to-keys relation  item
                $url_to_keywords[$url['url']][$keyword['keyword']] = $keyword['keyword'];
            }
            // end parsing process

            // if we have fails during parsing - return all the errors
            if (!empty($failed_rows)) {
                return response()->json([$failed_rows], 422);
            }

            // if we don't have errors - create entities
            // create Import item to have import_id
            $import = Import::create([
                'user_id' => Auth::id(),
                'project_id' => $project->id,
            ]);

            foreach ($urls as $k => $value) {
                $urls[$k]['import_id'] = $import->id;
            }

            foreach ($keywords as $k => $value) {
                $keywords[$k]['import_id'] = $import->id;
            }

            // save all prepared urls from $urls
            URL::upsert(array_values($urls), ['url']);

            //save all prepared keyword from $keywords
            Keyword::upsert(array_values($keywords), ['keyword']);

            foreach ($url_to_keywords as $url => $keywords) {
                $kw_collection = Keyword::whereIn('keyword', $keywords)->get('id');

                URL::where('url', $url)->first()->keywords()->syncWithoutDetaching($kw_collection);
            }

            // set import status to complete
            $import->status = Import::STATUS_COMPLETE;
            $import->save();

            if($project->strategy !== Project::NO_EXPAND_STRATEGY) {
                $this->expandGA($project, $urlsToExpand);
            }

//            $result = $this->expandGSC($urlsToExpand);

        } catch (Throwable $e) {
            var_dump($e->getMessage());
        }

        $return = [];
        if(isset($import)) {
            $return['import_id'] = $import->id;
        }

        return response()->json($return, 201);
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
                'search_intention' => Keyword::SEARCH_INTENTION,
                'status' => URL::URL__STATUS,
                'keyword' => Keyword::KEYWORD,
                'search_volume' => Keyword::SEARCH_VOLUME,
                'sv_clustered' => Keyword::SV_CLUSTERED,
                'current_ranking_url' => Keyword::CURRENT_RANKING_URL,
                'featured_snippet_kw' => Keyword::FEATURED_SNIPPET_KW,
                'featured_snippet_owned' => str_replace('@project.domain', $project->domain, Keyword::FEATURED_SNIPPET_OWNED),
                'current_ranking_position' => str_replace('@project.domain', $project->domain, Keyword::CURRENT_RANKING_POSITION),
                'current_ranking_position2' => Keyword::CURRENT_RANKING_POSITION_2,
                'current_ranking_position3' => $project->domain,
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
                'search_intention' => Keyword::SEARCH_INTENTION_EN,
                'status' => URL::URL__STATUS_EN,
                'keyword' => Keyword::KEYWORD_EN,
                'search_volume' => Keyword::SEARCH_VOLUME_EN,
                'sv_clustered' => Keyword::SV_CLUSTERED_EN,
                'current_ranking_url' => Keyword::CURRENT_RANKING_URL_EN,
                'featured_snippet_kw' => Keyword::FEATURED_SNIPPET_KW_EN,
                'featured_snippet_owned' => Keyword::FEATURED_SNIPPET_OWNED_EN,
                'current_ranking_position' => Keyword::CURRENT_RANKING_POSITION_EN,
                'current_ranking_position2' => Keyword::CURRENT_RANKING_POSITION_EN,
                'current_ranking_position3' => $project->domain,
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
                'search_intention' => '*' . Keyword::SEARCH_INTENTION_EN,
                'status' => '*' . URL::URL__STATUS_EN,
                'keyword' => '*' . Keyword::KEYWORD_EN,
                'search_volume' => '*' . Keyword::SEARCH_VOLUME_EN,
                'sv_clustered' => Keyword::SV_CLUSTERED_EN,
                'current_ranking_url' => Keyword::CURRENT_RANKING_URL_EN,
                'featured_snippet_kw' => Keyword::FEATURED_SNIPPET_KW_EN,
                'featured_snippet_owned' => str_replace('@project.domain', $project->domain, Keyword::FEATURED_SNIPPET_OWNED_EN),
                'current_ranking_position' => Keyword::CURRENT_RANKING_POSITION_EN,
                'current_ranking_position2' => '*' . Keyword::CURRENT_RANKING_POSITION_EN,
                'current_ranking_position3' => '*' . $project->domain,
            ];
        }

        // if we still here - we don't have any of desired field names (or at least URL column with right name),
        // so we return empty keys
        return [];
    }

    /**
     * parse csv file workflow
     *
     * @param $path - path of csv file
     * @param string $s - separator sign
     * @param false $stop - to prevent wrong parsing
     * @return array - array of parsed rows
     */
    public function parseCsv($path, string $s = ';', bool $stop = false): array
    {
        $csv = [];
        $row = 1;

        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $s)) !== FALSE) {
                if ($stop && empty($csv) && count($data) < $stop) {
                    break;
                }
                $csv[] = $data;
                $row++;
            }

            fclose($handle);
        }

        return $csv;
    }

    /**
     * @param Project $project
     * @param array $urls
     * @throws Exception
     */
    public function expandGA(Project $project, array $urls)
    {
        $urls = array_unique($urls);
        try {
            if($project->strategy === Project::GA_STRATEGY){
                if($project->ga_property_id) {
                    $dimensions = ['pagePath'];
                    $metrics = ['totalRevenue', 'averagePurchaseRevenue', 'engagementRate', 'conversions', 'sessions'];

                    $period = CarbonPeriod::create(Carbon::now()->subMonth(16)->format('Y-m-d'), Carbon::now());
                    $urlData = [];

                    foreach ($period as $date) {
                        $date = $date->format('Y-m-d');

                        $response = $this->ga->makeGAApiCall($project->ga_property_id, $dimensions, $metrics, $date, $date);
                        $result = $this->ga->formatGAResponse($response, $project->domain, $date);

                        if (!empty($result)) {
                            foreach ($result as $url => $data) {
                                $result[$url]['bounce_rate'] = 1 - $data['engagementRate'];
                                $result[$url]['bounce_rate'] = (string)$result[$url]['bounce_rate'];
                                $result[$url]['ecom_conversion_rate'] = $data['sessions'] == 0 ? 0 : $data['conversions'] / $data['sessions'];
                                $result[$url]['ecom_conversion_rate'] = (string)$result[$url]['ecom_conversion_rate'];

                                if (isset($result[$url]['totalRevenue'])) {
                                    $result[$url]['revenue'] = $result[$url]['totalRevenue'];
                                    unset($result[$url]['totalRevenue']);
                                }

                                if (isset($result[$url]['averagePurchaseRevenue'])) {
                                    $result[$url]['avg_order_value'] = $result[$url]['averagePurchaseRevenue'];
                                    unset($result[$url]['averagePurchaseRevenue']);
                                }

                                unset($result[$url]['engagementRate']);
                                unset($result[$url]['conversions']);
                                unset($result[$url]['sessions']);
                            }

                            $urlData = $this->handleGAResult($urlData, $result, $urls);
                        }
                    }
                    $this->saveGaResults($urlData);
                }
            } else if($project->strategy === Project::UA_STRATEGY){
                if($project->ua_view_id) {
                    $metrics = [
                        ['expression' => "ga:bounceRate", 'alias' => 'bounceRate'],
                        ['expression' => "ga:transactionsPerSession", 'alias' => 'conversionRate'],
                        ['expression' => "ga:transactionRevenue", 'alias' => 'revenue'],
                        ['expression' => "ga:revenuePerTransaction", 'alias' => 'avgOrderValue'],
                    ];

                    $period = CarbonPeriod::create(Carbon::now()->subMonth(16)->format('Y-m-d'), Carbon::now());
                    $urlData = [];

                    foreach ($period as $date) {
                        $date = $date->format('Y-m-d');
                        $result = $this->ga->parseUAResults($this->ga->getReport($project->ua_property_id, $project->ua_view_id, $metrics, $date, $date), $date);

                        $urlData = $this->handleGAResult($urlData, $result, $urls);
                    }
                    $this->saveGaResults($urlData);
                }
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function handleGAResult ($urlData, $result, $urls): array
    {
        foreach ($result as $url => $urlRow) {
            if (!in_array($url, $urls)) {
                $url2 = $url;

                if (substr($url, -1) === '/') {
                    $url2 = rtrim($url, "/ ");
                } else {
                    $url2 .= '/';
                }

                if (!in_array($url2, $urls)) {
                    unset($result[$url]);
                }
            }
        }

        foreach ($result as $url => $data) {
            if(isset($data['date'])) {
                $urlData[$url][$data['date']] = $data;
            }
        }
        return $urlData;
    }

    protected function saveGaResults($result)
    {
        $fails = [];
        $upsert = [];
        foreach ($result as $k=> $v) {
            $url = URL::whereUrl($k)->first();

            if(!$url) {
                $url = URL::whereUrl($k . '/')->first();
            }

            if(!$url) {
                $url = URL::whereUrl(rtrim($k, "/ "))->first();
            }

            if($url) {
                foreach ($v as $date => $data) {
                    if(empty($data)) {
                        $data = [
                            'ecom_conversion_rate' => 0,
                            'revenue' => 0,
                            'avg_order_value' => 0,
                            'bounce_rate' => 0,
                            'date' => $date,
                        ];
                    }
                    $data['url_id'] = $url->id;
                    $upsert[] = $data;
                }
            }
        }

        UrlData::upsert($upsert, ['url_id', 'date'], ['ecom_conversion_rate', 'revenue', 'avg_order_value', 'bounce_rate']);
    }

    /**
     * @param $urls
     * @return array
     * @throws Exception
     */
    public function expandGSC($urls): array
    {
        try {
            $result = [];

            $client = new Google_Client();
            $client->setApplicationName('"Hello Analytics Reporting"');
            $client->setAuthConfig(config_path() . DIRECTORY_SEPARATOR . 'credentials.json');
            $client->addScope('https://www.googleapis.com/auth/webmasters');

            $serviceWebmasters = new Google_Service_Webmasters( $client );

            if(is_countable($serviceWebmasters->sites->listSites()->getSiteEntry())) {
                foreach ($serviceWebmasters->sites->listSites()->getSiteEntry() as $site) {
                    if(in_array($site->getSiteUrl(), $urls)) {
                        $postBody = new Google_Service_Webmasters_SearchAnalyticsQueryRequest( [
                            'startDate'  => Carbon::now()->subMonth(16)->format('Y-m-d'),
                            'endDate'    => Carbon::now()->format('Y-m-d'),
                            'dimensions' => [
                                'page',
                            ],
                        ] );

                        $searchAnalyticsResponse = $serviceWebmasters->searchanalytics->query($site->getSiteUrl(), $postBody);

                        foreach ($searchAnalyticsResponse->getRows() as $row) {
                            $result[$site->getSiteUrl()]['clicks'] = $row->getClicks();
                            $result[$site->getSiteUrl()]['impressions'] = $row->getImpressions();
                            $result[$site->getSiteUrl()]['ctr'] = $row->getCtr();
                            $result[$site->getSiteUrl()]['position'] = $row->getPosition();
                            $result[$site->getSiteUrl()]['keys'] = $row->getKeys();
                        }
                    }
                }
            }
        } catch(Exception $e ) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }
}
