<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PackageMaster extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_code', 'package_name', 'package_type', 'package_benefit', 'package_discount_percentage', 'package_discount', 'package_price', 'package_currency', 'package_description', 'package_agreement', 'package_image', 'package_status'
    ];
}
