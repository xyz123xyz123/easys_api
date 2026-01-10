<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request; 
use App\Http\Controllers\Api\LoginController;   
use App\Http\Controllers\Api\MemberController;   
use App\Http\Controllers\Api\PdfController;   

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


Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/get-billed-details', [MemberController::class, 'getDetailedBill']);
Route::post('/get-my-bill-summary', [MemberController::class, 'getMyBillSummary']);
Route::get('/pdf/{type}/{filename}', [PdfController::class, 'show']);

