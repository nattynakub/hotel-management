<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AppToken extends Model
{
    //
    protected $fillable=['user_id', 'app_id', 'token', 'platform'];
}
