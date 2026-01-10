<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Token;

use DB;
use Validator;

class TokenManagerController extends Controller {
    
    
    public function getToken($mobileNo,$deviceId){
        $tokenData = [];
        $token = md5(rand(0,10000));
        $appToken = json_encode(['device' => e($deviceId),'mobile_no' => e($mobileNo),'token' => e($token) ]);
        
        $tokenData = [
            'mobile_no' => $mobileNo,
            'device_id' => $device,
            'app_token' => $appToken,
            'ip_address' => $ipAddress,
            'token' => $token,
            ];
            
        $insertTokenData = Token::create($tokenData);
        if($insertTokenData){
            $tokenData = [
                'token' => $token,
                'app_token' => $appToken
                ];
        }  
        
        return $tokenData;
        
    }
    
    public function generateToken(Request $request){
        
        $validation = Validator::make($request->all(),[
            'device_id' => 'required'
            ]);
            
        if($validation->fails()){
            return response_json(MISSINGPARAMETER,implode(',',$validation->errors()->all()));
        }
        
        $ipAddress = $request->ip();
        $device = $request->input('device_id');
        
        $token = md5(rand(0,10000));
        $appToken = json_encode(['device' => e($device),'token' => e($token) ]);
        
        $tokenData = [
            'device_id' => $device,
            'app_token' => $appToken,
            'ip_address' => $ipAddress,
            'token' => $token,
            ];
            
        $insertTokenData = Token::create($tokenData);
        if($insertTokenData){
            return response_json(SUCCESS,'Token Created Successfully',[
                'token' => $token,
                'app_token' => $appToken
                ]);
            
        }
        return response_json(UNSUCESS,'Unable To Cretate Token');
        
    }
    
    public function verfifyToken(){
        
    }
    
    
}
