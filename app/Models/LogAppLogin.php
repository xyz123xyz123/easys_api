<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAppLogin extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'log_app_user_login';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile_no'
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
