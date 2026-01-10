<?php

namespace App\Http\Traits;

use App\Models\Member;
use App\Models\Otp;
use App\Models\LogAppLogin;

trait SendOtpTrait{
    public function sendOtp($mobileNo){
    // $otp = mt_rand(1000,9999);
    $otp = 1234;
    $otpData = [
        'mobile_no' => $mobileNo,
        'otp' => $otp
        ];
    return  Otp::create($otpData);
    }
    
    public function send_sms($receipants,$message){
        $apiKey = urlencode('NjY1YTZkNjQ2YTY1NmU0ZjQ4NjM1MTQ1Mzc0MjcyNzg=');
    	$numbers = array($receipants);
    	$sender = urlencode('ESYLT');
    	$message = rawurlencode($message);
    	$numbers = implode(',', $numbers);
     
    	// Prepare data for POST request
    	$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
     
    	// Send the POST request with cURL
    	$ch = curl_init('https://api.textlocal.in/send/');
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$response = curl_exec($ch);
    	curl_close($ch);
     	  // echo "$receipants ".$response;exit;
    	// Process your response here
    	return  $response;        
            
    }    
}
