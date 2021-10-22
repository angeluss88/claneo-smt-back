<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Change Password Request",
 *     description="Change Password Request",
 * )
 */
class ChangePasswordRequest
{
    /**
     * @OA\Property(
     *     title="Email",
     *     description="Email of user",
     *     example="user@loc",
     * )
     *
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *     title="Password",
     *     description="Password of user",
     *     example="12345",
     * )
     *
     * @var string
     */
    public $password;

    /**
     * @OA\Property(
     *     title="password_confirmation",
     *     description="Enter the same password again",
     *     example="12345",
     * )
     *
     * @var string
     */
    public $password_confirmation;

    /**
     * @OA\Property(
     *     title="Token",
     *     description="Token from Email",
     *     example="noC1CHhmimTuwFpx",
     * )
     *
     * @var string
     */
    public $token;

    /**
     * @OA\Property(
     *     title="privacy_policy_flag",
     *     description="Has the user read the privacy policy?",
     *     example=1,
     * )
     *
     * @var integer
     */
    public $privacy_policy_flag;
}
