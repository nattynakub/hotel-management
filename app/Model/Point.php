<?php

namespace App\Model;

use Trexology\Pointable\Contracts\Pointable;
use Trexology\Pointable\Traits\Pointable as PointableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Point extends Model implements Pointable
{
    use PointableTrait;
    use SoftDeletes;
}
