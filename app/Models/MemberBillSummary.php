<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberBillSummary extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_bill_summaries';
    public  $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                'bill_type',
                'month',
                'member_id',
                'member_transfer',
                'society_id',
                'flat_no',
                'monthly_amount',
                'interest_free_amount',
                'op_principal_arrears_original',
                'jv_adjustment',
                'op_principal_arrears',
                'op_interest_arrears',
                'op_due_amount',
                'igst_total',
                'cgst_total',
                'sgst_total',
                'tax_total',
                'penalty',
                'op_tax_arrears',
                'monthly_principal_amount',
                'interest_on_due_amount',
                'discount',
                'monthly_bill_amount',
                'amount_payable',
                'principal_paid',
                'interest_paid',
                'tax_paid',
                'principal_adjusted',
                'interest_adjusted',
                'tax_adjusted',
                'principal_balance',
                'interest_balance',
                'tax_balance',
                'balance_amount',
                'bill_tariff_type',
                'bill_generated_date',
                'bill_due_date',
                'bill_end_date',
                'remarks',
                'bill_frequency_id',
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
