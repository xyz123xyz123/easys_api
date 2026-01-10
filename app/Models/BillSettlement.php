<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillSettlement extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_bill_settlements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'payment_id',
        'bill_summary_id',
        'bill_no',
        'bill_type',
        'bill_month',
        'principal_paid',
        'interest_paid',
        'tax_paid',
        'payable_amount',
        'cdate',
        'udate'
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
