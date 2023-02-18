<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTimeRecord extends Model
{
    protected $table = 'daily_time_records';
    protected $fillable = [
        'date',
        'am_start_time',
        'am_end_time',
        'pm_start_time',
        'pm_end_time',
        'description',
        'overtime',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'portal_id', 'id');
    }

    public function detailedReport()
    {
        return $this->belongsTo('App\Models\DetailedReport', 'id', 'daily_time_record_id');
    }
}
