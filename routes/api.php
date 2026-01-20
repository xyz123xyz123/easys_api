<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\PdfController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\LogoutController;

/*
|--------------------------------------------------------------------------
| Public APIs
|--------------------------------------------------------------------------
*/
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);

/*
|--------------------------------------------------------------------------
| Protected APIs
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {    
    Route::post('/get-my-bill-summary', [MemberController::class, 'getMyBillSummary']);
    Route::post('/get-billed-details', [MemberController::class, 'getDetailedBill']);
    Route::post('/get-member-ledger', [MemberController::class, 'getMemberLedgerDetails']);
    Route::post('/payment-summary', [MemberController::class, 'getPaymentSummary']);
    Route::post('/get-flat_details', [MemberController::class, 'getFlatDetails']);
    Route::post('/get-dashboard-data', [DashboardController::class, 'getData']);
    Route::post('/get-payment-pdf', [MemberController::class, 'memberPaymentInDetail']);
    Route::get('/pdf/{type}/{filename}', [PdfController::class, 'show']);
    Route::post('/get-switch-flat-details', [MemberController::class, 'getSwitchFlatDetails']);    
    Route::post('/logout', [LogoutController::class, 'logout']);
});

Route::post('/send-email', [EmailController::class, 'send']);
