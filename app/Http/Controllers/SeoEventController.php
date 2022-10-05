<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\SeoEventStoreRequest;
use App\Http\Requests\SeoEventUpdateRequest;
use App\Models\Event;
use App\Models\Project;
use App\Models\SeoEvent;
use App\Models\URL;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Response;

class SeoEventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/seo_events?page={page}&count={count}&title={title}&description={description}&search={search}&date_range={date_range}&project_id={project_id}&url_id={url_id}",
     *     operationId="seo_events_index",
     *     tags={"Seo_Events"},
     *     summary="List of SEO events",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="seo_events",
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
     *                          @OA\Items(ref="#/components/schemas/SeoEventResource")
     *                     )
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/seo_events?page=1",
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
     *                 example="http://127.0.0.1:8000/api/seo_events?page=4",
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 example={{
     *                     "url": null,
     *                     "label": "&laquo; Previous",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/seo_events?page=1",
     *                     "label": "1",
     *                     "active": true
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/seo_events?page=2",
     *                     "label": "2",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/seo_events?page=3",
     *                     "label": "3",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/seo_events?page=4",
     *                     "label": "4",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/seo_events?page=2",
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
     *                 example="http://127.0.0.1:8000/api/seo_events?page=2",
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/seo_api/events",
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
     *         name="title",
     *         in="path",
     *         description="Title filter",
     *         required=false,
     *         example="title",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="path",
     *         description="Description filter",
     *         required=false,
     *         example="description",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="path",
     *         description="Search by title or description",
     *         required=false,
     *         example="search",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="date_range",
     *         in="path",
     *         description="Date range filter (Y.m.d-Y.m.d)",
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
     *     @OA\Parameter(
     *         name="url_id",
     *         in="path",
     *         description="URL filter",
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
     * @param BaseIndexRequest $request
     * @return Response
     */
    public function index(BaseIndexRequest $request): Response
    {
        $count = $request->count == '{count}' ? 10 : $request->count;
        $seoEvent = SeoEvent::with(['entity']);

        if($request->search && $request->search !== '{search}') {
            $seoEvent->where('title', 'LIKE', '%' . $request->search . '%');
            $seoEvent->orWhere('description', 'LIKE', '%' . $request->search . '%');
        }

        if($request->title && $request->title !== '{title}') {
            $seoEvent->where('title', 'LIKE', '%' . $request->title . '%');
        }

        if($request->description && $request->description !== '{description}') {
            $seoEvent->where('description', 'LIKE', '%' . $request->description . '%');
        }

        if ($request->date_range && $request->date_range !== '{date_range}') {
            $dates = explode('-', $request->date_range);
            if(count($dates) == 2) {
                $from = Carbon::createFromFormat('Y.m.d', $dates[0])->subDay();
                $to = Carbon::createFromFormat('Y.m.d', $dates[1]);

                if($from && $to) {
                    $seoEvent->whereBetween('date', [$from, $to]);
                }
            }
        }

        if($request->project_id && $request->project_id !== '{project_id}') {
            $seoEvent->where('entity_type', Project::class)->where('entity_id', $request->project_id);
        }
        if($request->url_id && $request->url_id !== '{url_id}') {
            $seoEvent->where('entity_type', URL::class)->where('entity_id', $request->url_id);
        }

        $seoEvent->orderBy('date', 'desc');

        return response([
            'seo_events' => $seoEvent->paginate($count),
        ], 200);
    }

    /**
     *
     * @OA\Post (
     *     path="/seo_events",
     *     operationId="seo_events_store",
     *     tags={"Seo_Events"},
     *     summary="Create SEO Event",
     *     @OA\Response(
     *         response="201",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="seo_event",
     *             type="object",
     *             ref="#/components/schemas/SeoEventResource",
     *         ))
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
     *         @OA\JsonContent(ref="#/components/schemas/SeoEventRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param SeoEventStoreRequest $request
     * @return Response
     * @throws Exception
     */
    public function store(SeoEventStoreRequest $request): Response
    {
        $fields = $request->validated();

        switch ($fields['entity_type']) {
            case SeoEvent::URL_TYPE:
                $fields['entity_type'] = URL::class;
                break;
            case SeoEvent::PROJECT_TYPE:
                $fields['entity_type'] = Project::class;
                break;
            default:
                throw new Exception('unknown type');
        }

        if(isset($fields['date'])) {
            $fields['date'] = DateTime::createFromFormat(SeoEvent::DATE_FORMAT, $fields['date'])->format('Y-m-d');
        }

        $seoEvent = SeoEvent::create($fields);

        Event::create([
            'user_id' => \Auth::user()->id,
            'entity_type' => SeoEvent::class,
            'entity_id' => $seoEvent->id,
            'action' => Event::CREATE_ACTION,
            'data' =>  $fields,
            'oldData' => [],
        ]);

        return response([
            'seo_event' => $seoEvent,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/seo_events/{seo_event}",
     *     operationId="seo_events_show",
     *     tags={"Seo_Events"},
     *     summary="Show SEO Event",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="seo_event",
     *             type="object",
     *             ref="#/components/schemas/SeoEventResource",
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
     *         name="seo_event",
     *         in="path",
     *         description="The seo event id",
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
     * @param SeoEvent $seoEvent
     * @return Response
     */
    public function show(SeoEvent $seoEvent): Response
    {
        return response([
            'seo_event' => SeoEvent::with(['entity'])->find($seoEvent->id),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/seo_events/{seo_event}",
     *     operationId="seo_events_update",
     *     tags={"Seo_Events"},
     *     summary="Update SEO Event",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="seo_event",
     *             type="object",
     *             ref="#/components/schemas/SeoEventResource",
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
     *         description="The given data was invalid.",
     *     ),
     *     @OA\Parameter(
     *         name="seo_event",
     *         in="path",
     *         description="The seo event id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SeoEventRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param SeoEventUpdateRequest $request
     * @param SeoEvent $seoEvent
     * @return Response
     * @throws Exception
     */
    public function update(SeoEventUpdateRequest $request, SeoEvent $seoEvent): Response
    {
        $fields = $request->validated();

        if(isset($fields['entity_type'])) {
            switch ($fields['entity_type']) {
                case SeoEvent::URL_TYPE:
                    $fields['entity_type'] = URL::class;
                    break;
                case SeoEvent::PROJECT_TYPE:
                    $fields['entity_type'] = Project::class;
                    break;
                default:
                    throw new Exception('unknown type');
            }
        }

        if(isset($fields['date'])) {
            $fields['date'] = DateTime::createFromFormat(SeoEvent::DATE_FORMAT, $fields['date'])->format('Y-m-d');
        }

        $oldData = $seoEvent->getOriginal();

        $seoEvent->fill($fields)->save();

        Event::create([
            'user_id' => \Auth::user()->id,
            'entity_type' => SeoEvent::class,
            'entity_id' => $seoEvent->id,
            'action' => Event::UPDATE_ACTION,
            'data' =>  $request->validated(),
            'oldData' => $oldData,
        ]);

        return response([
            'seo_event' => SeoEvent::with(['entity'])->find($seoEvent->id),
        ], 200);
    }

    /**
     * @OA\Delete (
     *     path="/seo_events/{seo_event}",
     *     operationId="seo_events_delete",
     *     tags={"Seo_Events"},
     *     summary="Delete SEO Event",
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
     *         name="seo_event",
     *         in="path",
     *         description="The seo_event id",
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
     * @param SeoEvent $seoEvent
     * @return Response
     */
    public function destroy(SeoEvent $seoEvent): Response
    {
        $seoEvent->delete();

        Event::create([
            'user_id' => \Auth::user()->id,
            'entity_type' => SeoEvent::class,
            'entity_id' => $seoEvent->id,
            'action' => Event::DELETE_ACTION,
            'data' =>  [],
            'oldData' => [],
        ]);

        return response([], 204);
    }
}
