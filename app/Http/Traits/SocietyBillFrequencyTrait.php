<?php

namespace App\Http\Traits;
trait SocietyBillFrequencyTrait{
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

        return array();
    }    
}