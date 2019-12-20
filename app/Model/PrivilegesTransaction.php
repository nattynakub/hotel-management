<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PrivilegesTransaction extends Model
{
    //
    protected $fillable = [
        'user_id', 'privileges_id', 'date'
    ];
}
