<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\ForgotPasswordLinkMail;
use App\Mail\SetPasswordLinkMail;
use App\Models\Client;
use App\Http\Requests\ForgotPasswordRequest;
use App\Models\Event;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mail;
use Str;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     operationId="register",
     *     description="Accessible for only User with the 'Admin' Role",
     *     tags={"Auth"},
     *     summary="Register new user by admin",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Client not found",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param RegisterRequest $request
     * @return Response
     */
    public function register(RegisterRequest $request): Response
    {
        $fields = $request->validated();
        $fields['password'] = Str::random();

        $user = User::create([
            'email' => $fields['email'],
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'password' => Hash::make($fields['password']),
        ]);

        if(isset($fields['roles']) && !empty($fields['roles'])) {
            $user->roles()->sync($fields['roles']);
            $user->save();
        }

        if($user->hasRole('Client')) {
            $client = null;
            if (isset($fields['client'])) {
                $client = Client::whereName($fields['client'])->first();
            }

            if ($client) {
                $user->client_id = $client->id;
            } else if(isset($fields['client_id'])) {
                $user->client_id = $fields['client_id'];
            }

            if($user->client_id) {
                $user->save();
            }
        }

        $token = Str::random();

        DB::table('password_resets')->insert([
            'email' => $fields['email'],
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $details = [
            'firstname' => $user['first_name'],
            'email' => $user['email'],
            'password' => $fields['password'],
            'url' => env('FRONT_URL') . env('CHANGE_PASSWORD_LINK') . '/' . $token,
        ];

        Mail::to($user['email'])->send(new SetPasswordLinkMail($details));

        Event::create([
            'user_id' => \Auth::user()->id,
            'entity_type' => User::class,
            'entity_id' => $user->id,
            'action' => Event::CREATE_ACTION,
            'data' =>  $request->validated(),
            'oldData' => [],
        ]);

        return response([
            'user' => $user,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Login to get Auth Token",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(ref="#/components/schemas/LoginResource")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Bad Credentials",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="User should accept Privacy Policy first",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     * )
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response()->json([
                'message' => 'Bad credentials',
            ], 401);
        }

        if($user->is_superadmin != 1 && $user->hasRole('Client') && !$user->privacy_policy_flag){
            return response()->json([
                'message' => 'User should accept Privacy Policy first',
            ], 403);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        return response()->json($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Logout to remove Auth Token",
     *     @OA\Response(
     *         response="200",
     *         description="Logged out",
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
     * @return array
     */
    public function logout(): array
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }

    /**
     * @OA\Post(
     *     path="/change-pwd",
     *     operationId="change-pwd",
     *     tags={"Auth"},
     *     summary="Change password for non-logged user",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="user",
     *             type="object",
     *             ref="#/components/schemas/UserResource",
     *         )),
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="The given data was invalid",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChangePasswordRequest")
     *     ),
     * )
     *
     * @param ChangePasswordRequest $request
     * @return Response
     */
    public function changePassword(ChangePasswordRequest $request): Response
    {
        $fields = $request->validated();

        $confirmation =  DB::table('password_resets')
            ->where('email', $fields['email'])
            ->where('token', $fields['token'])
            ->first();

        if($confirmation){
            $user = User::with('roles')->where('email', $fields['email'])->first();

            $user->forceFill(['password' => Hash::make($fields['password'])]);

            if(isset($fields['privacy_policy_flag'])) {
                $user->fill([
                    'privacy_policy_flag' => $fields['privacy_policy_flag'],
                ]);
            }

            $user->save();

            DB::table('password_resets')
                ->where('email', $fields['email'])
                ->where('token', $fields['token'])
                ->delete();

            return response([
                'user' => $user,
            ], 200);
        }

        return response([
            'message' => 'The given data is invalid',
        ], 422);
    }

    /**
     * @OA\Post(
     *     path="/forgot-pwd",
     *     operationId="forgot-pwd",
     *     tags={"Auth"},
     *     summary="Send reset password link",
     *     @OA\Response(
     *         response="200",
     *         description="Everything is fine",
     *         @OA\JsonContent(
     *             @OA\Property(
     *             property="user",
     *             type="object",
     *             ref="#/components/schemas/UserResource",
     *         )),
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="The given data was invalid",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ForgotPasswordRequest")
     *     ),
     * )
     *
     * @param ForgotPasswordRequest $request
     * @return Response
     */
    public function forgotPassword(ForgotPasswordRequest $request): Response
    {
        $fields = $request->validated();

        $user = User::where('email', $fields['email'])->first();

        if(!$user){
            return response([
                'message' => 'The given data is invalid',
            ], 422);
        }

        $token = Str::random();
        $created_at = Carbon::now();

        DB::table('password_resets')->insert([
            'email' => $fields['email'],
            'token' => $token,
            'created_at' => $created_at,
        ]);

        $details = [
            'firstname' => $user['first_name'],
            'email' => $user['email'],
            'url' => env('FRONT_URL') . env('RESET_PASSWORD_LINK') . '/' . $token,
            'valid_until' => $created_at->addDay()->format('Y-m-d H:i'),
        ];

        if(!isset($fields['prevent_send']) ) {
            Mail::to($user['email'])->send(new ForgotPasswordLinkMail($details));
        }

        return response([
            'user' => $user,
        ], 200);
    }
}
