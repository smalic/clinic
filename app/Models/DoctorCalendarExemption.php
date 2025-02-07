<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorCalendarExemption extends Model
{
    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'removed'
    ];
}
