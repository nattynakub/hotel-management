<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    //
    protected $fillable = [
        'message', 'user_id', 'pointable_id', 'pointable_type', 'amount', 'current', 'expire_date', 'flags', 'status'
    ];
}
