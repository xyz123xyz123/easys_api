<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\SendSmsTrait;

use DB;
use Validator;

use App\Models\Member;
use App\Models\Otp;
use App\Models\LogAppLogin;


class CommonController extends Controller {
        use SendSmsTrait;
    public function memberMobileNoExist($mobileNo){
        return  Member::where('member_phone',$mobileNo)->where('status',1)->first();
    }
        
    public function sendOtp($mobileNo){
        $sendStatus = false;
        $otp = mt_rand(1000,9999);
        // $otp = 1234;  // message format do not touch it new line after easy logic techonolgy
        $message = "$otp is your OTP to verify mobile number on society app. Valid for 10 minutes only. Do not share - EASY LOGICS TECHNOLOGY
+PD7tIz3pES";
        $sendSms = $this->sendSms($mobileNo,$message);
       $decodedSmsResponse = json_decode($sendSms,1);
       if($decodedSmsResponse['status'] != 'success')  return $sendStatus;        
            $otpData = [
                'mobile_no' => $mobileNo,
                'otp' => $otp
                ];
      
        return  Otp::create($otpData);
    }
    
    public function logUserLogIn($mobileNo){
        $data = [
            'mobile_no' => $mobileNo
            ];
        LogAppLogin::create($data);
        
    }
    
    public function societyBillingFrequency($billFrequencyType = null,$monthNo = null){
        //1-monthly 6-yearly 3-quarterly 5- half yearly
        $billingFrequenciesArray = array(
            1=>array("4" => "April","5" => "May", "6" => "June", "7" => "July", "8" => "August","9" => "September", "10" => "October", "11" => "November", "12" => "December","1" => "January", "2" => "February", "3" => "March"),

            2=>array("4" => "Apr-May","6" => "Jun-Jul", "8" => "Aug-Sep", "10" => "Oct-Nov", "12" => "Dec-Jan","2" => "Feb-Mar"),

            3=>array("4" => "Apr-May-Jun","7" => "Jul-Aug-Sep", "10" => "Oct-Nov-Dec", "1" => "Jan-Feb-Mar"),

            4=>array(),

            5=>array("4" => "Apr-Sep","10" => "Oct-Mar"),

            6=>array("4" => "April-March"));

        if (isset($billingFrequenciesArray[$billFrequencyType])) {
            return $billingFrequenciesArray[$billFrequencyType][$monthNo];

        }

        return '';

    }    
    
    public function sortMultiDimentionalPaymentsArray($a,$b){
        return (strtotime($a["date"]) <= strtotime($b["date"])) ? -1 : 1;
    }    

    
    public function convertToDrCr($amount){
        return ($amount < 0) ? abs($amount).' Cr' : abs($amount).' Dr';
    }    
    
    public function sendFileInResponse($filePath,$fileName){
        $filePath = storage_path().'/'.$filePath; 
        return response()->download($filePath,$fileName,['Content-Type' => 'application/pdf','Content-Disposition'=> "inline;fileName = \{$fileName}\""]);
    }    
    
}

