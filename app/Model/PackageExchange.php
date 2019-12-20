<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PackageExchange extends Model
{
    //
    protected $fillable = [
        'exchange_code', 'owner_id', 'owner_pmd_id', 'receiver_id', 'receiver_pmd_id', 'type', 'post_date', 'exchange_date', 'confirm_date', 'remark'
    ];
}
