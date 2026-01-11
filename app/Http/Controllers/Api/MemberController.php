<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Token;
use App\Models\Member;
use App\Models\Society;
use App\Models\Payment;
use App\Models\BankMaster;
use App\Models\PaymentMode;
use App\Models\MemberBillSummary;
use App\Models\Building;
use App\Models\BillSettlement;
use App\Models\OnlinePayments;

use App\Http\Traits\SetFinancialYearDataTrait;
use Mail;
use DB;
use Validator;
use Config;

class MemberController extends Controller {
    use SetFinancialYearDataTrait;  
    
    public $startDate;
    public $endDate; 
    
    
    public function __construct(){
        $financialYearData = $this->setFinancialYearData();
        $this->startDate = $financialYearData['year-start-date'];
        $this->endDate = $financialYearData['year-end-date'];
    }
    
    public function getMemberInfoByMobileNo($mobileNo){
        $memberData = Member::join('societies','members.society_id','=','societies.id')->select('members.id as member_id','member_name','society_id','society_name')->where('member_phone',$mobileNo)->first();
        return (!empty($memberData)) ? $memberData->toArray() : [];
    }
    
    public function getMemberInfoById($memberId){
        $memberData = Member::join('societies','members.society_id','=','societies.id')->select('members.id as member_id','member_name','society_id','society_name')->where('members.id',$memberId)->first();
        return (!empty($memberData)) ? $memberData->toArray() : [];
    }
    
    public function getMyBillSummary(Request $request){
        
        $validation = Validator::make($request->all(),[
            'member_id' => 'required',
            'society_id' => 'required'
            ]);
        if($validation->fails()){
            return response()->json([
                'status'  => config('constants.MISSINGPARAMETER'),
                'message' => implode(', ', $validation->errors()->all()),
                'data'    => null
            ], 422);

        }
        
        $memberId = $request->input('member_id');
        $societyId = $request->input('society_id');
        $upatedMemeberBillSummary = [];
        
        $memberBillSummary = MemberBillSummary::where('member_id',$memberId)->where('society_id',$societyId)
        ->select('bill_no','bill_type','month as bill_month','bill_due_date','bill_generated_date','bill_end_date','bill_frequency_id','balance_amount','amount_payable','monthly_bill_amount','tax_total')
        ->whereBetween('bill_generated_date',[$this->startDate,$this->endDate])->orderby('bill_no','desc')->get();
       
        if(!empty($memberBillSummary)){
            foreach($memberBillSummary as $summary){
                $billFreqId = $summary['bill_frequency_id'];
                $billMonth = $summary['bill_month'];
                $billGeneratedDate = $summary['bill_generated_date'];
                $billEndDate = $summary['bill_end_date'];
                $billDueDate = $summary['bill_due_date'];
                $balAmt = $summary['balance_amount'];
                $billYear = date('Y',strtotime($billGeneratedDate));
                $summary['payment_status'] = ($balAmt > 0) ? 0 : 1;
                $summary['bill_for'] = (new CommonController)->societyBillingFrequency($billFreqId,$billMonth).' '.$billYear;
                $summary['bill_duration'] = dateFormatMMDDYY($billGeneratedDate).'-'.dateFormatMMDDYY($billEndDate);
                $summary['bill_due_date'] = dateFormatMMDDYY($billDueDate);
                $summary['bill_type'] =  ($summary['bill_type'] == 'reg') ? 'Regular' : 'Supplementary';
                
                array_push($upatedMemeberBillSummary,$summary);
            }
            
            return response()->json([
                'status'  => config('constants.SUCCESS'),
                'message' => 'My Bill Summary Data fetched successfully',
                'data'    => $upatedMemeberBillSummary
            ], 200);

        }
        
        return response()->json([
            'status'  => config('constants.UNSUCCESS'),
            'message' => 'Member Bill Not Found',
            'data'    => $upatedMemeberBillSummary
        ], 404);

    }    
    
    public function  getDetailedBill(Request $request){
        $validation = Validator::make($request->all(),[
            'member_id' => 'required',
            'society_id' => 'required',
            'bill_no' => 'required',
            'bill_month' => 'required'
            ]);
            
            if ($validation->fails()) {
                return response()->json([
                    'status'  => config('constants.MISSINGPARAMETER'),
                    'message' => implode(', ', $validation->errors()->all()),
                    'data'    => null
                ], 422);
            }
   
        
        $memberId = $request->input('member_id');
        $societyId = $request->input('society_id');        
        $billNo = $request->input('bill_no');
        $billMonth = $request->input('bill_month');
        $type = $request->has('type') ? $request->input('type') : '';
        $billDetailedData = [];
        
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        $sql = "select summaries.member_id,month,bill_due_date,bill_generated_date,bill_no,bill_type,monthly_amount,amount_payable,op_principal_arrears,monthly_bill_amount,interest_on_due_amount,
            op_interest_arrears,igst_total,cgst_total,sgst_total,principal_adjusted,interest_adjusted,discount,op_due_amount,tax_total
            ,member_prefix,member_name,member_email,flat_no,residential,unit_type,floor_no,igst_total,cgst_total,sgst_total,principal_adjusted,interest_adjusted,discount,
                society_name,society_code,registration_no,registration_date,email_id,address,telephone_no,authorised_person,bill_note,show_bills_in_receipt,special_field,title,amount,bill_frequency_id,wing_id,area    from 
            (
                select member_id,month,bill_due_date,bill_generated_date,bill_no,bill_type,amount_payable,op_principal_arrears,society_id,bill_frequency_id,monthly_bill_amount,interest_on_due_amount,
                op_interest_arrears,igst_total,cgst_total,sgst_total,principal_adjusted,interest_adjusted,discount,op_due_amount,tax_total,monthly_amount from member_bill_summaries 
                where bill_no = $billNo and society_id = $societyId and bill_generated_date between  '$startDate' and '$endDate'
            )  as summaries
            join (
	            select id ,member_prefix,member_name,member_email,flat_no,residential,unit_type,floor_no,wing_id,area from members where id = $memberId limit 1
            ) as member
            on summaries.member_id = member.id
            join (
	            select id,society_name,society_code,registration_no,registration_date,email_id,address,telephone_no,authorised_person from societies where id = $societyId limit 1
            ) as society on society.id = summaries.society_id
            join (
	            select bill_note,show_bills_in_receipt,society_id,special_field from society_parameters where society_id = $societyId limit 1
            ) as parameter on parameter.society_id = summaries.society_id
            left join (
                select title,amount,member_id from member_bill_generates  join society_ledger_heads  on member_bill_generates.ledger_head_id = society_ledger_heads.id 
                where member_bill_generates.society_id = $societyId and month = $billMonth and amount > 0
	       ) leaderHeads on summaries.member_id = leaderHeads.member_id";

        //    echo $sql;die;
        
        $billDetailed = DB::select($sql);

	   if(!empty($billDetailed)){
	       $firstIndexBillRecord = (array)$billDetailed[0];
	       $emailId = $firstIndexBillRecord['member_email'];
	       $billDetailedData['society_data'] = [
	           'name' => $firstIndexBillRecord['society_name'],
	           'registeration_no' => $firstIndexBillRecord['registration_no'],
	           'registeration_date' => $firstIndexBillRecord['registration_date'],
	           'address' => $firstIndexBillRecord['address'],
	           'email_id' => $firstIndexBillRecord['email_id'],
	           'telephone_no' => $firstIndexBillRecord['telephone_no'],
	           'authorized_persion'=> $firstIndexBillRecord['authorised_person']
	           ];
	           
	       $billDetailedData['member_data'] = [
	           'member_id' => $firstIndexBillRecord['member_id'],
	           'prefix' => $firstIndexBillRecord['member_prefix'],
	           'name' => $firstIndexBillRecord['member_name'],
	           'flat_no' => $firstIndexBillRecord['flat_no'],
	           'unit_type' => $firstIndexBillRecord['unit_type'],
	           'floor_no' => $firstIndexBillRecord['floor_no'],
	           'area' => $firstIndexBillRecord['area'],
	           'wing_id' => $firstIndexBillRecord['wing_id']	           
	           ];
	       $billMonth = $firstIndexBillRecord['month'];
	       $billFequenyId = $firstIndexBillRecord['bill_frequency_id'];
	       $billGeneratedDate = $firstIndexBillRecord['bill_generated_date'];
	       $billYear = date('Y',strtotime($billGeneratedDate));
	       $billDetailedData['bill_data'] = [
	           'bill_month' => $billMonth,
	           'bill_for' => (new CommonController)->societyBillingFrequency($billFequenyId,$billMonth).' '.$billYear,
	           'bill_no' => $firstIndexBillRecord['bill_no'],
	           'bill_date' => dateFormatMMDDYY($firstIndexBillRecord['bill_generated_date']),
	           'bill_due_date' => dateFormatMMDDYY($firstIndexBillRecord['bill_due_date']),
	           'bill_amount' => $firstIndexBillRecord['monthly_amount']-$firstIndexBillRecord['tax_total'],
	           'amount_payable' => (new CommonController)->convertToDrCr($firstIndexBillRecord['amount_payable']),
	           'principal_arrears' =>(new CommonController)->convertToDrCr($firstIndexBillRecord['op_principal_arrears']),
	           'interest_arrears' =>(new CommonController)->convertToDrCr($firstIndexBillRecord['op_interest_arrears']),
	           'interest' =>$firstIndexBillRecord['interest_on_due_amount'],
	           'principal_credit' => (new CommonController)->convertToDrCr($firstIndexBillRecord['op_due_amount']),
	           'less_adjustment' => $firstIndexBillRecord['principal_adjusted']+$firstIndexBillRecord['interest_adjusted']+$firstIndexBillRecord['discount'],
	           'bill_type' =>  ($firstIndexBillRecord['bill_type'] == 'reg') ? 'Regular' : 'Supplementary',
	           'igst_total' =>$firstIndexBillRecord['igst_total'],
	           'cgst_total' =>$firstIndexBillRecord['cgst_total'],
	           'sgst_total' =>$firstIndexBillRecord['sgst_total'],
	           'amount_payable_in_words' => numberTowordsEnglish($firstIndexBillRecord['amount_payable'])
	           ];
	       
	       $billDetailedData['society_paramter'] = [
	           'bill_note' => $firstIndexBillRecord['bill_note']
	           ];
	        $paymentData = $this->memberPaymentSummary($memberId);
    	    $billDetailedData['payment_data']['payments'] = $paymentData;	        
	        if(!empty($paymentData)){
                $totalAmountPaid = array_sum(array_column($paymentData,'amount_paid'));
                $billDetailedData['payment_data']['total_paid'] = $totalAmountPaid;
                $billDetailedData['payment_data']['total_paid_in_words'] = "RUPESS ".numberTowordsEnglish($totalAmountPaid);	           	        
	        }

            $billDetailedData['tarrif_data'] = [];
            foreach($billDetailed as $data){
                $tarrif = [
                    'tarrif' => $data->title,
                        'tarrif_amount' =>  $data->amount
                    ];
                    array_push($billDetailedData['tarrif_data'],$tarrif);
            }
            $fileData = (new PdfController)->generateBillPdf($billDetailedData);

                if(empty($fileData))
                    return response()->json([
                        'status'  => config('constants.UNSUCCESS'),
                        'message' => 'Failed to generate PDF',
                        'data'    => null
                    ], 500);                    
                                      
                
                // $fileData['bill_data'] = $billDetailedData['bill_data'];
                return response()->json([
                    'status'  => config('constants.SUCCESS'),
                    'message' => '',
                    'data'    => $fileData
                ], 200);

	        }
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => '',
                'data'    => $billDetailedData
            ], 200);


    }
    
    public function getCurrentBill(Request $request){
        $validation = Validator::make($request->all(),[
            'member_id' => 'required',
            'society_id' => 'required'
            ]);
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }  
        
        
        $memberId = $request->input('member_id');
        $societyId = $request->input('society_id');
        $upatedMemeberBillSummary = [];
        $billDetailedData['bill_data'] = [];
        $memberBillSummary = MemberBillSummary::where('member_id',$memberId)->where('society_id',$societyId)->select('*')->whereBetween('bill_generated_date',[$this->startDate,$this->endDate])->orderby('bill_no','desc')->get()->first();        
        if(!empty($memberBillSummary)){
            $memberBillSummary = $memberBillSummary->toArray();
            $billFequenyId = $memberBillSummary['bill_frequency_id'];
            $billMonth = $memberBillSummary['month'];
            $billGeneratedDate = $memberBillSummary['bill_generated_date'];
            $billEndDate = $memberBillSummary['bill_end_date'];            
            $billYear = date('Y',strtotime($billGeneratedDate));
            $billDetailedData['bill_data'] = [
	           'bill_month' => $billMonth,
	           'bill_for' => (new CommonController)->societyBillingFrequency($billFequenyId,$billMonth).' '.$billYear,
	           'bill_no' => $memberBillSummary['bill_no'],
	           'bill_date' => dateFormatMMDDYY($memberBillSummary['bill_generated_date']),
	           'bill_due_date' => dateFormatMMDDYY($memberBillSummary['bill_due_date']),
	           'bill_amount' => (int)$memberBillSummary['monthly_bill_amount'], // -$memberBillSummary['tax_total'],
	           'amount_payable' => $memberBillSummary['amount_payable'],
	           'principal_arrears' =>$memberBillSummary['op_principal_arrears'],
	           'interest_arrears' =>$memberBillSummary['op_interest_arrears'],
	           'interest' =>$memberBillSummary['interest_on_due_amount'],
	           'principal_credit' => $memberBillSummary['op_due_amount'],
	           'less_adjustment' => $memberBillSummary['principal_adjusted']+$memberBillSummary['interest_adjusted']+$memberBillSummary['discount'],
	           'bill_type' =>  ($memberBillSummary['bill_type'] == 'reg') ? 'Regular' : 'Supplementary',
	           'igst_total' =>$memberBillSummary['igst_total'],
	           'cgst_total' =>$memberBillSummary['cgst_total'],
	           'sgst_total' =>$memberBillSummary['sgst_total'],
	           'payment_status' => ($memberBillSummary['balance_amount'] > 0) ? 0 : 1,
	           'bill_duration' => dateFormatMMDDYY($billGeneratedDate).'-'.dateFormatMMDDYY($billEndDate),
	           'amount_payable_in_words' => numberTowordsEnglish($memberBillSummary['amount_payable']),
	           'balance_amount' => $memberBillSummary['balance_amount']
	           ];            
            $billNo = $memberBillSummary['bill_no'];
            $billMonth = $memberBillSummary['month'];
            
            return response_json(SUCCESS,'',$billDetailedData);
        }
        
        return response_json(UNSUCCESS,'Member Bill Not Available');
    }
    
    public function getPaymentSummary(Request $request){
        
        $validation = Validator::make($request->all(),[
            'member_id' => 'required',
            'society_id' => 'required'
            ]);
        if($validation->fails()){
            return response()->json([
                'status'  => config('constants.MISSINGPARAMETER'),
                'message' => implode(', ', $validation->errors()->all()),
                'data'    => null
            ], 422);

        }      
        
        $memberId = $request->input('member_id');
        $societyId = $request->input('society_id');          
        $paymentResponse = [];
        $paymentResponse['payment_data'] = [];
        $memberPaymentDetails = $this->memberPaymentSummary($memberId);
        $memberPaymentDetails1 = $memberPaymentDetails;
        if(!empty($memberPaymentDetails)){
            $totalAmountPaid = array_sum(array_column($memberPaymentDetails,'amount_paid'));
            $paymentFirstIndexData = $memberPaymentDetails[0];    
            $paymentResponse['payment_data'] = $memberPaymentDetails1;
            $paymentResponse['total_paid'] = $totalAmountPaid;
            $paymentResponse['total_paid_in_words'] = numberTowordsEnglish($totalAmountPaid);
            $paymentResponse['member_data'] = [
                'member_prefix' => $paymentFirstIndexData['member_prefix'],
                'member_name' => $paymentFirstIndexData['member_name'],
                'flat_no' => $paymentFirstIndexData['flat_no']
                ];
            
            return response()->json([
                'status'  => config('constants.SUCCESS'),
                'message' => 'Payments fetched successfully',
                'data'    => $paymentResponse
            ], 200);

        }
        else
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'Payments not available',
                'data'    => $paymentResponse
            ], 404);

    }
    
    public function memberPaymentSummary($memberId){
        return Payment::leftjoin('banks','member_payments.member_bank_id','=','banks.id')
        ->join('members','member_payments.member_id','=','members.id')
        ->join('payment_modes','member_payments.payment_mode','=','payment_modes.id')
        ->select('member_prefix','member_name','flat_no','member_payments.id as payment_id','receipt_id','cheque_reference_number','narration','amount_paid',DB::raw("date_format(payment_date,'%d-%m-%Y') as payment_date"),DB::raw("date_format(entry_date,'%d-%m-%Y') entry_date"),'bank_name','payment_modes.payment_mode')
        ->where('member_id',$memberId)->whereBetween('payment_date',[$this->startDate,$this->endDate])->orderby('payment_date','desc')->get()->toArray();        
    }
    
    public function memberPaymentInDetail(Request $request){
        $validation = Validator::make($request->all(),[
            'payment_id' => 'required'
            ]);
        
        if($validation->fails()){
            return response()->json([
                'status'  => config('constants.MISSINGPARAMETER'),
                'message' => implode(', ', $validation->errors()->all()),
                'data'    => null
            ], 422);
        }  
        
        $paymentId = $request->input('payment_id');
        // $type = $request->input('type');
        $paymentDetails =  Payment::leftjoin('banks','member_payments.member_bank_id','=','banks.id')->where('member_payments.id',$paymentId)
        ->select('member_payments.id as payment_id','receipt_id','cheque_reference_number','narration',
         'amount_paid',DB::raw("date_format(payment_date,'%d-%m-%Y') as payment_date"),DB::raw("date_format(entry_date,'%d-%m-%Y') entry_date"),'bank_name','payment_modes.payment_mode','members.id as member_id',
         'member_prefix','member_name','member_email','society_name','address','email_id','telephone_no','registration_no',DB::raw("date_format(registration_date,'%d-%m-%Y') registration_date"),'flat_no')
        ->join('payment_modes','member_payments.payment_mode','=','payment_modes.id')
        ->join('members','member_payments.member_id','=','members.id')
        ->join('societies','member_payments.society_id','=','societies.id')
        ->whereBetween('payment_date',[$this->startDate,$this->endDate])
        ->get()->first();          

        if(!empty($paymentDetails)){
            $paymentDetails = $paymentDetails->toArray();
            $fileData = (new PdfController)->generateReciptPdf($paymentDetails);
            if(!empty($fileData)){
                if(empty($fileData)){
                    return response()->json([
                            'status'  => config('constants.UNSUCCESS'),
                            'message' => 'Failed to generate PDF',
                            'data'    => null
                        ], 500);

                }
                else{
                    $emailId = $paymentDetails['member_email'];
                    // if($type == 'email'){
                    //     if(!empty($emailId)){
                    //         $fileName = $fileData['file_path'];
                    //         $sendStatus = $this->sendEmail($emailId,$fileName,'Payment Receipt');
                    //         if($sendStatus){
                    //             return response_json(SUCCESS,"Email Sent To $emailId",$fileData);
                    //         }
                    //         else{
                    //             return response_json(SUCCESS,"Email Not Sent");
                    //         }
                    //     }
                    //     else{
                    //         return response_json(UNSUCCESS,'Email Id Not Registered'); 
                    //     }
                    // }                    
                }
                return response()->json([
                    'status'  => config('constants.SUCCESS'),
                    'message' => 'Payment Details Fetched Suceessfully',
                    'data'    => $fileData
                ], 200);

            }            
            return response()->json([
                'status'  => config('constants.SUCCESS'),
                'message' => '',
                'data'    => $paymentDetails
            ], 200);

        }
        else{
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'Payment details not found',
                'data'    => $paymentDetails
            ], 404);

        }
    }
    
    public function getMemberLedgerDetails(Request $request){
        $validation = Validator::make($request->all(),[
            'member_id' => 'required',
            'is_pdf_required' => 'required||in:1,true'
            ]);
        
        if($validation->fails()){
          return response()->json([
                'status'  => config('constants.MISSINGPARAMETER'),
                'message' => implode(', ', $validation->errors()->all()),
                'data'    => null
            ], 422);
        }  
        
        
        $startDate = $this->startDate;
        $endDate = $this->endDate;
                
        $memberId = $request->input('member_id');
        $isPdfReq = $request->input('is_pdf_required');
        
        $memberData = Member::join('societies','members.society_id','=','societies.id')->leftjoin('buildings','members.society_id','=','buildings.society_id')->select('members.id as member_id','member_name','member_email','members.society_id','society_name','op_principal',
        'op_tax','op_interest','member_transfer','supplementary_principal','supplementary_interest','supplementary_tax','op_bill_date','op_bill_due_date','address',
        'email_id','telephone_no','registration_no','building_name','flat_no','wing_id')->where('members.id',$memberId)->first();        
        
        if(empty($memberData)){
          return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'Member data not available',
                'data'    => null
            ], 404);

        }
               
        
        $openingData = $memberData->toArray();
        $memberTransfer = $openingData['member_transfer'];
        $emailId = $openingData['member_email'];
        if($memberTransfer){
             $openingData['op_principal'] = 0;
             $openingData['op_tax'] = 0;
             $openingData['op_interest'] = 0;
        }   
        
        $openingBal = $openingData['op_principal']+$openingData['op_tax']+$openingData['op_interest']+$openingData['supplementary_principal']+$openingData['supplementary_interest']+$openingData['supplementary_tax'];
        $opDebit = 0.00;
        $opCredit = 0.00;
        if($openingBal < 0)
            $opCredit = $openingBal;
        else 
            $opDebit = $openingBal;
            
        $currentYear = date('Y');
        $currentMonth = date('m');
        $opeingDate = '01-04-'.($currentMonth < 4 ? $currentYear-1 : $currentYear);
        $totalDebit = 0;
        $totalCredit = 0;        
        $opening = [
             'date' => date('d-m-Y',strtotime($opeingDate."-1 days")), // just a patch work to bring opening data at the top,
            'formattedDate' => $opeingDate,
            'particular' => 'Opening Amount',
            'debit' => $opDebit,
            'credit' => abs($opCredit),
            ];
            
        $totalDebit = $totalDebit +  $opening['debit'];
        $totalCredit = $totalCredit + $opening['credit'];     

        $ledgerData = DB::table('member_bill_summaries as mbs')->select('mbs.id as bill_summary_id','bill_no','bill_type','monthly_bill_amount','mbs.bill_generated_date','mbs.bill_no','mbs.monthly_bill_amount','bill_type','bill_frequency_id','month','pay.*','crd.*','jv_debit.*','jv_credit.*')
            ->leftjoin(DB::raw("(select id as pay_id,member_id,cheque_reference_number,payment_date,amount_paid,receipt_id from member_payments where member_id = $memberId and member_transfer = $memberTransfer and payment_date between '$startDate' and '$endDate') pay"),function($join){
            $join->on('mbs.member_id','=','pay.member_id');
        })
        ->leftjoin(DB::raw("(select cr_id,member_id,cheque_no,cheque_return_date,cheque_amount from cheque_return_details where member_id = $memberId ) crd"),function($join){
            $join->on('pay.cheque_reference_number','=','crd.cheque_no');
        })
        ->leftjoin(DB::raw("(select id as jv_debit_id,jv_debit_member_head_id,voucher_date as jv_debit_voucher_date,voucher_no as jv_debit_voucher_no,note as jv_debit_note,jv_amount_credited as jv_debit_amount from journal_vouchers where jv_debit_member_head_id = $memberId and voucher_date between '$startDate' and '$endDate') jv_debit"),function($join){
            $join->on('mbs.member_id','=','jv_debit.jv_debit_member_head_id');
        })
        ->leftjoin(DB::raw("(select id as jv_credit_id,jv_credit_member_head_id,voucher_date as jv_credit_voucher_date,voucher_no as jv_credit_vouchar_no,note as jv_credit_note,jv_amount_credited as jv_credit_amount from journal_vouchers where jv_credit_member_head_id = $memberId and voucher_date between '$startDate' and '$endDate') jv_credit"),function($join){
            $join->on('mbs.member_id','=','jv_credit.jv_credit_member_head_id');
        })->where('mbs.member_id',$memberId)->where('mbs.member_transfer',$memberTransfer)->orderby('mbs.bill_generated_date')->get();
        
       
        $ledgerFinal = [];
        array_push($ledgerFinal,$opening);
        if(!empty($ledgerData)){
            $billSummary = [];
            $billSummaryIdArr = [];
            $payments = [];
            $paymentIdArr = [];
            $chequeRetData = []; 
            $chequeRetIdArr = [];
            $jvCred  = [];  
            $jvCredIdArr = [];
            $jvDebit  = [];   
            $jvDebitIdArr = [];
            foreach($ledgerData as $data){
                $data = (array)$data;
                $billSummaryId = $data['bill_summary_id'];
                $chequeRetId = $data['cr_id'];
                $paymentId = $data['pay_id'];
                $jvCredId = $data['jv_debit_id'];
                $jvDebitId = $data['jv_credit_id'];
                $billMonth = $data['month'];
                $billFrequencyId = $data['bill_frequency_id'];
                if(!empty($billSummaryId) && !in_array($billSummaryId,$billSummaryIdArr)){
                    array_push($billSummaryIdArr,$billSummaryId);
                    $month = (new CommonController)->societyBillingFrequency($billFrequencyId,$billMonth);
                    $suplementary = ($data['bill_type'] == "sup")?" Suplementary":"";
                    
                    $billSummary = [
                        'date' => $data['bill_generated_date'],
                        'formattedDate'=> dateFormatMMDDYY($data['bill_generated_date']),
                        'particular' => 'To Bill NO '.$data['bill_no'].' For'.$month.' '.$suplementary,
                        'debit' => $data['monthly_bill_amount'],
                        'credit'=>'0.00',
                        'bill_id'=>$billSummaryId,
                        'month'=>$month,
                        'flag'=>'bill'                        
                        ];
                    
                        $totalDebit = $totalDebit +  $billSummary['debit'];
                        $totalCredit = $totalCredit + $billSummary['credit'];                        
                        
                        array_push($ledgerFinal,$billSummary);
                }
                
                if(!empty($paymentId) && !in_array($paymentId,$paymentIdArr)){
                    array_push($paymentIdArr,$paymentId);
                    $chq='';
                    $billType = $data['bill_type'] == 'sup' ? ' - supplementary' :  '';
                    if ($data['cheque_reference_number'] != ''){
                        $chq .= ' and Cheque Reference Number '. $data['cheque_reference_number'];
                    }                    
                    
                    $payments = array(
                    'date'=>$data['payment_date'],
                    'formattedDate'=> dateFormatMMDDYY($data['payment_date']),
                    'particular'=>'By Receipt V.No.'. $data['receipt_id'].$chq.' '.$billType,
                    'debit'=>'0.00',
                    'credit'=> $data['amount_paid'],
                    'flag'=>'receipt',
                    'payment_id' => $paymentId
                    );  
                    
                    $totalDebit = $totalDebit +  $payments['debit'];
                    $totalCredit = $totalCredit + $payments['credit'];                       
                    
                    array_push($ledgerFinal,$payments);
                }
                
                if(!empty($chequeRetId) &&  !in_array($chequeRetId,$chequeRetIdArr)){
                    $billType = $data['bill_type'] == 'sup' ? ' - supplementary' :  '';
                    array_push($chequeRetIdArr,$chequeRetId);
                    $chequeRetData = array(
                    'date'=> $data['cheque_return_date'],
                    'formattedDate'=> dateFormatMMDDYY($data['cheque_return_date']),
                    'particular'=> 'Ret'.' By Receipt V.No.'. $data['receipt_id'].' Cheque Reference Number '. $data['cheque_no'].' '.$billType ,
                    'debit'=> $data['cheque_amount'],
                    'credit'=> 0.00,
                    'flag'=>''
                    );    
                    $indiVisualSum = $indiVisualSum+$chequeRetData['debit']-$chequeRetData['credit'];
                    $chequeRetData['total'] = (new CommonController)->convertToDrCr($indiVisualSum); 
                    
                    $totalDebit = $totalDebit +  $chequeRetData['debit'];
                    $totalCredit = $totalCredit + $chequeRetData['credit'];                       
                    array_push($ledgerFinal,$chequeRetData);
                }
                
                if(!empty($jvCredId) && !in_array($jvCredId,$jvCredIdArr)){
                    array_push($jvCredIdArr,$jvCredId);
                        $jvCred = array(
                        'date'=> dateFormatMMDDYY($data['jv_credit_voucher_date']),
                        'formattedDate'=> $data['jv_credit_voucher_date'],
                        'particular'=>"JV Credited. V.No ".$data['jv_credit_voucher_no']." ".$data['jv_credit_note'],
                        'debit'=>'0.00',
                        'credit'=> $data['jv_credit_amount'],
                        'flag'=>'jv'    
                         );  

                    $totalDebit = $totalDebit +  $jvCred['debit'];
                    $totalCredit = $totalCredit + $jvCred['credit'];                                           
                    
                    array_push($ledgerFinal,$jvCred);
                }
                if(!empty($jvDebitId) && !in_array($jvDebitId,$jvDebitIdArr)){
                    array_push($jvDebitIdArr,$jvDebitId);
                    $jvDebit = array(
                    'date'=>    $data['jv_debit_voucher_date'],
                    'formattedDate'=> dateFormatMMDDYY($data['jv_debit_voucher_date']),
                    'particular'=>"JV Credited. V.No ".$data['jv_debit_voucher_no']." ".$data['jv_debit_note'],
                    'debit'=> $data['jv_debit_amount'],
                    'credit'=>'0.00',
                    'flag'=>'jv'
                    );  
                    
                    $totalDebit = $totalDebit +  $jvDebit['debit'];
                    $totalCredit = $totalCredit + $jvDebit['credit'];                                                               
                    
                    array_push($ledgerFinal,$jvDebit);
                }
            }
        }
        
        $type = $request->input('type');
        usort($ledgerFinal,array((new CommonController),"sortMultiDimentionalPaymentsArray"));
        $this->setLedgerIndivisulaTotal($ledgerFinal);
        $totalBal = $totalDebit - $totalCredit;
        $response['ledger_data'] = $ledgerFinal;
        $response['member_data'] = $openingData;
        $response['total_debit'] = $totalDebit;
        $response['total_credit'] = $totalCredit;
        $response['total_balance'] = (new CommonController)->convertToDrCr($totalBal);
        
        $fileData = (new PdfController)->generateLedgerPdf($response);
        if(empty($fileData))
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'Failed to generate PDF',
                'data'    => null
            ], 500);

            
        // if($type == 'email'){
        //     if(!empty($emailId)){
        //         $fileName = $fileData['file_path'];
        //         $sendStatus = $this->sendEmail($emailId,$fileName,'Ledger');
        //         if($sendStatus){
        //             return response_json(SUCCESS,"Email Sent To $emailId",$fileData);
        //         }
        //         else{
        //             return response_json(SUCCESS,"Email Not Sent");
        //         }
        //     }
        //     else{
        //         return response_json(UNSUCCESS,'Email Id Not Registered'); 
        //     }
        // }

        return response()->json([
            'status'  => config('constants.SUCCESS'),
            'message' => 'Member Ledger Data Fetched Successfully',
            'data'    => $fileData
        ], 200);

                
    }
    
    public function sendEmail($emailId,$fileName,$subject){
        // $file =  PDF_LOCAL_PATH.$fileName;
        $sendEmail = Mail::raw("Dear Member \n Kindly find the file in attachment",function($msg) use($subject,$fileName,$emailId){
            $msg->to($emailId);
            $msg->subject($subject);
            $msg->attach($fileName);
        });
        return $sendEmail;
    }
    
    public function setLedgerIndivisulaTotal(&$ledgerFinal){
        $indiVisualSum = 0.00; 
        foreach($ledgerFinal as $key => $data){
            $indiVisualSum = $indiVisualSum+$data['debit']-$data['credit'];
            $ledgerFinal[$key]['total'] = (new CommonController)->convertToDrCr($indiVisualSum);
        }
    }
    
    public function getPaymentMethod(){
        return PaymentMode::where('app',1)->select('id','short_code')->get()->toArray();
    }
    
    public function memberPaymentReceiptUniqueNumber($societyId){
        $paymentReceiptNumber = 1;
        $billReceiptArr = Payment::where('society_id',$societyId)->selectRaw('MAX(receipt_id) as max_receipt_number')->whereBetween('payment_date',[$this->startDate,$this->endDate])->get()->toArray();
        if(isset($billReceiptArr[0]['max_receipt_number']) && $billReceiptArr[0]['max_receipt_number'] >= 0){
            $paymentReceiptNumber = $billReceiptArr[0]['max_receipt_number']+1;
        }
        return $paymentReceiptNumber;
    }    
    
    public function addMemberPayment(Request $request){
        
        $validation = Validator::make($request->all(),[
            'society_id' => 'required',
            'member_id' => 'required',
            'order_id' => 'required',
            'bill_type' => 'required',
            'bill_month' => 'required',
            ]);
        
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }          
        
        
        $societyId = $request->input('society_id');
        $billType = $request->input('bill_type');
        $memberId = $request->input('member_id');
        $orderId = $request->input('order_id');
        $paymentMode = $this->getPaymentMethod();
        
        $billType = ($billType == 'Regular') ? 'reg' :'sup';
        
        $transactionDetail  = $this->getOrderDetails($orderId);
        $authStatus = isset($transactionDetail['auth_status']) ? $transactionDetail['auth_status'] : '';
        $paymentType =  isset($transactionDetail['txn_process_type']) ? $transactionDetail['txn_process_type'] : '';
        
        if(empty($authStatus) || $authStatus != '0300' || empty($paymentType)){
            return response_json(UNSUCCESS,'Something went Wrong!');
        }
         
        $memberData = Member::select('member_transfer')->where('id',$memberId)->first();
        if(!empty($memberData)){
            $memberData = $memberData->toArray();    
        }
        
        $paymentId = 0;
        foreach($paymentMode as $mode){
            if($mode['short_code'] = $paymentType) {
                $paymentId =   $mode['id'];
                break;
            }
        }
        
        
        $memberTransferId = $memberData['member_transfer'];        
        $paymentDate = $transactionDetail['transaction_date'];
        
        $insertPayment = [
            'society_id' => $societyId,
            'receipt_id' => $this->memberPaymentReceiptUniqueNumber($societyId),
            'member_id' => $memberId,
            'member_transfer' => $memberTransferId, 
            'bill_generated_id' => 0,
            'bill_month' => 0,
            'amount_paid' => $transactionDetail['charge_amount'],
            'monthly_bill_amount'=> 0,
            'amount_payable' => 0,
            'principal_balance' => 0,
            'interest_balance' => 0,
            'balance_amount' => 0,
            'payment_mode' => $paymentId,
            'cheque_reference_number' => 0,
            'payment_date' => date('Y-m-d',strtotime($paymentDate)),
            'credited_date' => 0,
            'society_bank_id' => 0,
            'bank_slip_no' => 0,
            'member_bank_id' => 0,
            'member_bank_ifsc' => 0,
            'member_bank_branch' => 0,
            'entry_date' => 0,
            'bill_type' => $billType,
            'narration' => 0,
            'order_id' => $transactionDetail['orderid'],
            'transaction_id' => $transactionDetail['transactionid'], 
            'txn_process_type' => $transactionDetail['txn_process_type'],
            'cdate' => date('Y-m-d H:i:s'),
            'udate' => date('Y-m-d H:i:s')
            ];
            
            $saveData = Payment::create($insertPayment);
            if($saveData){
                $paymentInsertedId = $saveData->id;
                $settled = $this->memberPaymentSettlement($insertPayment,$paymentInsertedId);
                if($settled){
                    return response_json(SUCCESS,'Payment Settled Successfully');            
                }
                return response_json(UNSUCCESS,'Payment Not Settled');            
                
            }
        return response_json(UNSUCCESS,'Payment Not Saved');
    }
    
    public function createOrder(Request $request){
        
        $validation = Validator::make($request->all(),[
            'amount_paid' => 'required',
            'member_id' => 'required',
            'society_id' => 'required',
            'bill_type' => 'required'
            ]);
        
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }          
        try{                
            $paymentData = $request->input();
            $amountPaid = $paymentData['amount_paid'];
            $memberId = $paymentData['member_id'];
            $societyId = $paymentData['society_id'];        
            $billType = $paymentData['bill_type'];
            
            $orderId = substr($billType,0,3).'_'.$societyId.'_'.$memberId.'_'.date('YmdHis');
            
            $payload = [
                "mercid" => 'UATELTSOC',
                "orderid" => $orderId,
                "amount"=> $amountPaid,
                "order_date"=> str_replace('+00:00','+05:30',gmdate('c')),
                "currency"=> 356,
                "ru" =>  'http://www.easylogicstechnology.in/eltsocietyapp/transactionStatus',
                "additional_info" =>  [ 
                    "additional_info1" => 'NA',
                    "additional_info2" => 'NA'
                ],
                "itemcode" => 'DIRECT',    
                    "device" => [ 
                    "init_channel" => "internet",
                    "ip"=> '148.72.88.29',
                    "user_agent"=> "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0"
                    ]
                ];
                
            $createOrder = (new BillPGController)->callPGApi($payload);
            if(!empty($createOrder)){
                return response_json(SUCCESS,'',$createOrder);
            }
            else{
                return response_json(UNSUCCESS,'Something Went Wrong');
            }
        }
        catch(Exception $e){
            return response_json(UNSUCCESS,'Something Went Wrong ');
        }

    }
    

    public function memberPaymentSettlement($paymentData,$paymentId) {
        $memberId = $paymentData['member_id'];
        $societyId = $paymentData['society_id'];        
        $billType = $paymentData['bill_type'];
        $memberTransferId = $paymentData['member_transfer'];
        $principalPaid = $paymentData['amount_paid'];

        if(empty($memberTransferId)){
           $memberBillSummaryDetails =  MemberBillSummary::where('bill_type',$billType)->where('member_id',$memberId)->where('society_id',$societyId)->orderby('bill_no','asc')->whereBetween('bill_generated_date',[$this->startDate,$this->endDate])->get()->toArray();
        }
        else{
            $memberBillSummaryDetails =  MemberBillSummary::where('bill_type',$billType)->where('member_id',$memberId)->where('society_id',$societyId)->whereBetween('bill_generated_date',[$this->startDate,$this->endDate])->where('member_transfer',$memberTransferId)->orderby('bill_no','asc')->get()->toArray();
        }

        $memberBillSummaryData = array();
        $memberSettlementData = array();
        $updateStatus = false;

        if (!empty($memberBillSummaryDetails)){
            $billSummaryCounter = 0;
            $paidAmountDeduction = $principalPaid;
            $cntMemberBillSummaryDetails = count($memberBillSummaryDetails);
                foreach ($memberBillSummaryDetails as $billData) {
                    if (isset($memberBillSummaryDetails[$billSummaryCounter]['tax_balance']) && $memberBillSummaryDetails[$billSummaryCounter]['tax_balance'] > 0 && $paidAmountDeduction > 0) {
                        $tempPaidAmount = $paidAmountDeduction;
                        $paidAmountDeduction = $paidAmountDeduction - $memberBillSummaryDetails[$billSummaryCounter]['tax_balance'];
                        if ($paidAmountDeduction >= 0) {
                            $memberBillSummaryDetails[$billSummaryCounter]['tax_paid'] = $memberBillSummaryDetails[$billSummaryCounter]['tax_balance'];
                        } else {
                            $memberBillSummaryDetails[$billSummaryCounter]['tax_paid'] = $tempPaidAmount;
                        }

                        $memberBillSummaryDetails[$billSummaryCounter]['tax_balance'] = $memberBillSummaryDetails[$billSummaryCounter]['tax_balance'] - $memberBillSummaryDetails[$billSummaryCounter]['tax_paid'];
                        for ($t=($billSummaryCounter+1);$t<$cntMemberBillSummaryDetails;$t++){
                            $memberBillSummaryDetails[$t]['tax_balance'] = $memberBillSummaryDetails[$t]['tax_balance'] - $memberBillSummaryDetails[$billSummaryCounter]['tax_paid'];
                        }
                    }

                    if (isset($memberBillSummaryDetails[$billSummaryCounter]['interest_balance']) && $memberBillSummaryDetails[$billSummaryCounter]['interest_balance'] > 0 && $paidAmountDeduction > 0) {
                        $tempPaidAmount = $paidAmountDeduction;
                        $paidAmountDeduction = $paidAmountDeduction - $memberBillSummaryDetails[$billSummaryCounter]['interest_balance'];
                        if ($paidAmountDeduction >= 0) {
                            $memberBillSummaryDetails[$billSummaryCounter]['interest_paid'] = $memberBillSummaryDetails[$billSummaryCounter]['interest_balance'];
                        } else {
                            $memberBillSummaryDetails[$billSummaryCounter]['interest_paid'] = $tempPaidAmount;
                        }

                        $memberBillSummaryDetails[$billSummaryCounter]['interest_balance'] = $memberBillSummaryDetails[$billSummaryCounter]['interest_balance'] - $memberBillSummaryDetails[$billSummaryCounter]['interest_paid'];
                        for ($in=($billSummaryCounter+1);$in<$cntMemberBillSummaryDetails;$in++){
                            $memberBillSummaryDetails[$in]['interest_balance'] = $memberBillSummaryDetails[$in]['interest_balance'] - $memberBillSummaryDetails[$billSummaryCounter]['interest_paid'];
                        }
                    }

                    if (isset($memberBillSummaryDetails[$billSummaryCounter]['principal_balance']) && $paidAmountDeduction > 0) {
                        $tempPaidAmount = $paidAmountDeduction;
                        $paidAmountDeduction = $paidAmountDeduction - $memberBillSummaryDetails[$billSummaryCounter]['principal_balance'];
                        if ($paidAmountDeduction >= 0) {
                            $memberBillSummaryDetails[$billSummaryCounter]['principal_paid'] = $memberBillSummaryDetails[$billSummaryCounter]['principal_balance'];
                        } else {
                            $memberBillSummaryDetails[$billSummaryCounter]['principal_paid'] = $tempPaidAmount;
                        }

                        $memberBillSummaryDetails[$billSummaryCounter]['principal_balance'] = $memberBillSummaryDetails[$billSummaryCounter]['principal_balance'] - $memberBillSummaryDetails[$billSummaryCounter]['principal_paid'];
                        for ($p=($billSummaryCounter+1);$p<$cntMemberBillSummaryDetails;$p++){
                            $memberBillSummaryDetails[$p]['principal_balance'] = $memberBillSummaryDetails[$p]['principal_balance'] - $memberBillSummaryDetails[$billSummaryCounter]['principal_paid'];
                        }
                    }

                    if ($billSummaryCounter != ($cntMemberBillSummaryDetails-1)) 
                        $payableAmount = $memberBillSummaryDetails[$billSummaryCounter]['balance_amount'] = $memberBillSummaryDetails[$billSummaryCounter]['principal_balance'] + $memberBillSummaryDetails[$billSummaryCounter]['interest_balance'] + $memberBillSummaryDetails[$billSummaryCounter]['tax_balance'];
                    else{
                        $payableAmount = $memberBillSummaryDetails[$billSummaryCounter]['balance_amount'] = ($paidAmountDeduction > 0) ? (-$paidAmountDeduction) :$memberBillSummaryDetails[$billSummaryCounter]['principal_balance'] + $memberBillSummaryDetails[$billSummaryCounter]['interest_balance'] + $memberBillSummaryDetails[$billSummaryCounter]['tax_balance'];
                        $memberBillSummaryDetails[$billSummaryCounter]['principal_balance'] = $payableAmount;
                    }
                    
                    array_push($memberBillSummaryData,$memberBillSummaryDetails[$billSummaryCounter]);
                    $memberBillSettlement = array('member_id' => $memberBillSummaryDetails[$billSummaryCounter]['member_id'],'payment_id'=>$paymentId,'bill_summary_id'=>$memberBillSummaryDetails[$billSummaryCounter]['id'],'bill_no' =>$memberBillSummaryDetails[$billSummaryCounter]['bill_no'],'bill_type'=>$memberBillSummaryDetails[$billSummaryCounter]['bill_type'],'bill_month'=>$memberBillSummaryDetails[$billSummaryCounter]['month'],'principal_paid'=>$memberBillSummaryDetails[$billSummaryCounter]['principal_paid'],'tax_paid'=>$memberBillSummaryDetails[$billSummaryCounter]['tax_paid'],'interest_paid'=>$memberBillSummaryDetails[$billSummaryCounter]['interest_paid'],'payable_amount'=>$payableAmount,'cdate'=> date('Y-m-d H:i:s'),'udate'=>date('Y-m-d H:i:s'));
                    array_push($memberSettlementData,$memberBillSettlement);
                    $billSummaryCounter++;
                    if ($paidAmountDeduction <= 0){
                        break;
                    }
                }

                if(!empty($memberBillSummaryData)){
                    foreach($memberBillSummaryData as $summary){
                        $id = $summary['id'];
                        $summaryInsert = MemberBillSummary::where('id',$id)->update($summary);
                    }
                }
                
                // $summaryInsert = MemberBillSummary::insert($memberBillSummaryData);
                if($summaryInsert){
                    $settlmentInsert =  BillSettlement::insert($memberBillSettlement);
                    $updateStatus = true;
                }
        }
        return $updateStatus;
    }    
    
    public function transactionStatus(Request $request){
        $orderId = $request->has('orderid') ? $request->input('orderid') : '';
        $transactionResponse = $request->has('transaction_response') ? $request->input('transaction_response') : '';
        if(!empty($transactionResponse)){
            $decodedTransactionResponse = (new BillPGController)->decode($transactionResponse);
            $onlinePaymentsInsertArr = [
                 'surcharge' => $decodedTransactionResponse['surcharge'],
                 'payment_method_type' => $decodedTransactionResponse['payment_method_type'],
                 'orderid' => $decodedTransactionResponse['orderid'],
                 'transaction_error_type' => $decodedTransactionResponse['transaction_error_type'],
                 'discount' => $decodedTransactionResponse['discount'],
                 'transactionid' => $decodedTransactionResponse['transactionid'],
                 'txn_process_type' => $decodedTransactionResponse['txn_process_type'],
                 'bank_id' => $decodedTransactionResponse['bankid'],
                 'additional_info' => isset($decodedTransactionResponse['additional_info']) ? json_encode($decodedTransactionResponse['additional_info']) :'',
                 'itemcode' => $decodedTransactionResponse['itemcode'],
                 'transaction_error_code' => $decodedTransactionResponse['transaction_error_code'],
                 'currency' => $decodedTransactionResponse['currency'],
                 'auth_status' => $decodedTransactionResponse['auth_status'],
                 'transaction_error_description' => $decodedTransactionResponse['transaction_error_desc'],
                 'objectid' => $decodedTransactionResponse['objectid'],
                 'charge_amount' => $decodedTransactionResponse['charge_amount'],
                 'transaction_date' => $decodedTransactionResponse['transaction_date']
                ];
                
            OnlinePayments::insert($onlinePaymentsInsertArr);
        }
    }
    
    public function getAddedTransactionDetails(Request $request){
        $validation = Validator::make($request->all(),[
            'orderid' => 'required'
            ]);
            
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }
        
        $orderId = $request->input('orderid');
        $paymentData = $this->getOrderDetails($orderId);
        return response_json(SUCCESS,'',$paymentData); 
    }    
    
    public function getOrderDetails($orderId){
        $orderData = [];
        if(!empty($orderId)){
            $orderData = OnlinePayments::where('orderid',$orderId)->get()->first();
            if(!empty($orderData)){
                $orderData = $orderData->toArray();
            };
        }

        return $orderData;
    }

    public function getFlatDetails(Request $request)
    {
        // ✅ Validation
        $validation = Validator::make($request->all(), [
            'mobile_no' => 'required|digits_between:10,15',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status'  => config('constants.MISSINGPARAMETER'),
                'message' => implode(', ', $validation->errors()->all()),
                'data'    => null
            ], 422);
        }

        $mobileNo = $request->mobile_no;

        // ✅ Query
        $flatNos = DB::table('members')
            ->where('member_phone', $mobileNo)
            ->whereNotNull('user_id')
            ->value(DB::raw('GROUP_CONCAT(flat_no)'));

        // ❌ No data found
        if (empty($flatNos)) {
            return response()->json([
                'status'  => config('constants.UNSUCCESS'),
                'message' => 'No flat details found',
                'data'    => []
            ], 404);
        }

        // ✅ Success response
        return response()->json([
            'status'  => config('constants.SUCCESS'),
            'message' => 'Flat details fetched successfully',
            'data'    => [
                'mobile_no' => $mobileNo,
                'flat_no'  => explode(',', $flatNos)
            ]
        ], 200);
    }

}
