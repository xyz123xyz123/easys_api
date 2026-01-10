<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Login API
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|string',
            'password'  => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        $mobileNo = $request->mobile_no;
        $password = $request->password;

        // Fetch user
        $user = User::where('username', $mobileNo)
                    ->where('is_active', 1)
                    ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid mobile number or password'
            ], 401);
        }

        // Fetch member info (existing logic preserved)
        $memberData = app(MemberController::class)
                        ->getMemberInfoByMobileNo($mobileNo);

        if (empty($memberData)) {
            return response()->json([
                'status'  => false,
                'message' => 'Member not found'
            ], 404);
        }

        // Log login
        app(CommonController::class)->logUserLogIn($mobileNo);

        return response()->json([
            'status'  => true,
            'message' => 'Logged in successfully',
            'data'    => [
                'member_data' => [
                    'member_id'    => $memberData['member_id'],
                    'member_name'  => $memberData['member_name'],
                    'society_id'   => $memberData['society_id'],
                    'society_name' => $memberData['society_name']
                ]
            ]
        ]);
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
}
