<?php

namespace App\Http\Traits;

trait ConvertAmtToDrCrTrait{
    public function convertToDrCr($amount){
    return ($amount < 0) ? abs($amount).' Cr' : abs($amount).' Dr';
    }    
}