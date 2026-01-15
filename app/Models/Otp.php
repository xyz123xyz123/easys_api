<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'otp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile_no',
        'otp',
        'otp_expires_at',
        'otp_email_status',
        'otp_sms_status',
        'is_verified',
        'created_at'
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
