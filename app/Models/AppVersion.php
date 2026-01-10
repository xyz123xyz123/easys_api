<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_version_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
