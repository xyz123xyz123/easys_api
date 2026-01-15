<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request; 
use App\Http\Controllers\Api\LoginController;   
use App\Http\Controllers\Api\MemberController;   
use App\Http\Controllers\Api\PdfController;   
use App\Http\Controllers\Api\DashboardController;   

// Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return response()->json($request->user());
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/profile', function (Request $request) {
//         return $request->user();
//     });

//     Route::post('/logout', function (Request $request) {
//         $request->user()->currentAccessToken()->delete();
//         return response()->json(['message' => 'Logged out']);
//     });
// });

// Login API
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);

// Member Current Bill Summary - To download PDF
Route::post('/get-billed-details', [MemberController::class, 'getDetailedBill']);

// Member Year Summary - For 12 month
Route::post('/get-my-bill-summary', [MemberController::class, 'getMyBillSummary']);

// Member Ledger Summary - PDF Report
Route::post('/get-member-ledger', [MemberController::class, 'getMemberLedgerDetails']);

// get Payment Summary Details
Route::post('/payment-summary', [MemberController::class, 'getPaymentSummary']);

// get Flat Units
Route::post('/get-flat_details', [MemberController::class, 'getFlatDetails']);

// get All details like bill/ledger/payment summary
Route::post('/get-dashboard-data', [DashboardController::class, 'getData']);

// get payment pdf
Route::post('/get-payment-pdf', [MemberController::class, 'memberPaymentInDetail']);

// view PDF
Route::get('/pdf/{type}/{filename}', [PdfController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/profile', [MemberController::class, 'profile']);
});


// SELECT GROUP_CONCAT(`flat_no`) FROM `members` WHERE `member_phone` = '9167737494' AND `user_id` IS NOT NULL LIMIT 50