<?php

namespace App\Http\Traits;


trait SetFinancialYearDataTrait{
     public function setFinancialYearData($yearId = ''){
        $financialYearData = [];
        $startDate = '2025-04-01';
        $endDate = '2026-03-31';
        return [
            'year-start-date' => $startDate,
            'year-end-date' => $endDate
            ];
    }        
}