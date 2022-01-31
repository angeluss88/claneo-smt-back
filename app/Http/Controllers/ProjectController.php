<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/projects?page={page}&count={count}&client_id={client_id}",
     *     operationId="projects_index",
     *     tags={"Projects"},
     *     summary="Projects List",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="projects",
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
     *                          @OA\Items(ref="#/components/schemas/ProjectResource")
     *                     )
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/projects?page=1",
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
     *                 example="http://127.0.0.1:8000/api/projects?page=4",
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 example={{
     *                     "url": null,
     *                     "label": "&laquo; Previous",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/projects?page=1",
     *                     "label": "1",
     *                     "active": true
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/projects?page=2",
     *                     "label": "2",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/projects?page=3",
     *                     "label": "3",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/projects?page=4",
     *                     "label": "4",
     *                     "active": false
     *                 }, {
     *                     "url": "http://127.0.0.1:8000/api/projects?page=2",
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
     *                 example="http://127.0.0.1:8000/api/projects?page=2",
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 example="http://127.0.0.1:8000/api/projects",
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
     *         name="client_id",
     *         in="path",
     *         description="Client filter",
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
    public function index(Request $request): Response
    {
        $count = $request->count == '{count}' ? 10 : $request->count;

        $project = Project::with('client');

        if ($request->client_id && $request->client_id !== '{client_id}') {
            $project->where('client_id', (int) $request->client_id);
        }

        return response([
            'projects' => $project->paginate($count),
        ], 200);
    }

    /**
     *
     * @OA\Post (
     *     path="/projects",
     *     operationId="projects_store",
     *     tags={"Projects"},
     *     summary="Create Project",
     *     @OA\Response(
     *         response="201",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="project",
     *             type="object",
     *             ref="#/components/schemas/ProjectResource",
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
     *         @OA\JsonContent(ref="#/components/schemas/ProjectRequest")
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
            'domain' => 'required|unique:projects,domain|string|max:255',
            'client_id' => 'required_without:client|exists:clients,id',
            'client' => 'required_without:client_id|exists:clients,name',
            'ga_property_id' => 'max:20',
            'ua_property_id' => 'max:20',
            'ua_view_id' => 'max:20',
            'strategy'  => [
                'required',
                'max:255',
                Rule::in([ Project::GA_STRATEGY, Project::UA_STRATEGY, Project::NO_EXPAND_STRATEGY]),
            ],
        ]);

        $project = Project::create([
            'domain' => $fields['domain'],
            'ga_property_id' => $fields['ga_property_id'] ?? '',
            'ua_property_id' => $fields['ua_property_id'] ?? '',
            'ua_view_id' => $fields['ua_view_id'] ?? '',
            'client_id' => $fields['client_id'] ?? Client::where('name', $fields['client'])->firstOrFail()->id,
            'strategy' => $fields['strategy'] ?? Project::NO_EXPAND_STRATEGY,
        ]);

        return response([
            'project' => $project,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/projects/{project}",
     *     operationId="projects_show",
     *     tags={"Projects"},
     *     summary="Show Project",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="project",
     *             type="object",
     *             ref="#/components/schemas/ProjectResource",
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
     */
    public function show(Project $project): Response
    {
        return response([
            'project' => $project,
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/projects/{project}",
     *     operationId="projects_update",
     *     tags={"Projects"},
     *     summary="Update Project",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="project",
     *             type="object",
     *             ref="#/components/schemas/ProjectResource",
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
     *         name="project",
     *         in="path",
     *         description="The project id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProjectRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function update(Request $request, Project $project): Response
    {
        $fields = $request->validate([
            'domain' => [
                'string',
                'max:255',
                Rule::unique('projects')->ignore($project->id),
            ],
            'client_id' => 'exists:users,id',
            'client' => 'exists:clients,name',
            'ga_property_id' => 'max:20',
            'ua_property_id' => 'max:20',
            'ua_view_id' => 'max:20',
            'strategy'  => [
                'max:255',
                Rule::in([ Project::GA_STRATEGY, Project::UA_STRATEGY, Project::NO_EXPAND_STRATEGY]),
            ],
        ]);

        $project->fill($fields)->save();

        if (isset($fields['client'])) {
            $project->client_id = Client::where('name', $fields['client'])->firstOrFail()->id;
        }
        $project->save();

        return response([
            'project' => $project,
        ], 200);
    }

    /**
     * @OA\Delete (
     *     path="/projects/{project}",
     *     operationId="projects_delete",
     *     tags={"Projects"},
     *     summary="Delete Project",
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
     */
    public function destroy(Project $project): Response
    {
        $project->delete();

        return response([], 204);
    }
}
