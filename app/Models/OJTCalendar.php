<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OJTCalendar extends Model
{
    use HasFactory;

    protected $table = 'ojt_calendars';

    protected $fillable = [
        'title',
        'date',
        'note',
        'is_working_day',
        'supervisor_id'
    ];
}
