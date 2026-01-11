<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Config;

class DashboardController extends Controller
{
    public function getData(Request $request){

        $validation = Validator::make($request->all(),[
        'member_id' => 'required',
        'society_id' => 'required',
        'is_pdf_required' => 'required',
        'mobile_no' => 'required'
        ]);
        
        if($validation->fails()){
            return response()->json([
                'status'  => config('constants.MISSINGPARAMETER'),
                'message' => implode(', ', $validation->errors()->all()),
                'data'    => null
            ], 422);
        } 

        $member_id  = trim((string) $request->input('member_id'));
        $society_id = trim((string) $request->input('society_id'));
        $mobile_no  = trim((string) $request->input('mobile_no'));

        $is_pdf_required = filter_var(
            $request->input('is_pdf_required'),
            FILTER_VALIDATE_BOOLEAN
        );
        

        $bill_response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post(
            rtrim(config('app.url'), '/') . '/api/get-my-bill-summary',
            [
                'member_id'  => $member_id,
                'society_id' => $society_id,
            ]
        );

        $ledger_response = Http::withHeaders([     
        'Accept' => 'application/json',
        ])->post(rtrim(config('app.url'), '/') . '/api/get-member-ledger', [
            'member_id' => $member_id,'is_pdf_required' => $is_pdf_required
        ]);


        $payment_response = Http::withHeaders([     
        'Accept' => 'application/json',
        ])->post(rtrim(config('app.url'), '/') . '/api/payment-summary', [
            'member_id' => $member_id,'society_id' => $society_id
        ]);  

        $flat_response = Http::withHeaders([     
        'Accept' => 'application/json',
        ])->post(rtrim(config('app.url'), '/') . '/api/get-flat_details', [
            'mobile_no' => $mobile_no
        ]);

        
        $data['bill_summary'] = $bill_response->json();
        $data['ledger_summary'] = $ledger_response->json();
        $data['payment_summary'] = $payment_response->json();
        $data['flat_summary'] = $flat_response->json();

        // return $bill_response->json();
        // return $data;
        return response()->json([
            'status'  => config('constants.SUCCESS'),
            'message' => 'Dashboard Details fetched successfully',
            'data'    => $data
        ], 200);
    }
}