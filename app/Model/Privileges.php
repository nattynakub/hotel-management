<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Privileges extends Model
{
    //
    protected $fillable = [
        'partner_id', 'name', 'description', 'agreement', 'privileges_type', 'price', 'code', 'start_date', 'end_date', 'status'
    ];
}
