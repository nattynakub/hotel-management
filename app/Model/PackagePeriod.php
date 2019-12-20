<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PackagePeriod extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'transfer_to', 'package_id', 'package_coupon', 'package_additional_coupon', 'package_period_start_time', 'package_period_end_time', 'status'
    ];
}
