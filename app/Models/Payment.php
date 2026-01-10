<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_payments';
    public  $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'id',
            'society_id',
            'receipt_id',
            'member_id',
            'member_transfer',
            'bill_generated_id',
            'bill_month',
            'amount_paid',
            'monthly_bill_amount',
            'amount_payable',
            'principal_balance',
            'interest_balance',
            'balance_amount',
            'payment_mode',
            'cheque_reference_number',
            'payment_date',
            'credited_date',
            'society_bank_id',
            'bank_slip_no',
            'member_bank_id',
            'member_bank_ifsc',
            'member_bank_branch',
            'entry_date',
            'bill_type',
            'narration',
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
