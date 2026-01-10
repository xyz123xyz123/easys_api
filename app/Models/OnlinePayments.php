<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlinePayments extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'online_payment_details';
    public  $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'id', 'surcharge', 'payment_method_type', 'orderid', 'transaction_error_type', 'discount', 'transactionid', 'txn_process_type', 
            'bank_id', 'additional_info', 'itemcode', 'transaction_error_code', 'currency', 'auth_status', 'transaction_error_description', 
            'objectid', 'charge_amount', 'transaction_date', 'created_date'
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
