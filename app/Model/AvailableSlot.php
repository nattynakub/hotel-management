<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AvailableSlot extends Model
{
    //
    protected $fillable = [
        'slot_name', 'slot_code', 'slot_start_date', 'slot_end_date', 'slot_year', 'slot_week', 'slot_peak', 'slot_room', 'slot_room_remain', 'room_type_id', 'slot_status'
    ];
}
