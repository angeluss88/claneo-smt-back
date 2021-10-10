<?php

namespace App\Http\Controllers;

use App\Mail\SetPasswordLinkMail;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     security={
     *       {"bearerAuth": {}},
     *     },
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $fields = $request->validate([
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'roles' => 'array',
        ]);

        $fields['password'] = Str::random();

        /**
         * @var $user User
         */
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

        $response = [
            'user' => $user,
        ];

        return response($response, 201);
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
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        /**
         * @var $user User
         */
        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'Bad credentials',
            ], 401);
        }

        if($user->hasRole('Client') && !$user->privacy_policy_flag){
            return response([
                'message' => 'User should accept Privacy Policy first',
            ], 403);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        return response($response, 201);
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
     * @param Request $request
     * @return array
     */
    public function logout(Request $request): array
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
     * @param Request $request
     * @return Response
     */
    public function changePassword(Request $request): Response
    {
        $fields = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
        ]);

        $confirmation =  DB::table('password_resets')
            ->where('email', $fields['email'])
            ->where('token', $fields['token'])
            ->first();

        if($confirmation){
            $user = User::with('roles')->where('email', $fields['email'])->first();

            $user->forceFill(['password' => Hash::make($fields['password'])])->save();

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
}
