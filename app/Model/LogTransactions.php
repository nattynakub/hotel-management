<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LogTransactions extends Model
{
    //
    protected $fillable = [
        'app_id', 'user_id', 'checkin_date', 'durations', 'total_guests', 'total_kids', 'total_rooms', 'floor_id', 'room_id', 'payment_type', 'total_price' 
    ];
}
