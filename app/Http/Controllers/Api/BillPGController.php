<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class BillPGController extends Controller {
    
    protected $clientId = 'uateltsoc';
    protected $alg = 'HS256';
    protected $key = '4uh6psX7axPRcWFCZ4aS1ArkOkKiQmNe';
    protected $createorderurl = 'https://pguat.billdesk.io/payments/ve1_2/orders/create';
    public function urlSafeEncode($data)
        {
            if (\is_array($data)) {
                $data = \json_encode($data, \JSON_UNESCAPED_SLASHES);
            }
    
            return \rtrim(\strtr(\base64_encode($data), '+/', '-_'), '=');
        }
        
    public function encode(array $payload, array $header = [])
        {
            $jwtHeader = ['clientid' => $this->clientId, 'alg' => $this->alg];
            $header    = $this->urlSafeEncode($jwtHeader);
            $payload   = $this->urlSafeEncode($payload);
            $signature = $this->urlSafeEncode($this->sign($header . '.' . $payload));
    
            return $header . '.' . $payload . '.' . $signature;
        }  
        
        function urlSafeDecode($data)
        {
            $data = \json_decode(\base64_decode(\strtr($data, '-_', '+/')));
            return $data;
        }    
        
        
     public function decode($token)
        {
            $payload = [];
            if (\substr_count($token, '.') == 2) {
                $token = \explode('.', $token, 3);
                // Validate signature.
                if (verify($token[0] . '.' . $token[1], $token[2])) {
                    $payload = (array) $this->urlSafeDecode($token[1]);                    
                }
            }
            return $payload;
        }    
        
    public  function verify($input, $signature)
        {
            $algo = JWTALGO;
            // HMAC SHA.
            return \hash_equals($this->urlSafeEncode(\hash_hmac($algo, $input, JWTKEY, true)), $signature);
        }
        
        
    public function sign($input)
        {
            // HMAC SHA.
            return \hash_hmac('sha256', $input, JWTKEY, true);
        }    
        
    public function callPGApi($payload) {

        $reqHeaders = [
            'content-type:application/jose',
            'bd-timestamp:  '.date('YmdHis'),
            'accept:application/jose',
            'bd-traceid:  '.date('YmdHis').'ABD1K'    
        ];
        
// echo "header:".json_encode($reqHeaders);
        $jwt = $this->encode($payload);
        $url = $this->createorderurl;
// echo $jwt;
        
        $ch_session = curl_init();
        curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_session, CURLOPT_POST, 1);
        curl_setopt($ch_session, CURLOPT_HTTPHEADER, $reqHeaders);
        curl_setopt($ch_session, CURLOPT_URL, $url);
        curl_setopt( $ch_session, CURLOPT_POSTFIELDS,$jwt);
        $result_url = curl_exec($ch_session);
        return $this->decode($result_url);
    }        

}

