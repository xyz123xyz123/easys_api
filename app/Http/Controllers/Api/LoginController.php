<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;
use App\Models\Otp;
use App\Http\Traits\SendOtpMail;
use Carbon\Carbon;

class LoginController extends Controller
{
    use SendOtpMail;

    /**
     * Login API
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|digits:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 200);
        }

        $mobileNo = $request->mobile_no;       

        // Fetch user
        $user = Member::where('member_phone', $mobileNo)
                    ->where('status', 1)
                    ->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid mobile number'
            ], 200);
        }

        // Fetch member info (existing logic preserved)
        $memberData = app(MemberController::class)
                        ->getMemberInfoByMobileNo($mobileNo);

        if (empty($memberData)) {
            return response()->json([
                'status'  => false,
                'message' => 'Member not found'
            ], 200);
        }

        // Log login
        // app(CommonController::class)->logUserLogIn($mobileNo);

        $otp   = random_int(1000, 9999);
        $email = trim($user->member_email ?? '');      
        $name  = trim($user->member_name ?? '');

        $email_status = false;
        $email_status_msg = 'Invalid Email ID';
        $mailSent = false;

        /**
         * Custom Email Validation (No Validator Used)
         */
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {

            // Send OTP Email
            $mailSent = $this->sendOtpMail($email, $name, $otp);

            if ($mailSent) {
                $email_status = true;
                $email_status_msg = 'Email sent successfully';
            } else {
                $email_status = false;
                $email_status_msg = 'Failed to send OTP email';
            }

        } else {
            $email_status = false;
            $email_status_msg = 'Invalid Email ID';
        }

        /**
         * Store OTP
         */
        Otp::create([
            'mobile_no'        => trim($mobileNo),
            'otp'              => Hash::make($otp),
            'otp_expires_at'   => now()->addMinutes(5),
            'otp_email_status' => $email_status
        ]);


        return response()->json([
            'status'  => true,
            'message' => 'Logged in successfully',
            'data'    => [
                'mobile_no'        => $mobileNo,
                'email_id'         => $email,
                'otp'              => $otp,
                'email_status'     => $email_status,
                'email_status_msg' => $email_status_msg
            ]
        ], 200);

    }

    /**
     * Forgot Password API
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no'     => 'required|string',
            'new_password'  => 'required|min:6',
            'conf_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        $mobileNo = $request->mobile_no;

        $user = User::where('username', $mobileNo)
                    ->where('is_active', 1)
                    ->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Mobile number does not exist'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Password updated successfully'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $mobileNo = trim($request->mobile_no);
        $otp      = trim($request->otp);

        // Custom basic validation
        if (empty($mobileNo) || empty($otp)) {
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'Mobile number and OTP are required',
                'data'    => []
            ], 400);
        }

        // Fetch latest OTP
        $otpRecord = Otp::where('mobile_no', $mobileNo)
                        ->latest()
                        ->first();

        if (!$otpRecord) {
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'OTP not found. Please request a new OTP.',
                'data'    => []
            ], 404);
        }

        // Check expiry
        if (Carbon::now()->gt($otpRecord->otp_expires_at)) {
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'OTP has expired. Please request a new one.',
                'data'    => []
            ], 410);
        }

        // Verify OTP
        if (!Hash::check($otp, $otpRecord->otp)) {
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'Invalid OTP',
                'data'    => []
            ], 401);
        }

        $memberData = (new MemberController)->getMemberInfoByMobileNo($mobileNo);

        // Prepare default response data
        $data = [
            'mobile_no' => $mobileNo,
            'verified'  => true,
            'member_id' => null,
            'member_name' => null,
            'society_id'  => null,
            'society_name'=> null,
            'token'       => null,
            'token_type'  => 'Bearer',
        ];

        // If member exists, safely map values
        if (!empty($memberData) && is_array($memberData)) {

            /**
             * 🔐 Find Member by Mobile Number
             */
            $member = Member::where('member_phone', $mobileNo)->first();

            if (!$member) {
                return response()->json([
                    'status'  => config('constants.UNSUCCESS'),
                    'message' => 'Member not found',
                    'data'    => []
                ], 404);
            }

            /**
             * 🗑️ Revoke old tokens (optional but recommended)
             */
            $member->tokens()->delete();

            /**
             * 🔑 Generate Sanctum Token
             */
            $token = $member->createToken('mobile-otp-login')->plainTextToken;

            $data = [
                'mobile_no'   => $mobileNo,
                'verified'    => true,
                'member_id'   => $member->id,
                'member_name' => $member->member_name,
                'society_id'  => $member->society_id ?? null,
                'society_name'=> $member->society->society_name ?? null, // if relationship exists
                'token'       => $token,
                'token_type'  => 'Bearer'
            ];
        }

        // Mark OTP as verified (instead of delete)
        $otpRecord->update(['is_verified' => 1]);

        return response()->json([
            'status'  => config('constants.SUCCESS'),
            'message' => 'OTP verified successfully',
            'data'    => $data
        ], 200);
    }


}
