<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Otp;
use App\Models\AppVersion;
use Exception;
use DB;
use Validator;
use Config;
class WebServices extends Controller {
    public function __construct(){
        $this->middleware('decrypt_request');
        $this->middleware('auth_token',['except' => [
            'postGenerateToken'
            ]]);
    }
    

    
    public function postGetCurrentBill(Request $request){
        try{
        return (new MemberController)->getCurrentBill($request);
        }
        catch(Exception $ex){
            return response_json(UNSUCCESS,'Something went wrong ');
        }
    }
    
    public function postGetMyBillSummary(Request $request){
        try {
        return (new MemberController)->getMyBillSummary($request);
        }
        catch(Exception $ex){
            return response_json(UNSUCCESS,'Something went wrong '.$ex->getMessage().' '.$ex->getLine());
        }
    }
    
    public function postGetDetailedBill(Request $request){
        try{
            return (new MemberController)->getDetailedBill($request);
        }
        catch(Exception $ex){
             return response_json(UNSUCCESS,'Something went wrong ');
        }
        
    }
    
    public function postGetMemberLedger(Request $request){
        try
        {
        return (new MemberController)->getMemberLedgerDetails($request);
        }
        catch(Exception $ex){
            return response_json(UNSUCCESS,'Something went wrong '.$ex->getMessage().' '.$ex->getLine());
        }
    }    
    
    public function postPaymentSummary(Request $request){
        try
        {
        return (new MemberController)->getPaymentSummary($request);
        }
        catch(Exception $ex){
            return response_json(UNSUCCESS,'Something went wrong'.$ex->getMessage());
        }
    }    
    
    public function postPaymentDetail(Request $request){
         try
        {
            return (new MemberController)->memberPaymentInDetail($request);
        }
        catch(Exception $ex){
            return response_json(UNSUCCESS,'Something went wrong'.$ex->getMessage());
        }
        
    }        
    
    
    public function postGenerateToken(Request $request){
        try
        {
        return (new TokenManagerController)->generateToken($request); 
        }
        catch(Exception $ex){
            return response_json(UNSUCCESS,'Something went wrong ');
        }
        
    }
    
    public function postLogin(Request $request){
        return (new LoginController)->authenticate($request);
    }
    
    public function postPaymentSettlement(Request $request){
        
    }
    

    

    
    public function postSendOtp(Request $request){
        $validation = Validator::make($request->all(),[
            'mobile_no' => 'required',
            ]);
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }
        
        $mobileNo = $request->input('mobile_no');
        $mobileNoExist = (new CommonController)->memberMobileNoExist($mobileNo);
        if(!empty($mobileNoExist)){
            $otp = Otp::where('mobile_no',$mobileNo)->where(DB::raw('cast(created_at as date)'),'=',date('Y-m-d'))->get()->toArray();
            if(!empty($otp)){
                $otpId= $otp[0]['id'];
                $otpSendStatus = (new CommonController)->sendOtp($mobileNo);
                   
                Otp::where('id',$otpId)->delete();
            }
            else{
                $otpSendStatus = (new CommonController)->sendOtp($mobileNo);
            }
           
            if($otpSendStatus){
                return response_json(SUCCESS,'OTP Sent Successfully');  
            }
            
            return response_json(UNSUCCESS,'Failed To Send OTP'); 
        }            
        return response_json(UNSUCCESS,'Mobile No. Does Not Exist'); 
    }
    
    
    public function postVerifyOtp(Request $request){
        $validation = Validator::make($request->all(),[
            'mobile_no' => 'required',
            'otp' => 'required'
            ]);
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }
        
        $otp = $request->input('otp');
        $mobileNo = $request->input('mobile_no');
        
        $otp = Otp::where('mobile_no',$mobileNo)->where('otp',$otp)->where(DB::raw('cast(created_at as date)'),'=',date('Y-m-d'))->get()->toArray();
        if(empty($otp)){
            return response_json(UNSUCCESS,'OTP Does Not Match');  
        }
       
        $otpId= $otp[0]['id'];
        Otp::where('id',$otpId)->delete();
        return response_json(SUCCESS,'Otp Verified Successfully');        
        
    }    
    
    public function postForgotPassword(Request $request){
        return (new LoginController)->forgotPassword($request);
    }
      
    public function postForceUpdate(Request $request){
        $validation = Validator::make($request->all(),[
            'platform' => 'required',
            'version' => 'required'
            ]);
            
        $platform = $request->input('platform');
        $version = $request->input('version');
        
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }
        
        $appVersion = AppVersion::where('platform',$platform)->where('version',$version)->first();
        if(is_null($appVersion)){
            return response_json(UNSUCCESS,'App Version Is Not UpToDate Kindly Update From PlayStore');
        }
                
        return response_json(SUCCESS,'You Are Using Latest Version');
    }
    
    public function postCreateOrder(Request $request){
        return  (new MemberController)->createOrder($request);
    }    
    
    public function postAddMemberPayment(Request $request){
        return  (new MemberController)->addMemberPayment($request);
        
    }
    
    public function postTransactionStatus(Request $request){
        return (new MemberController)->getAddedTransactionDetails($request);
    }
    
}


