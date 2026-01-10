<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Token extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'session_token';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile_no',
        'device_id',
        'ip_address',
        'token',
        'app_token',
        'created_at',
        'updated_at'
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
