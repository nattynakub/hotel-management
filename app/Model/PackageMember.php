<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PackageMember extends Model
{
    //
    protected $fillable = [
        'user_id', 'package_id', 'package_member', 'package_start_time', 'package_end_time', 'package_member_status'
    ];
}
