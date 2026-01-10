<?php 

date_default_timezone_set('Asia/Kolkata'); 
function response_json($code = UNSUCCESS,$msg = '',$data=[]){
    $response = [
        'code' => $code,
        'message'=> $msg,
        'data' => $data
        ];
        
        $status = 200;
        
        return response()->json($response,$status);
}

function dateFormatMMDDYY($date){
    return !empty($date) ? date('d-m-Y',strtotime($date)) : '';
}

function getClientIp(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}


    function numberTowordsEnglish($number, $amountType = 'rupees') {
        $arr_ones = array("", "One", "Two", "Three", "Four", "Five", "Six","Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen","Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen","Nineteen");
        $arr_tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
        $arab = floor($number / 1000000000);  /* arab (giga) */
        $number -= $arab * 1000000000;
        $crores = floor($number / 10000000);  /* crore (giga) */
        $number -= $crores * 10000000;
        $lakhs = floor($number / 100000);  /* lakhs (giga) */
        $number -= $lakhs * 100000;
        $thousands = floor($number / 1000);  /* Thousands (kilo) */
        $number -= $thousands * 1000;
        $hundreds = floor($number / 100);   /* Hundreds (hecto) */
        $number -= $hundreds * 100;
        $tens = floor($number / 10);    /* Tens (deca) */
        $ones = $number % 10;      /* Ones */
        $res = "";
        
        if ($arab) {

            $res .= convertToWords($arab);

            $res .= ($arab > 10) ? " Arabs " : " Arab ";

        }


        if ($crores) {

            $res .= convertToWords($crores);

            $res .= ($crores > 10) ? " Crores " : " Crore ";

        }

        if ($lakhs) {
            
            $res .= convertToWords($lakhs);

            $res .= ($lakhs > 10) ? " Lakhs " : " Lakh ";

        }

        if ($thousands) {
            $res .=  convertToWords($thousands) . " Thousand ";

        }



        if ($hundreds) {

            $res .= convertToWords($hundreds) . " Hundred ";

        }
        


        if ($tens || $ones) {

            if (!empty($res)) {

                $res .= " and ";

            }

            if ($tens < 2) {

                $res .= $arr_ones[$tens * 10 + $ones];

            } else {

                $res .= $arr_tens[$tens];
                // echo $res;
                if ($ones) {
                    $res .= " " .$arr_ones[$ones];

                }

            }

        }



        if (empty($res)) {

            $res = "zero";

        }

        return strtoupper($res);

    }
    
 function convertToWords($num){

        $num = str_replace('-','',$num);

        error_reporting(0);
        $ones = array(
        1 => "one",
        2 => "two",
        3 => "three",
        4 => "four",
        5 => "five",
        6 => "six",
        7 => "seven",
        8 => "eight",
        9 => "nine",
        10 => "ten",
        11 => "eleven",
        12 => "twelve",
        13 => "thirteen",
        14 => "fourteen",
        15 => "fifteen",
        16 => "sixteen",
        17 => "seventeen",
        18 => "eighteen",
        19 => "nineteen"
        );
        $tens = array(
        1 => "ten",
        2 => "twenty",
        3 => "thirty",
        4 => "forty",
        5 => "fifty",
        6 => "sixty",
        7 => "seventy",
        8 => "eighty",
        9 => "ninety"
        );
        $hundreds = array(
        "hundred",
        "thousand",
        "million",
        "billion",
        "trillion",
        "quadrillion"
        ); //limit t quadrillion
        $num = number_format($num,2,".",",");
        $num_arr = explode(".",$num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",",$wholenum));
        krsort($whole_arr);
        $rettxt = "";
        foreach($whole_arr as $key => $i){
            if($i < 20){
            $rettxt .= $ones[$i];
            }elseif($i < 100){
            $i = firstLetterZero($i);
            $rettxt .= $tens[substr($i,0,1)];
            $rettxt .= " ".$ones[substr($i,1,1)];
            }else{
            $i = firstLetterZero($i);
            $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
            $rettxt .= " ".$tens[substr($i,1,1)];
            $rettxt .= " ".$ones[substr($i,2,1)];
            }
            if($key > 0){
            $rettxt .= " ".$hundreds[$key]." ";
            }
        }
        if($decnum > 0){
        $rettxt .= " and ";
        if($decnum < 20){
        $rettxt .= $ones[$decnum];
        }elseif($decnum < 100){
        $rettxt .= $tens[substr($decnum,0,1)];
        $rettxt .= " ".$ones[substr($decnum,1,1)];
        }
        $rettxt .= " Paisa ";
        }
        return $rettxt;
    }    
    
function firstLetterZero($number){
    $splitedNo = str_split($number);
    if($splitedNo[0] == 0){
        unset($splitedNo[0]);
    }
    $removedArray = array_values($splitedNo);
    $str = implode($removedArray);    
    return $str;
}

function urlSafeEncode($data)
    {
        if (\is_array($data)) {
            $data = \json_encode($data, \JSON_UNESCAPED_SLASHES);
        }

        return \rtrim(\strtr(\base64_encode($data), '+/', '-_'), '=');
    }
    
function encode(array $payload, array $header = [])
    {
        $jwtHeader = ['clientid' => 'uateltsoc', 'alg' => 'HS256'];
        $header    = urlSafeEncode($jwtHeader);
        $payload   = urlSafeEncode($payload);
        $signature = urlSafeEncode(sign($header . '.' . $payload));

        return $header . '.' . $payload . '.' . $signature;
    }  
    
    function urlSafeDecode($data)
    {
        $data = \json_decode(\base64_decode(\strtr($data, '-_', '+/')));
        return $data;
    }    
    
    
 function decode($token)
    {
        $payload = [];
        if (\substr_count($token, '.') == 2) {
            $token = \explode('.', $token, 3);
            // Validate signature.
            if (verify($token[0] . '.' . $token[1], $token[2])) {
                $payload = (array) urlSafeDecode($token[1]);                    
            }
        }
        return $payload;
    }    
    
 function verify($input, $signature)
    {
        $algo = JWTALGO;
        // HMAC SHA.
        return \hash_equals(urlSafeEncode(\hash_hmac($algo, $input, JWTKEY, true)), $signature);
    }
    
    
function sign($input)
    {
        // HMAC SHA.
        return \hash_hmac('sha256', $input, JWTKEY, true);
    }    

function jswHmac(){

$header = [
    'clientid' => 'uateltsoc', 
    'alg' => 'HS256'
    ];
    
$reqHeaders = [
    'content-type:application/jose',
    'bd-timestamp:  '.date('YmdHis'),
    'accept:application/jose',
    'bd-traceid:  '.date('YmdHis').'ABD1K'    
    ];
    
$payload = [
    "mercid" => 'UATELTSOC',
    "orderid" => 'eltordercrop45608988'.date('YmdHis'),
    "amount"=> '300.00',
    "order_date"=> str_replace('+00:00','+05:30',gmdate('c')),
    "currency"=> 356,
    "ru" =>  'https://merchant.com',
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
  echo json_encode($reqHeaders);
  echo json_encode($payload);
  $jwt = encode($payload,$header); 
  
  $url_name = "https://pguat.billdesk.io/payments/ve1_2/orders/create";
  $ch_session = curl_init();
  curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch_session, CURLOPT_POST, 1);
  curl_setopt($ch_session, CURLOPT_HTTPHEADER, $reqHeaders);
  curl_setopt($ch_session, CURLOPT_URL, $url_name);
  curl_setopt( $ch_session, CURLOPT_POSTFIELDS,$jwt);
  $result_url = curl_exec($ch_session);
    decode($result_url);
  return $jwt;
    
}

    
