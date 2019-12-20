<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    //
    protected $fillable = [
        'name', 'description', 'partner_status', 'start_date', 'end_date'
    ];
}
