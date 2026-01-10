<?php

namespace App\Http\Traits;
trait SortMultidimesionalArrayTrait{
    
    public function sortMultiDimentionalPaymentsArray($a,$b){
        return (strtotime($a["date"]) <= strtotime($b["date"])) ? -1 : 1;
    }        
}
