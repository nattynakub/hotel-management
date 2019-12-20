<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PackageMemberDetail extends Model
{
    //
    protected $fillable = [
        'package_member_id', 'coupon_id', 'slot_id', 'package_coupon', 'transfer', 'passport_id', 'nametitle', 'firstname', 'lastname', 'email', 'gender', 'kids', 'additional_price', 'status'
    ];
}
