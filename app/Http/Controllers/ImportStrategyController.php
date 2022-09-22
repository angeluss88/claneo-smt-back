<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentStrategyRequest;
use App\Http\Requests\ImportStrategyRequest;
use App\Http\Requests\TimeLineDataRequest;
use App\Http\Requests\UrlDetailsRequest;
use App\Http\Requests\UrlKeywordDetailsRequest;
use App\Models\Import;
use App\Models\Keyword;
use App\Models\Project;
use App\Models\URL;
use App\Models\UrlKeywordData;
use App\Services\GoogleAnalyticsService;
use Auth;
use Carbon\Carbon;
use Exception;
use Google\Client;
use Google\Service\Webmasters;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use Validator;
use DateTime;
use \Illuminate\Support\Facades\URL as UrlFacade;

class ImportStrategyController extends Controller
{
    public $ga;

    public function __construct (GoogleAnalyticsService $ga)
    {
        $this->ga = $ga;
    }

    /**
     * @OA\Get(
     *     path="/imports?page={page}&count={count}&import_date={import_date}&project_id={project_id}",
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
     *     @OA\Parameter(
     *         name="import_date",
     *         in="path",
     *         description="Import date range (Y.m.d H:i:s-Y.m.d H:i:s)",
     *         required=false,
     *         example="2021.11.03 00:00:00-2021.12.03 00:00:00",
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
     * @param ImportStrategyRequest $request
     * @return Response
     */
    public function index(ImportStrategyRequest $request): Response
    {
        $imports = Import::with(['user', 'project']);

        $count = $request->count == '{count}' ? 10 : $request->count;

        if ($request->project_id && $request->project_id !== '{project_id}') {
            $imports->where('project_id', (int) $request->project_id);
        }

        if ($request->import_date && $request->import_date !== '{import_date}') {
            $dates = explode('-', $request->import_date);
            if(count($dates) == 2) {
                $from = Carbon::createFromFormat('Y.m.d H:i:s', $dates[0]);
                $to = Carbon::createFromFormat('Y.m.d H:i:s', $dates[1]);

                if($from && $to) {
                    $imports->whereBetween('updated_at', [$from, $to]);
                }
            }
        }

        $imports = $imports->orderBy('id', 'desc')->paginate($count);

        foreach ($imports as $import) {
            $import->setLastGAExpandDate();
            $import->setLastGSCExpandDate();
        }

        return response([
            'imports' => $imports,
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
        $import = Import::with(['user', 'project'])->find($import->id);
        $import->setLastGAExpandDate();
        $import->setLastGSCExpandDate();

        return response([
            'import' => $import,
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
                    'message' => 'URL: this field is required',
                ], 422);
            }

            $required_fields = ['url', 'status', 'main_category', 'keyword', 'search_volume'];

            foreach ($required_fields as $required_field) {
                if (!isset($headers[$keys[$required_field]])) {
                    return response()->json([
                        'message' => $keys[$required_field] . ': this column is required',
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
                    'message' => $keys['current_ranking_position'] . ': this column is required',
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
                            'message' => $keys[$required_field] . ': this field is required in row #' . $row_number,
                        ];
                        continue 2;
                    }
                }

                // custom checking of current_ranking_position - it has several cases of name
                if (!$row[$headers[$keys[$current_ranking_position_key]]]) {
                    $failed_rows[] = [
                        'row' => $row_number,
                        'attribute' => $keys['current_ranking_position'],
                        'message' => $keys['current_ranking_position'] . ': this field is required in row #' . $row_number,
                    ];
                    continue;
                }
                // end validation process

                // create keyword item form row
                $keyword['keyword'] = $row[$headers[$keys['keyword']]];

                $keyword['search_volume'] = (int) str_replace('.', '', $row[$headers[$keys['search_volume']]]);
                $keyword['search_volume_clustered'] = isset($headers[$keys['sv_clustered']]) ? $row[$headers[$keys['sv_clustered']]] : null;
                $keyword['search_volume_clustered'] = (int)str_replace('.', '', $keyword['search_volume_clustered']);

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

        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }

        $return = [];
        $return['import_id'] = $import->id;

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
    protected function parseCsv($path, string $s = ';', bool $stop = false): array
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
     * @OA\Get(
     *     path="/content_strategy_data?page={page}&count={count}&url={url}&keyword={keyword}&import_date={import_date}&project_id={project_id}&import_id={import_id}",
     *     operationId="content_strategy_data",
     *     tags={"Content Strategy"},
     *     summary="Handled content strategy data",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="count",
     *                 type="integer",
     *                 example=99,
     *             ),
     *             @OA\Property(
     *                 property="page",
     *                 type="integer",
     *                 example=1,
     *             ),
     *             @OA\Property(
     *                 property="pages",
     *                 type="integer",
     *                 example=10,
     *             ),
     *             @OA\Property(
     *                 property="perPage",
     *                 type="integer",
     *                 example=10,
     *             ),
     *             @OA\Property(
     *                 property="csData",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/CsResource"),
     *             ),
     *         ),
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
     *         name="keyword",
     *         in="path",
     *         description="Keyword",
     *         required=false,
     *         example="",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="url",
     *         in="path",
     *         description="Url",
     *         required=false,
     *         example="",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="import_date",
     *         in="path",
     *         description="Import date range (Y.m.d H:i:s-Y.m.d H:i:s)",
     *         required=false,
     *         example="2021.11.03 00:00:00-2021.12.03 00:00:00",
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
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param ContentStrategyRequest $request
     * @return Response
     */
    public function csStrategy (ContentStrategyRequest $request): Response
    {
        $urls = URL::with('keywords');

        if ($request->project_id && $request->project_id !== '{project_id}') {
            $urls->where('project_id', (int) $request->project_id);
        }

        if ($request->import_id && $request->import_id !== '{import_id}') {
            $urls->where('import_id', (int) $request->import_id);
        }

        if ($request->import_date) {
            $dates = explode('-', $request->import_date);
            if(count($dates) == 2) {
                $from = DateTime::createFromFormat('Y.m.d H:i:s', $dates[0]);
                $to = DateTime::createFromFormat('Y.m.d H:i:s', $dates[1]);
                if($from && $to) {
                        $from = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from->format('Y-m-d H:i:s'))));
                        $from = DateTime::createFromFormat('Y-m-d H:i:s', $from);
                        $urls->whereBetween('updated_at', [$from, $to]);

                }
            }
        }

        if($request->url && $request->url != '{url}') {
            $urls->where('url', 'like', '%' . $request->url . '%');
        }

        $csData = [];
        $i = 1;

        foreach ($urls->get() as $url) {
            /**
             * @var URL $url
             */
            $item = [];
            $item['url'] = $url->url;
            $item['page_type'] = $url->page_type;
            $item['main_category'] = $url->main_category;
            $item['sub_category'] = $url->sub_category;
            $item['sub_category2'] = $url->sub_category2;
            $item['sub_category3'] = $url->sub_category3;
            $item['sub_category4'] = $url->sub_category4;
            $item['sub_category5'] = $url->sub_category5;
            $item['status'] = $url->status;
            $item['import_id'] = $url->import_id;

            foreach ($url->keywords as $keyword) {
                /**
                 * @var Keyword $keyword
                 */
                $item['keyword'] = $keyword->keyword;
                $item['current_ranking_position'] = $keyword->current_ranking_position;
                $item['search_volume'] = $keyword->search_volume;
                $item['search_volume_clustered'] = $keyword->search_volume_clustered;
                $item['current_ranking_url'] = $keyword->current_ranking_url;
                $item['featured_snippet_keyword'] = $keyword->featured_snippet_keyword;
                $item['featured_snippet_owned'] = $keyword->featured_snippet_owned;
                $item['search_intention'] = $keyword->search_intention;
                $item['id'] = $i++;

                $csData[] = $item;
            }
        }

        if($request->keyword && $request->keyword != '{keyword}') {
            foreach ($csData as $k => $item) {
                if(strripos($item['keyword'], $request->keyword) === false) {
                    unset($csData[$k]);
                }
            }
        }


        $count = is_null($request->count) || $request->count == '{count}' ? 10 : $request->count;
        $page = is_null($request->page) || $request->page == '{page}' ? 1 : (int) $request->page;
        $offset = ($page-1)*$count;

        $data = array_slice($csData, $offset, $count);

        return response([
            'count' => count($csData),
            'page' => $page,
            'pages' => ceil(count($csData)/$count),
            'perPage' => (int) $count,
            'csData' => $data,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/timeline_data?import_date={import_date}&project_id={project_id}&metric={metric}&revert_data={revert_data}",
     *     operationId="api_timeline_data",
     *     tags={"Content Strategy"},
     *     summary="CS Timeline Data",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="timeLineData",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/TimelineData"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
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
     *         name="project_id",
     *         in="path",
     *         description="Project filter",
     *         required=true,
     *         example=1,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="metric",
     *         in="path",
     *         description="Metric ['ecom_conversion_rate', 'revenue', 'avg_order_value', 'bounce_rate', 'position', 'clicks', 'impressions', 'ctr']",
     *         required=true,
     *         example="clicks",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="revert_data",
     *         in="path",
     *         description="return negative numbers",
     *         required=false,
     *         example="0",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param TimeLineDataRequest $request
     * @return Response
     * @throws Exception
     */
    public function timelineData (TimeLineDataRequest $request): Response
    {
        $urls = URL::with('keywords');
        $dateData = [];

        $urls->where('project_id', $request->project_id);

        if ($request->import_date) {
            $urls = $this->filterTimeLineByImportData($request->import_date, $request->metric, $urls);
        }

        $urls = $urls->get();
        $metric = $request->metric;

        if(in_array($metric, GoogleAnalyticsService::GA_METRICS)) {
            foreach ($urls as $url) {
                foreach ($url->urlData as $urlData) {
                    if(!isset($dateData[$urlData->date])) {
                        $dateData[$urlData->date] = $urlData->$metric;
                    } else {
                        $dateData[$urlData->date] += $urlData->$metric;
                    }
                }
            }

            if(in_array($metric, ['ecom_conversion_rate', 'avg_order_value', 'bounce_rate']) && count($urls) !== 0) {
                foreach ($dateData as $k => $v) {
                    $dateData[$k] /= count($urls);
                }
            }
        }

        if(in_array($metric, GoogleAnalyticsService::GSC_METRICS)) {
            foreach ($urls as $url) {
                foreach ($url->urlKeywordData as $urlData) {
                    if(!isset($dateData[$urlData->date])) {
                        $dateData[$urlData->date] = [$urlData->$metric, 1];
                    } else {
                        $dateData[$urlData->date] = [
                            $dateData[$urlData->date][0] + $urlData->$metric,
                            ++$dateData[$urlData->date][1],
                        ];
                    }
                }
            }
            foreach ($dateData as $k => $v) {
                $dateData[$k] = in_array($metric, ['position', 'ctr']) ? $v[0]/$v[1] : (float) $v[0];
            }
        }

        foreach ($dateData as $k => $v) {
            $dateData[$k] = (float) $v;
        }

        ksort($dateData);

        if($request->revert_data && $request->revert_data == 1) {
            foreach ($dateData as $k => $v) {
                $dateData[$k] = $v * -1;
            }
        }

        return response([
            'timeLineData' => $dateData,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/urlDetails?import_date={import_date}&url_id={url_id}&metric={metric}&revert_data={revert_data}",
     *     operationId="urlDetails",
     *     tags={"Content Strategy"},
     *     summary="UrlDetails",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="urlDetails",
     *                 type="array",
     *                 collectionFormat="multi",
     *                 @OA\Items(ref="#/components/schemas/TimelineData"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
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
     *         name="url_id",
     *         in="path",
     *         description="Url ID",
     *         required=true,
     *         example=1,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="metric",
     *         in="path",
     *         description="Metric ['ecom_conversion_rate', 'revenue', 'avg_order_value', 'bounce_rate', 'position', 'clicks', 'impressions', 'ctr']",
     *         required=true,
     *         example="clicks",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="revert_data",
     *         in="path",
     *         description="return negative numbers",
     *         required=false,
     *         example="0",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param UrlDetailsRequest $request
     * @return Response
     * @throws Exception
     */
    public function urlDetails (UrlDetailsRequest $request): Response
    {
        $url = URL::with('keywords')->whereId($request->url_id);

        $urlDetails = [];

        if ($request->import_date) {
            $url = $this->filterTimeLineByImportData($request->import_date, $request->metric, $url);
        }

        $metric = $request->metric;

        $url = $url->first();

        if(in_array($metric, GoogleAnalyticsService::GA_METRICS)) {
            foreach ($url->urlData as $urlData) {
                if(!isset($urlDetails[$urlData->date])) {
                    $urlDetails[$urlData->date] = $urlData->$metric;
                } else {
                    $urlDetails[$urlData->date] += $urlData->$metric;
                }
            }
        }

        if(in_array($metric, GoogleAnalyticsService::GSC_METRICS)) {
            foreach ($url->urlKeywordData as $urlData) {
                if(!isset($urlDetails[$urlData->date])) {
                    $urlDetails[$urlData->date] = [$urlData->$metric, 1];
                } else {
                    $urlDetails[$urlData->date] = [
                        $urlDetails[$urlData->date][0] + $urlData->$metric,
                        ++$urlDetails[$urlData->date][1],
                    ];
                }
            }

            foreach ($urlDetails as $k => $v) {
                $urlDetails[$k] = in_array($metric, ['position', 'ctr']) ? $v[0]/$v[1] : (float) $v[0];
            }
        }

        foreach ($urlDetails as $k => $v) {
            $urlDetails[$k] = (float) $v;
        }

        ksort($urlDetails);

        if($request->revert_data && $request->revert_data == 1) {
            foreach ($urlDetails as $k => $v) {
                $urlDetails[$k] = $v * -1;
            }
        }

        return response([
            'urlDetails' => $urlDetails,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/urlKeywordDetails?import_date={import_date}&url_id={url_id}&page={page}&count={count}",
     *     operationId="UrlKeywordDetails",
     *     tags={"Content Strategy"},
     *     summary="UrlKeywordDetails",
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
     *                          @OA\Items(ref="#/components/schemas/UrlKeywordDetailsResource")
     *                     )
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/urlKeywordDetails?page=1",
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
     *                 example="http://127.0.0.1:8000/api/urlKeywordDetails?page=4",
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 example={{
     *                     "url": null,
     *                     "label": "&laquo; Previous",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urlKeywordDetails?page=1",
     *                     "label": "1",
     *                     "active": true
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urlKeywordDetails?page=2",
     *                     "label": "2",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urlKeywordDetails?page=3",
     *                     "label": "3",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urlKeywordDetails?page=4",
     *                     "label": "4",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/urlKeywordDetails?page=2",
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
     *                 example="http://127.0.0.1:8000/api/urlKeywordDetails?page=2",
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/urlKeywordDetails",
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
     *         name="url_id",
     *         in="path",
     *         description="Url ID",
     *         required=true,
     *         example=1,
     *         @OA\Schema(
     *             type="integer",
     *         )
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
     * @param UrlKeywordDetailsRequest $request
     * @return Response
     * @throws Exception
     */
    public function urlKeywordDetails (UrlKeywordDetailsRequest $request): Response
    {
        $url = URL::findOrFail($request->url_id)->with('keywords');

        if ($request->import_date) {
            $url = $this->filterTimeLineByImportData($request->import_date, GoogleAnalyticsService::GSC_METRICS[0], $url);
        }

        $url = $url->first();

        $keywords = [];
        $page = !isset($request->page) || $request->page == '{page}' || $request->page < 1 ? 1 : (int) $request->page;
        $count = !isset($request->count) || $request->count == '{count}' || $request->count < 1 ? 10 : (int) $request->count;

        foreach ($url->keywords as $i => $keyword) {
            if($i >= $page * $count) {
                break;
            }
            if ($i < ($page-1) * $count) {
                continue;
            }

            $position = 0;
            $clicks = 0;
            $impressions = 0;
            $ctr = 0;

            foreach ($keyword->urlKeywordData as $ukw) {
                $position += $ukw->position;
                $clicks += $ukw->clicks;
                $impressions += $ukw->impressions;
                $ctr += $ukw->ctr;
            }

            $keyword->totalPosition = $position;
            $keyword->totalClicks = $clicks;
            $keyword->totalImpressions = $impressions;
            $keyword->totalCtr = $ctr;

            $keywords[$keyword->id] = $keyword;
        }

        // @TODO look for better pagination solution
        $totalPages = ceil($url->keywords()->count() / $count);
        $nextPage = $page + 1;
        $nextPageUrl = UrlFacade::current() . "?page=" . $nextPage;
        $prevPage = $page - 1;
        $prevPageUrl = UrlFacade::current() . "?page=" . $prevPage;

        $links = [
            [
                'url' => $prevPage > 0 ? $prevPageUrl : null,
                'label' => "&laquo; Previous",
                'active' => $page == 1,
            ]
        ];

        for($i = 1; $i <= $totalPages; $i++) {
            $links[] = [
                'url' => UrlFacade::current() . "?page=" . $i,
                'label' => "$i",
                'active' => $page == $i,
            ];
        }

        $links[] = [
            [
                'url' => $totalPages > $page ? $nextPageUrl : null,
                'label' => "Next &raquo;",
                'active' => $page == $totalPages,
            ]
        ];

        return response([
            'keywords' => [
                'current_page' => $page,
                'data' => array_values($keywords),
                "first_page_url" => UrlFacade::current() . "?page=1",
                "from" => 1,
                "last_page"=> $totalPages,
                "last_page_url" => UrlFacade::current() . "?page=" . $totalPages,
                "links" => $links,
                "next_page_url" => $totalPages > $page ? $nextPageUrl : null,
                "path" => UrlFacade::current(),
                "per_page" => $count,
                "prev_page_url" => $prevPage > 0 ? $prevPageUrl : null,
                "to" => $totalPages,
                "total" => $url->keywords()->count()
            ],
        ], 200);
    }

    /**
     * @throws Exception
     */
    protected function filterTimeLineByImportData ($date, $metric, $urlModel)
    {
        $dates = explode('-', $date);
        if(count($dates) == 2) {
            $from = Carbon::createFromFormat('Y.m.d', $dates[0])->subDay();
            $to = Carbon::createFromFormat('Y.m.d', $dates[1]);

            if($from && $to) {
                if(in_array($metric, GoogleAnalyticsService::GA_METRICS)) {
                    $urlModel->with([
                        'urlData' => function (HasMany $query) use ($from, $to) {
                            $query->whereBetween('date', [$from, $to]);
                        }
                    ]);
                } else if(in_array($metric, GoogleAnalyticsService::GSC_METRICS)) {
                    $urlModel->with([
                        'urlKeywordData' => function (HasManyThrough $query) use ($from, $to) {
                            $query->whereBetween('date', [$from, $to]);
                        }
                    ]);
                } else {
                    throw new Exception('Unknown metric');
                }
            }
        }

        return $urlModel;
    }

    /**
     * @OA\Get(
     *     path="/expandGA/{import}",
     *     operationId="imports_expandGA",
     *     tags={"Content Strategy"},
     *     summary="Expand GA data for import",
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
     *     @OA\Response(
     *         response="500",
     *         description="Error: Internal Server Error",
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
     * @param Import $import
     * @return Response
     * @throws \Google\Exception
     */
    public function expandGA(Import $import): Response
    {
        $import = Import::find($import->id);

        if($import->project && $import->urls) {
            if ($import->project->strategy !== Project::NO_EXPAND_STRATEGY) {
                $urls = [];
                foreach ($import->urls as $url) {
                    $urls[] = $url->url;
                }

                $this->ga->expandGA($import->project, $urls);
            }
        }

        return response([], 204);
    }

    /**
     * @OA\Get(
     *     path="/expandGSC/{import}",
     *     operationId="imports_expandGSC",
     *     tags={"Content Strategy"},
     *     summary="Expand GSC data for import",
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
     *     @OA\Response(
     *         response="500",
     *         description="Error: Internal Server Error",
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
     * @param Import $import
     * @return Response
     * @throws \Google\Exception
     */
    public function expandGSC(Import $import): Response
    {
        $import = Import::find($import->id);

        if($import->project) {
            if ($import->project->expand_gsc) {
                $urls = [];
                foreach ($import->urls as $url) {
                    $urls[] = $url->url;
                }

                $keywords = [];
                foreach ($import->keywords as $keyword) {
                    $keywords[] = $keyword->keyword;
                }

                $this->ga->expandGSC($urls, $keywords, $import->project);
            }
        }

        return response([], 204);
    }

    /**
     * @OA\Get(
     *     path="/expandGAForProject/{project}",
     *     operationId="imports_expandGAForProject",
     *     tags={"Content Strategy"},
     *     summary="Expand GA data for project",
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
     *     @OA\Response(
     *         response="500",
     *         description="Error: Internal Server Error",
     *     ),
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         description="The project id",
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
     * @param Project $project
     * @return Response
     * @throws \Google\Exception
     */
    public function expandGAForProject(Project $project): Response
    {
        if($project->urls) {
            if ($project->strategy !== Project::NO_EXPAND_STRATEGY) {
                $urls = [];
                foreach ($project->urls as $url) {
                    $urls[] = $url->url;
                }

                $this->ga->expandGA($project, $urls);
            }
        }

        return response([], 204);
    }

    /**
     * @OA\Get(
     *     path="/expandGSCForProject/{project}",
     *     operationId="imports_expandGSCForProject",
     *     tags={"Content Strategy"},
     *     summary="Expand GSC data for project",
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
     *     @OA\Response(
     *         response="500",
     *         description="Error: Internal Server Error",
     *     ),
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         description="The project id",
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
     * @param Project $project
     * @return Response
     * @throws \Google\Exception
     */
    public function expandGSCForProject(Project $project): Response
    {
        if($project->urls && $project->expand_gsc) {
                $urls = [];
                $keywords = [];
                foreach ($project->urls as $url) {
                    $urls[] = $url->url;
                    foreach ($url->keywords as $keyword) {
                        $keywords[] = $keyword->keyword;
                    }
                }

                $this->ga->expandGSC($urls, array_unique($keywords), $project);
        }

        return response([], 204);
    }

    /**
     * @OA\Get(
     *     path="/getGscAuthLink",
     *     operationId="getGscAuthLink",
     *     tags={"Content Strategy"},
     *     summary="Get GSC Auth Link",
     *     @OA\Response(
     *         response="200",
     *         description="Link",
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
     * @throws \Google\Exception
     */
    public function getGscAuthLink()
    {
        $link = '';

        if(file_exists(GoogleAnalyticsService::getGSCOAuthCredsPath())) {
            $client = new Client();
            $client->setAuthConfig(GoogleAnalyticsService::getGSCOAuthCredsPath());
            $client->addScope(Webmasters::WEBMASTERS_READONLY);
            $client->setAccessType('offline');
            $client->setPrompt('consent');
            $link = $client->createAuthUrl();
        }

        return response([
            'link' => $link,
        ], 200);
    }
}
